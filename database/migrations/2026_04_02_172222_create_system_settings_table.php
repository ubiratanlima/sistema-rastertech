<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->string('label')->nullable();
            $table->string('type')->default('text'); // text, password, number, select
            $table->timestamps();
        });

        // Valores padrão
        $settings = [
            // SMTP
            ['key' => 'smtp_host',       'value' => '',        'group' => 'smtp', 'label' => 'Servidor SMTP',       'type' => 'text'],
            ['key' => 'smtp_port',       'value' => '587',     'group' => 'smtp', 'label' => 'Porta',               'type' => 'number'],
            ['key' => 'smtp_username',   'value' => '',        'group' => 'smtp', 'label' => 'Usuário SMTP',        'type' => 'text'],
            ['key' => 'smtp_password',   'value' => '',        'group' => 'smtp', 'label' => 'Senha SMTP',          'type' => 'password'],
            ['key' => 'smtp_encryption', 'value' => 'tls',     'group' => 'smtp', 'label' => 'Criptografia',        'type' => 'text'],
            ['key' => 'mail_from_address','value' => '',       'group' => 'smtp', 'label' => 'E-mail Remetente',    'type' => 'text'],
            ['key' => 'mail_from_name',  'value' => 'Rastertech', 'group' => 'smtp', 'label' => 'Nome Remetente',  'type' => 'text'],

            // SMS
            ['key' => 'sms_api_url',     'value' => '',        'group' => 'sms',  'label' => 'URL da API SMS',      'type' => 'text'],
            ['key' => 'sms_api_token',   'value' => '',        'group' => 'sms',  'label' => 'Token da API SMS',    'type' => 'password'],
            ['key' => 'sms_sender',      'value' => 'RASTERTECH', 'group' => 'sms', 'label' => 'Remetente SMS',    'type' => 'text'],

            // Geral
            ['key' => 'app_url',         'value' => '',        'group' => 'general', 'label' => 'URL do Sistema',   'type' => 'text'],
            ['key' => 'app_name',        'value' => 'Rastertech Fleet', 'group' => 'general', 'label' => 'Nome do Sistema', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->insert(array_merge($setting, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
