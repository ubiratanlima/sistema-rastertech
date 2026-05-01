<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            // Evolution GO
            [
                'key'   => 'evolution_go_api_url',
                'value' => '',
                'group' => 'evolution_go',
                'label' => 'URL do Servidor Evolution GO',
                'type'  => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'evolution_go_api_key',
                'value' => '',
                'group' => 'evolution_go',
                'label' => 'API Key (Global) Evolution GO',
                'type'  => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Evolution CRM
            [
                'key'   => 'evolution_crm_base_url',
                'value' => '',
                'group' => 'evolution_crm',
                'label' => 'Base URL Evolution CRM',
                'type'  => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key'   => 'evolution_crm_access_token',
                'value' => '',
                'group' => 'evolution_crm',
                'label' => 'API Access Token Evolution CRM',
                'type'  => 'password',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(['key' => $setting['key']], $setting);
        }
    }

    public function down(): void
    {
        DB::table('system_settings')->whereIn('key', [
            'evolution_go_api_url', 
            'evolution_go_api_key',
            'evolution_crm_base_url',
            'evolution_crm_access_token'
        ])->delete();
    }
};
