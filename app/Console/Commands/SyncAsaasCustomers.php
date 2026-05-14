<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AsaasService;
use App\Models\Customer;
use Illuminate\Support\Facades\Log;

class SyncAsaasCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asaas:sync-customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza a base de clientes do Asaas com o Rastertech e formata o Código de Segurança';

    protected $asaas;

    public function __construct(AsaasService $asaas)
    {
        parent::__construct();
        $this->asaas = $asaas;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronização inteligente e ajuste de códigos...');
        
        $offset = 0;
        $limit = 100;
        $totalSynced = 0;
        $totalUpdated = 0;
        $totalIgnored = 0;
        $totalGroupFixed = 0;

        do {
            $result = $this->asaas->listCustomers($offset, $limit);

            if (!$result || !isset($result['data'])) {
                $this->error('Falha ao obter dados do Asaas. Verifique a API Key.');
                return 1;
            }

            foreach ($result['data'] as $asaasData) {
                $document = preg_replace('/[^0-9]/', '', $asaasData['cpfCnpj'] ?? '');
                $asaasGroup = $asaasData['groupName'] ?? null;

                // 1. Regra de Grupo (RASTERTECH)
                if (empty($asaasGroup)) {
                    $this->warn("Cliente {$asaasData['name']} sem grupo. Atualizando no Asaas...");
                    $this->asaas->updateCustomer($asaasData['id'], ['groupName' => 'RASTERTECH']);
                    $asaasGroup = 'RASTERTECH';
                    $totalGroupFixed++;
                }

                if (strtoupper($asaasGroup) !== 'RASTERTECH') {
                    $totalIgnored++;
                    continue;
                }

                if (empty($document)) continue;

                // 2. Regra do Código de Segurança (Remover 'cus_')
                $securityCode = str_replace('cus_', '', $asaasData['id']);

                // 3. Importação / Atualização
                $customer = Customer::where('document', $document)
                    ->orWhere('asaas_id', $asaasData['id'])
                    ->first();

                $data = [
                    'asaas_id'       => $asaasData['id'],
                    'code'           => $securityCode, // <-- UNIFICAÇÃO AQUI
                    'origin'         => 'ASAAS',
                    'asaas_group'    => $asaasGroup,
                    'name'           => $asaasData['name'],
                    'company_name'   => $asaasData['company'] ?? null,
                    'document'       => $document,
                    'email'          => $asaasData['email'] ?? null,
                    'cell_phone'     => $asaasData['mobilePhone'] ?? null,
                    'landline_phone' => $asaasData['phone'] ?? null,
                    'zip_code'       => $asaasData['postalCode'] ?? null,
                    'street'         => $asaasData['address'] ?? null,
                    'number'         => $asaasData['addressNumber'] ?? null,
                    'neighborhood'   => $asaasData['province'] ?? null,
                    'city'           => $asaasData['city'] ?? null,
                    'state'          => $asaasData['state'] ?? null,
                ];

                if ($customer) {
                    $customer->update($data);
                    $totalUpdated++;
                } else {
                    Customer::create($data);
                    $totalSynced++;
                }
            }

            $offset += $limit;
            $hasMore = $result['hasMore'] ?? false;

        } while ($hasMore);

        $this->info("Sincronização concluída!");
        $this->info("Novos clientes: {$totalSynced}");
        $this->info("Códigos de segurança ajustados/atualizados: {$totalUpdated}");
        $this->info("Grupos corrigidos no Asaas: {$totalGroupFixed}");
        $this->info("Clientes ignorados: {$totalIgnored}");

        return 0;
    }
}
