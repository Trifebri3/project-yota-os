<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Program;
use App\Models\BudgetCeiling;
use App\Models\FinanceTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class FinanceTransactionEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_financial_transaction(): void
    {
        $admin = User::factory()->create(['role' => 'program-admin', 'status' => 'active']);

        $program = Program::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Mangrove Action Program',
            'slug' => 'mangrove-action-program',
            'description' => 'Mangrove program',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonth(),
        ]);

        $ceiling = BudgetCeiling::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'source' => 'Sponsor CSR A',
            'amount' => 50000000,
        ]);

        $transaction = FinanceTransaction::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'program_id' => $program->id,
            'budget_ceiling_id' => $ceiling->id,
            'title' => 'Pembelian Bibit Awal',
            'description' => 'Membeli 500 bibit',
            'amount' => 1500000,
            'type' => 'expense',
            'category' => 'operational',
            'created_by' => $admin->id,
            'status' => 'approved',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post("/program-admin/finance/transaction/update/{$transaction->id}", [
                'program_id' => $program->id,
                'budget_ceiling_id' => $ceiling->id,
                'title' => 'Pembelian Bibit Terupdate',
                'description' => 'Membeli 1000 bibit',
                'amount' => 3000000,
                'type' => 'expense',
                'category' => 'logistics',
            ]);

        $response->assertRedirect();

        // Verify transaction is updated in database
        $this->assertDatabaseHas('finance_transactions', [
            'id' => $transaction->id,
            'title' => 'Pembelian Bibit Terupdate',
            'description' => 'Membeli 1000 bibit',
            'amount' => 3000000,
            'category' => 'logistics',
        ]);
    }
}
