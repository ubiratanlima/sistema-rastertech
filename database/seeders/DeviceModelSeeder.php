<?php

namespace Database\Seeders;

use App\Models\DeviceModel;
use App\Models\DeviceCommand;
use Illuminate\Database\Seeder;

class DeviceModelSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            'J14 / J16 (Concox/Jimi)' => [
                'commands' => [
                    ['desc' => 'Configurar Servidor/Porta', 'cmd' => 'SERVER,0,{IP},{PORT},0#'],
                    ['desc' => 'Configurar APN Completa', 'cmd' => 'APN,{APN},{APN_USER},{APN_PASS}#'],
                    ['desc' => 'Definir Fuso Horário (GMT 0)', 'cmd' => 'GMT,W,0,0#'],
                    ['desc' => 'Ativar GPRS', 'cmd' => 'GPRSSET,1#'],
                    ['desc' => 'Verificar Parâmetros de Rede', 'cmd' => 'PARAM#'],
                    ['desc' => 'Verificar Status do sinal', 'cmd' => 'STATUS#'],
                    ['desc' => 'Solicitar Localização (Link Maps)', 'cmd' => 'WHERE#'],
                    ['desc' => 'Reiniciar Equipamento (Reboot)', 'cmd' => 'REBOOT#'],
                    ['desc' => 'Resetar de Fábrica', 'cmd' => 'FACTORY#'],
                    ['desc' => 'Verificar Versão Firmware', 'cmd' => 'VERSION#'],
                ]
            ],
            'TK303 (Coban original)' => [
                'commands' => [
                    ['desc' => 'Iniciar Protocolo (Begin)', 'cmd' => 'begin123456'],
                    ['desc' => 'Configurar IP e Porta Master', 'cmd' => 'adminip123456 {IP} {PORT}'],
                    ['desc' => 'Configurar Nome da APN', 'cmd' => 'apn123456 {APN}'],
                    ['desc' => 'Configurar Usuário da APN', 'cmd' => 'apnuser123456 {APN_USER}'],
                    ['desc' => 'Configurar Senha da APN', 'cmd' => 'apnpasswd123456 {APN_PASS}'],
                    ['desc' => 'Ativar Modo GPRS', 'cmd' => 'GPRS123456'],
                    ['desc' => 'Definir Tempo de Transmissão (30s)', 'cmd' => 'fix030s030n123456'],
                    ['desc' => 'Verificar Status e IMEI', 'cmd' => 'check123456'],
                    ['desc' => 'Reiniciar Equipamento', 'cmd' => 'reset123456'],
                ]
            ],
            'GT06 (Accurate/Generic)' => [
                'commands' => [
                    ['desc' => 'Ligar GPRS e Conectar', 'cmd' => 'GPRSON,1#'],
                    ['desc' => 'Configurar DNS/IP e Porta', 'cmd' => 'SERVER,0,{IP},{PORT},0#'],
                    ['desc' => 'Configurar APN', 'cmd' => 'APN,{APN}#'],
                    ['desc' => 'Verificar Status Atual', 'cmd' => 'STATUSI#'],
                    ['desc' => 'Reiniciar via Software', 'cmd' => 'REBOOT#'],
                    ['desc' => 'Consultar Localização GPS', 'cmd' => 'WHERE#'],
                ]
            ],
            'BWS E3+ / EE04 (E3 Tech)' => [
                'commands' => [
                    ['desc' => 'Configurar IP/Porta Primário', 'cmd' => 'IP {IP} {PORT}'],
                    ['desc' => 'Configurar APN Master', 'cmd' => 'APN {APN} {APN_USER} {APN_PASS}'],
                    ['desc' => 'Setar Tempo de Transmissão', 'cmd' => 'TIMER,30,60#'],
                    ['desc' => 'Desativar Sleep Mode', 'cmd' => 'SLEEP 0'],
                    ['desc' => 'Ativar GPRS Online', 'cmd' => 'GPRS 1'],
                    ['desc' => 'Verificar Parâmetros (Config)', 'cmd' => 'PARAM'],
                    ['desc' => 'Reiniciar Sistema', 'cmd' => 'RESET'],
                    ['desc' => 'Consultar Versão', 'cmd' => 'VERSION'],
                ]
            ]
        ];

        foreach ($models as $name => $data) {
            $model = DeviceModel::updateOrCreate(['name' => $name], ['manufacturer' => 'Multi-Brand Telemetry']);
            
            foreach ($data['commands'] as $c) {
                DeviceCommand::updateOrCreate([
                    'device_model_id' => $model->id,
                    'description' => $c['desc']
                ], [
                    'command_template' => $c['cmd']
                ]);
            }
        }
    }
}
