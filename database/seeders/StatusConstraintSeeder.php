<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StatusConstraintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 🛠️ 1. FORÇANDO A CONSTRAINT DE STATUS (PGSQL)
        DB::statement("ALTER TABLE gsm_cards DROP CONSTRAINT IF EXISTS gsm_cards_status_check");
        DB::statement("ALTER TABLE gsm_cards ADD CONSTRAINT gsm_cards_status_check CHECK (status IN ('active', 'inactive', 'canceled'))");

        // 🏢 2. ADICIONANDO COLUNA DE CLIENTE SE NÃO EXISTIR
        if (!Schema::hasColumn('gsm_cards', 'customer_id')) {
            DB::statement("ALTER TABLE gsm_cards ADD COLUMN customer_id INTEGER");
            DB::statement("ALTER TABLE gsm_cards ADD CONSTRAINT fk_gsm_cards_customer FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL");
        }
    }
}
