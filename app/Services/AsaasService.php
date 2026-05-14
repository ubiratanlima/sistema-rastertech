<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AsaasService
{
    protected $apiKey;
    protected $apiUrl;
    protected $environment;

    public function __construct()
    {
        $this->apiKey      = SystemSetting::get('asaas_api_key');
        $this->environment = SystemSetting::get('asaas_environment', 'sandbox');
        
        // Se a URL não estiver configurada, usa o padrão do ambiente
        $defaultUrl = ($this->environment === 'production') 
            ? 'https://api.asaas.com/v3' 
            : 'https://sandbox.asaas.com/api/v3';
            
        $this->apiUrl = SystemSetting::get('asaas_api_url', $defaultUrl);
    }

    /**
     * Retorna o cliente HTTP configurado para o Asaas.
     */
    protected function client()
    {
        if (empty($this->apiKey)) {
            Log::error('AsaasService: API Key não configurada.');
            throw new \Exception('Chave de API do Asaas não encontrada nas configurações do sistema.');
        }

        return Http::withHeaders([
            'access_token' => $this->apiKey,
            'Content-Type' => 'application/json',
        ]);
    }

    /**
     * Cria um novo cliente no Asaas.
     */
    public function createCustomer(array $data)
    {
        try {
            $response = $this->client()->post("{$this->apiUrl}/customers", $data);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('AsaasService @ createCustomer: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista os clientes do Asaas (com paginação opcional).
     */
    public function listCustomers($offset = 0, $limit = 100)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/customers", [
                'offset' => $offset,
                'limit'  => $limit
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('AsaasService @ listCustomers: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('AsaasService @ listCustomers Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca os detalhes de um pagamento (cobrança).
     */
    public function getPayment($paymentId)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/payments/{$paymentId}");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Atualiza dados de um cliente no Asaas.
     */
    public function updateCustomer($asaasId, array $data)
    {
        try {
            $response = $this->client()->post("{$this->apiUrl}/customers/{$asaasId}", $data);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AsaasService @ updateCustomer ({$asaasId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lista os pagamentos de um cliente específico.
     */
    public function listPayments($asaasId, $limit = 15, $offset = 0)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/payments", [
                'customer' => $asaasId,
                'limit'    => $limit,
                'offset'   => $offset,
            ]);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AsaasService @ listPayments ({$asaasId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém dados da Nota Fiscal de um pagamento.
     */
    public function getPaymentInvoice($paymentId)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/payments/{$paymentId}/invoices");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AsaasService @ getPaymentInvoice ({$paymentId}): " . $e->getMessage());
            return null;
        }
    }

    public function getInvoiceByPayment($paymentId)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/invoices", [
                'payment' => $paymentId
            ]);
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error("AsaasService @ getInvoiceByPayment ({$paymentId}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtém o QR Code e o Copia e Cola de um PIX.
     */
    public function getPixQrCode($paymentId)
    {
        try {
            $response = $this->client()->get("{$this->apiUrl}/payments/{$paymentId}/pixQrCode");
            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            return null;
        }
    }
}
