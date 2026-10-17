<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Program;
use App\Models\User;
use App\Services\PhotoUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryController extends Controller
{
    /**
     * Display inventory dashboard & list of assets for Program Admin.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Find programs authorized for this admin
        $programs = $this->getAuthorizedPrograms();
        $programIds = $programs->pluck('id')->toArray();

        // Get assets in authorized programs
        $assets = Inventory::with(['program', 'pic'])
            ->whereIn('program_id', $programIds)
            ->orderBy('created_at', 'desc')
            ->get();

        // Summary metrics
        $totalItems = $assets->sum('quantity');
        $activeItems = $assets->where('status_active', true)->sum('quantity');
        $damagedItems = $assets->where('status_damaged', true)->sum('quantity');
        $lostItems = $assets->where('status_lost', true)->sum('quantity');
        $borrowedItems = $assets->where('status_borrowed', true)->sum('quantity');

        // Total program value
        $totalValue = $assets->sum('total_value');

        // Data for charts
        // 1. Condition breakdown
        $conditions = [
            'Bagus / Layak' => $assets->where('current_condition', 'Bagus')->count() + $assets->where('current_condition', 'Baru')->count(),
            'Rusak Ringan' => $assets->where('current_condition', 'Rusak Ringan')->count(),
            'Rusak Berat' => $assets->where('current_condition', 'Rusak Berat')->count(),
            'Hilang' => $assets->where('status_lost', true)->count(),
        ];

        // 2. Asset by Location
        $locations = $assets->groupBy('location')->map(fn($group) => $group->count());

        // 3. Category distribution
        $categories = $assets->groupBy('category')->map(fn($group) => $group->count());

        // Fetch users to assign as PIC (Volunteer & Admin roles)
        $volunteers = User::whereIn('role', ['volunteer', 'peserta', 'participant', 'program-admin', 'project-officer', 'supervisor'])->get();

        return view('program-admin.inventory.index', compact(
            'assets', 'programs', 'volunteers', 'totalItems', 'activeItems', 
            'damagedItems', 'lostItems', 'borrowedItems', 'totalValue',
            'conditions', 'locations', 'categories'
        ));
    }

    /**
     * Create new asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'program_id' => 'required|uuid',
            'location' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'initial_condition' => 'required|string',
            'price_per_unit' => 'required|numeric|min:0',
            'pic_id' => 'required|uuid',
            'photo' => 'nullable', // universal uploader string or file
        ]);

        // Generate unique asset ID
        $assetId = 'AST-' . strtoupper(substr(uniqid(), 7, 5)) . '-' . rand(10, 99);

        // Upload photo documentation
        $photoPath = null;
        if ($request->photo) {
            $photoPath = PhotoUploadService::storeAndCompress(
                $request->photo,
                $request->program_id,
                null,
                null,
                $request->photo_lat,
                $request->photo_lng,
                $request->photo_device
            );
        }

        $totalValue = $request->price_per_unit * $request->quantity;

        $inventory = Inventory::create([
            'asset_id' => $assetId,
            'name' => $request->name,
            'category' => $request->category,
            'sub_category' => $request->sub_category,
            'program_id' => $request->program_id,
            'location' => $request->location,
            'quantity' => $request->quantity,
            'unit' => $request->unit,
            'initial_condition' => $request->initial_condition,
            'current_condition' => $request->initial_condition, // starts same
            'price_per_unit' => $request->price_per_unit,
            'total_value' => $totalValue,
            'purchase_date' => $request->purchase_date,
            'funding_source' => $request->funding_source,
            'supplier' => $request->supplier,
            'pic_id' => $request->pic_id,
            'status_active' => true,
            'description' => $request->description,
            'photo_path' => $photoPath,
        ]);

        // Create permanent audit log
        InventoryLog::create([
            'inventory_id' => $inventory->id,
            'user_id' => Auth::id(),
            'action' => 'create',
            'new_values' => $inventory->toArray(),
            'location' => $request->photo_lat && $request->photo_lng ? $request->photo_lat . ',' . $request->photo_lng : $request->location,
        ]);

        return redirect()->route('program-admin.inventory.index')->with('success', 'Aset baru "' . $inventory->name . '" berhasil ditambahkan dengan Asset ID: ' . $assetId);
    }

    /**
     * Show internal details dashboard for an asset.
     */
    public function show($id)
    {
        $asset = Inventory::with(['program', 'pic', 'logs.user'])->findOrFail($id);
        $volunteers = User::whereIn('role', ['volunteer', 'peserta', 'participant', 'program-admin', 'project-officer', 'supervisor'])->get();
        $programs = $this->getAuthorizedPrograms();

        return view('program-admin.inventory.show', compact('asset', 'volunteers', 'programs'));
    }

    /**
     * Update asset condition and status (Mode 1 action panel).
     */
    public function update(Request $request, $id)
    {
        $asset = Inventory::findOrFail($id);
        $oldValues = $asset->toArray();

        $request->validate([
            'current_condition' => 'required|string',
            'location' => 'required|string',
            'pic_id' => 'required|uuid',
            'quantity' => 'required|integer|min:0',
        ]);

        // Capture status changes
        $statusActive = $request->has('status_active');
        $statusDamaged = $request->has('status_damaged') || in_array($request->current_condition, ['Rusak Ringan', 'Rusak Berat']);
        $statusLost = $request->has('status_lost');
        $statusBorrowed = $request->has('status_borrowed');

        // Handle photo upload if present
        $photoPath = $asset->photo_path;
        if ($request->photo) {
            $photoPath = PhotoUploadService::storeAndCompress(
                $request->photo,
                $asset->program_id,
                null,
                null,
                $request->photo_lat,
                $request->photo_lng,
                $request->photo_device
            );
        }

        $totalValue = $asset->price_per_unit * $request->quantity;

        $asset->update([
            'current_condition' => $request->current_condition,
            'location' => $request->location,
            'pic_id' => $request->pic_id,
            'quantity' => $request->quantity,
            'total_value' => $totalValue,
            'status_active' => $statusActive,
            'status_damaged' => $statusDamaged,
            'status_lost' => $statusLost,
            'status_borrowed' => $statusBorrowed,
            'description' => $request->description,
            'photo_path' => $photoPath,
        ]);

        $newValues = $asset->toArray();

        // Create permanent audit log trail
        InventoryLog::create([
            'inventory_id' => $asset->id,
            'user_id' => Auth::id(),
            'action' => 'update',
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'location' => $request->photo_lat && $request->photo_lng ? $request->photo_lat . ',' . $request->photo_lng : $request->location,
        ]);

        return redirect()->route('program-admin.inventory.show', $asset->id)->with('success', 'Detail aset berhasil diperbarui dan dicatat permanen dalam log audit.');
    }

    /**
     * QR code scan entry point.
     * Decides: redirect admin to internal page, guest to public transparency page.
     */
    public function scan($idOrAssetId)
    {
        // Try finding by ID first, then by Asset ID
        $asset = Inventory::where('id', $idOrAssetId)
            ->orWhere('asset_id', $idOrAssetId)
            ->firstOrFail();

        $user = Auth::user();
        // Check Role: Admin Program, Project Officer, Supervisor
        if ($user && ($user->isSuperAdmin() || $user->isProgramAdmin() || $user->isProjectOfficer() || $user->isSupervisor())) {
            // MODE 1: Open Internal detail panel
            return redirect()->route('program-admin.inventory.show', $asset->id);
        }

        // MODE 2: Open Public transparency page
        return redirect()->route('inventory.public', $asset->id);
    }

    /**
     * Render the public asset page (mode 2 public transparency page).
     */
    public function publicShow($id)
    {
        $asset = Inventory::with(['program', 'logs.user'])->findOrFail($id);
        
        return view('inventory.public', compact('asset'));
    }

    /**
     * Helper to get authorized programs for admin.
     */
    private function getAuthorizedPrograms()
    {
        $user = Auth::user();
        if ($user->isSuperAdmin() || $user->isViewer()) {
            return Program::all();
        }
        return Program::where('project_officer_id', $user->id)
            ->orWhere('supervisor_id', $user->id)
            ->get();
    }
}
