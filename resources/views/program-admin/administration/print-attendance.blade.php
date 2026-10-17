<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi Kehadiran Lapangan</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 20px;
            font-size: 11px;
            line-height: 1.5;
        }
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 12px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .header h1 {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 4px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .header p {
            margin: 0;
            color: #64748b;
            font-size: 10px;
        }
        .meta-info {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #cbd5e1;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            color: #475569;
        }
        tr:nth-child(even) {
            background-color: #f8fafc/50;
        }
        .badge {
            font-size: 8px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .badge-success {
            background-color: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .badge-warning {
            background-color: #fffbeb;
            color: #92400e;
            border: 1px solid #fef3c7;
        }
        .filter-container {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: flex-end;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-weight: bold;
            color: #475569;
            font-size: 9px;
            text-transform: uppercase;
        }
        .filter-group input {
            padding: 6px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-family: inherit;
            font-size: 11px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            font-family: inherit;
            font-size: 11px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn:hover {
            background-color: #2E7D32;
        }
        .btn-print {
            background-color: #0f172a;
        }
        .btn-print:hover {
            background-color: #1e293b;
        }
        .footer-sigs {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .sig-box {
            width: 200px;
            text-align: center;
        }
        .sig-space {
            height: 60px;
        }
        @media print {
            .filter-container, .btn-container {
                display: none !important;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Filter & Buttons Container (Hidden during print) -->
    <div class="filter-container">
        <form method="GET" action="{{ route('program-admin.attendance.print') }}" style="display: flex; gap: 15px; align-items: flex-end;">
            <div class="filter-group">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="filter-group">
                <label>Tanggal Selesai</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <button type="submit" class="btn">Filter</button>
            <a href="{{ route('program-admin.attendance.print') }}" class="btn" style="background-color: #64748b; text-decoration: none; display: inline-block; padding: 7px 14px;">Reset</a>
        </form>
        <div style="margin-left: auto;">
            <button onclick="window.print()" class="btn btn-print">🖨️ Cetak Dokumen</button>
        </div>
    </div>

    <!-- Printable sheet header -->
    <div class="header">
        <div>
            <h1>Laporan Kehadiran Lapangan</h1>
            <p>Sistem Informasi Manajemen Dampak Sosial - Yota OS</p>
        </div>
        <div style="text-align: right;">
            <span style="font-size: 12px; font-weight: bold; color: #4CAF50;">YOTA OS</span>
        </div>
    </div>

    <div class="meta-info">
        <div>
            Tanggal Cetak: <strong>{{ date('d F Y') }}</strong><br>
            Waktu Cetak: <strong>{{ date('H:i') }} WIB</strong>
        </div>
        <div style="text-align: right;">
            Status Dokumen: <span class="badge badge-success">VALID SYSTEM SEAL</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Nama Relawan</th>
                <th style="width: 30%;">Program / Aktivitas</th>
                <th style="width: 12%;">Check In</th>
                <th style="width: 13%;">Check Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $att)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $att->created_at->format('d M Y') }}</td>
                    <td><strong>{{ $att->user?->name }}</strong></td>
                    <td>
                        {{ $att->activity->workPlan->program?->name }}<br>
                        <span style="color: #64748b; font-size: 10px;">{{ $att->activity->name }}</span>
                    </td>
                    <td>
                        {{ $att->check_in ? date('H:i', strtotime($att->check_in)) : '-' }}<br>
                        <span class="badge {{ $att->check_in_status === 'inside_zone' ? 'badge-success' : 'badge-warning' }}" style="font-size: 7px; padding: 1px 4px;">
                            {{ $att->check_in_status === 'inside_zone' ? 'Inside' : 'Outside' }}
                        </span>
                    </td>
                    <td>
                        {{ $att->check_out ? date('H:i', strtotime($att->check_out)) : '-' }}<br>
                        <span class="badge {{ $att->check_out_status === 'inside_zone' ? 'badge-success' : 'badge-warning' }}" style="font-size: 7px; padding: 1px 4px;">
                            {{ $att->check_out_status === 'inside_zone' ? 'Inside' : 'Outside' }}
                        </span>
                    </td>
                </tr>
            @endforeach
            @if($attendances->isEmpty())
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px; color: #64748b; font-style: italic;">Tidak ada catatan kehadiran yang cocok dengan parameter pencarian.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-sigs">
        <div class="sig-box">
            <p>Petugas Validasi Absensi,</p>
            <div class="sig-space"></div>
            <p><strong>Yota OS Validator System</strong></p>
            <p style="font-size: 8px; color: #94a3b8;">System Generated Verification Seal</p>
        </div>
        <div class="sig-box">
            <p>Mengesahkan,<br>Project Officer,</p>
            <div class="sig-space" style="display: flex; align-items: center; justify-content: center;">
                @php
                    $program = null;
                    if (isset($attendances) && $attendances->isNotEmpty()) {
                        $program = $attendances->first()->activity?->workPlan?->program ?? null;
                    }
                    
                    $signer = null;
                    if ($program) {
                        $signer = $program->projectOfficer ?? $program->supervisor ?? null;
                    }
                    if (!$signer) {
                        $signer = \Illuminate\Support\Facades\Auth::user();
                    }
                    if (!$signer) {
                        $signer = \App\Models\User::where('role', 'program-admin')->first();
                    }
                    
                    $roleTitle = 'Project Officer';
                    if ($signer) {
                        if ($signer->role === 'project-officer') {
                            $roleTitle = 'Project Officer';
                        } elseif ($signer->role === 'program-admin') {
                            $roleTitle = 'Direktur Program Yota Adiwidya Center';
                        } elseif ($signer->role === 'supervisor') {
                            $roleTitle = 'Supervisor';
                        }
                    }
                @endphp
                @if($signer && $signer->signature_path)
                    <img src="{{ $signer->signature_path ? (str_starts_with($signer->signature_path, 'http') ? $signer->signature_path : (str_starts_with($signer->signature_path, '/storage/') ? $signer->signature_path : (str_starts_with($signer->signature_path, 'storage/') ? '/' . $signer->signature_path : '/storage/' . ltrim($signer->signature_path, '/')))) : '' }}" style="max-height: 45px; object-fit: contain; mix-blend-mode: multiply;">
                @else
                    <div style="height: 45px; display: flex; align-items: center; justify-content: center; color: #cbd5e1; font-style: italic; font-size: 8px;">(Belum Upload TTD)</div>
                @endif
            </div>
            <p><strong>{{ $signer?->name }}</strong></p>
            <p style="font-size: 8px; color: #94a3b8;">{{ $roleTitle }}</p>
        </div>
    </div>

</body>
</html>
