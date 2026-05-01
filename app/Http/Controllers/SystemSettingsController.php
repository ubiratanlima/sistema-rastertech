<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Config;

class SystemSettingsController extends Controller
{
    public function index()
    {
        $smtp      = SystemSetting::getGroup('smtp');
        $sms       = SystemSetting::getGroup('sms');
        $asaas     = SystemSetting::getGroup('asaas');
        $spark     = SystemSetting::getGroup('spark');
        $evolution     = SystemSetting::getGroup('evolution');
        $evolution_go  = SystemSetting::getGroup('evolution_go');
        $evolution_crm = SystemSetting::getGroup('evolution_crm');
        $general       = SystemSetting::getGroup('general');

        return view('settings.index', compact(
            'smtp', 'sms', 'asaas', 'spark', 'evolution', 
            'evolution_go', 'evolution_crm', 'general'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            SystemSetting::set($key, $value);
        }

        // Aplicar configurações SMTP em tempo real
        $this->applySmtpConfig();

        return redirect()->back()->with('success', 'Configurações salvas com sucesso!');
    }

    /**
     * Aplica as configurações SMTP do banco no runtime do Laravel.
     */
    public static function applySmtpConfig(): void
    {
        Config::set('mail.mailers.smtp.host',       SystemSetting::get('smtp_host', env('MAIL_HOST')));
        Config::set('mail.mailers.smtp.port',       SystemSetting::get('smtp_port', env('MAIL_PORT', 587)));
        Config::set('mail.mailers.smtp.username',   SystemSetting::get('smtp_username', env('MAIL_USERNAME')));
        Config::set('mail.mailers.smtp.password',   SystemSetting::get('smtp_password', env('MAIL_PASSWORD')));
        Config::set('mail.mailers.smtp.encryption', SystemSetting::get('smtp_encryption', env('MAIL_ENCRYPTION', 'tls')));
        Config::set('mail.from.address',            SystemSetting::get('mail_from_address', env('MAIL_FROM_ADDRESS')));
        Config::set('mail.from.name',               SystemSetting::get('mail_from_name', env('MAIL_FROM_NAME', 'Rastertech')));
    }
}
