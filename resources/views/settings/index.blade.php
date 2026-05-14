@extends('layouts.app')

@section('title', 'Configurações do Sistema')

@section('content')
<div class="container-fluid px-4 py-3">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="font-weight-bold mb-0" style="color: #2d3748;">
                <i class="fas fa-cog mr-2" style="color: #20c997;"></i> Configurações do Sistema
            </h4>
            <p class="text-muted small mb-0">Gerencie as integrações e parâmetros globais do sistema.</p>
        </div>
    </div>



    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ABAS --}}
        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist" style="border-bottom: 2px solid #20c997;">
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" id="smtp-tab" data-toggle="tab" href="#smtp" role="tab">
                    <i class="fas fa-envelope mr-1"></i> E-mail (SMTP)
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="sms-tab" data-toggle="tab" href="#sms" role="tab">
                    <i class="fas fa-sms mr-1"></i> API SMS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="asaas-tab" data-toggle="tab" href="#asaas" role="tab">
                    <i class="fas fa-dollar-sign mr-1"></i> API ASAAS
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="spark-tab" data-toggle="tab" href="#spark" role="tab">
                    <i class="fas fa-bolt mr-1"></i> API SPARK
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="evolution-tab" data-toggle="tab" href="#evolution" role="tab">
                    <i class="fab fa-whatsapp mr-1"></i> Evolution API
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="evolution-go-tab" data-toggle="tab" href="#evolution-go" role="tab">
                    <i class="fas fa-rocket mr-1"></i> Evolution GO
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="evolution-crm-tab" data-toggle="tab" href="#evolution-crm" role="tab">
                    <i class="fas fa-users-cog mr-1"></i> Evolution CRM
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font-weight-bold" id="general-tab" data-toggle="tab" href="#general" role="tab">
                    <i class="fas fa-sliders-h mr-1"></i> Geral
                </a>
            </li>
        </ul>

        <div class="tab-content" id="settingsTabContent">

            {{-- ABA SMTP --}}
            <div class="tab-pane fade show active" id="smtp" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #20c997;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-server mr-1"></i> Configuração de E-mail (SMTP)
                        </h6>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-network-wired mr-1"></i> Servidor SMTP (Host)
                                </label>
                                <input type="text" name="smtp_host" class="form-control"
                                    value="{{ $smtp['smtp_host']->value ?? '' }}"
                                    placeholder="Ex: mail.rastertech.com.br">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-plug mr-1"></i> Porta
                                </label>
                                <input type="number" name="smtp_port" class="form-control"
                                    value="{{ $smtp['smtp_port']->value ?? '587' }}"
                                    placeholder="587">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-user mr-1"></i> Usuário SMTP
                                </label>
                                <input type="text" name="smtp_username" class="form-control"
                                    value="{{ $smtp['smtp_username']->value ?? '' }}"
                                    placeholder="noreply@rastertech.com.br">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> Senha SMTP
                                </label>
                                <div class="input-group">
                                    <input type="password" name="smtp_password" id="smtp_password" class="form-control"
                                        value="{{ $smtp['smtp_password']->value ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('smtp_password');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-shield-alt mr-1"></i> Criptografia
                                </label>
                                <select name="smtp_encryption" class="form-control">
                                    <option value="tls" {{ ($smtp['smtp_encryption']->value ?? 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($smtp['smtp_encryption']->value ?? '') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ ($smtp['smtp_encryption']->value ?? '') === '' ? 'selected' : '' }}>Nenhuma</option>
                                </select>
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-at mr-1"></i> E-mail Remetente
                                </label>
                                <input type="email" name="mail_from_address" class="form-control"
                                    value="{{ $smtp['mail_from_address']->value ?? '' }}"
                                    placeholder="noreply@rastertech.com.br">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-signature mr-1"></i> Nome Remetente
                                </label>
                                <input type="text" name="mail_from_name" class="form-control"
                                    value="{{ $smtp['mail_from_name']->value ?? 'Rastertech' }}"
                                    placeholder="Rastertech">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA SMS --}}
            <div class="tab-pane fade" id="sms" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #17a2b8;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-sms mr-1"></i> Configuração de API SMS
                        </h6>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> URL da API SMS
                                </label>
                                <input type="text" name="sms_api_url" class="form-control"
                                    value="{{ $sms['sms_api_url']->value ?? '' }}"
                                    placeholder="https://api.smsprovider.com/send">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-id-badge mr-1"></i> Remetente SMS
                                </label>
                                <input type="text" name="sms_sender" class="form-control"
                                    value="{{ $sms['sms_sender']->value ?? 'RASTERTECH' }}"
                                    placeholder="RASTERTECH">
                            </div>
                            <div class="col-12 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> Token da API SMS
                                </label>
                                <div class="input-group">
                                    <input type="password" name="sms_api_token" id="sms_token" class="form-control"
                                        value="{{ $sms['sms_api_token']->value ?? '' }}"
                                        placeholder="Token de autenticação">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('sms_token');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA ASAAS --}}
            <div class="tab-pane fade" id="asaas" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #f39c12;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-dollar-sign mr-1"></i> Configuração da API ASAAS (Financeiro)
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> API Key
                                </label>
                                <div class="input-group">
                                    <input type="password" name="asaas_api_key" id="asaas_key" class="form-control"
                                        value="{{ $asaas['asaas_api_key']->value ?? '' }}"
                                        placeholder="$aact_...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('asaas_key');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-server mr-1"></i> Ambiente
                                </label>
                                <select name="asaas_environment" class="form-control">
                                    <option value="sandbox" {{ (isset($asaas['asaas_environment']) && $asaas['asaas_environment']->value === 'sandbox') ? 'selected' : '' }}>Sandbox (Testes)</option>
                                    <option value="production" {{ (isset($asaas['asaas_environment']) && $asaas['asaas_environment']->value === 'production') ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> URL da API
                                </label>
                                <input type="text" name="asaas_api_url" class="form-control"
                                    value="{{ $asaas['asaas_api_url']->value ?? '' }}"
                                    placeholder="https://api.asaas.com/v3">
                            </div>
                        </div>

                        {{-- CONFIGURAÇÕES ATUAIS --}}
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="p-3 bg-light" style="border-radius: 8px; border-left: 5px solid #f39c12;">
                                    <h6 class="font-weight-bold small text-uppercase mb-3 text-muted">
                                        <i class="fas fa-info-circle mr-1"></i> Configurações Atuais (Banco de Dados)
                                    </h6>
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <p class="small text-muted mb-0">Ambiente Ativo</p>
                                            @php
                                                $env = $asaas['asaas_environment']->value ?? 'sandbox';
                                            @endphp
                                            <span class="badge badge-{{ $env === 'production' ? 'success' : 'warning' }} px-3 py-2 text-uppercase">
                                                {{ $env === 'production' ? 'Produção' : 'Sandbox' }}
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="small text-muted mb-0">URL em Uso</p>
                                            <code class="small" style="color: #e67e22;">{{ $asaas['asaas_api_url']->value ?? 'Padrão do Ambiente' }}</code>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="small text-muted mb-0">Status da Chave</p>
                                            <span class="small font-weight-bold {{ isset($asaas['asaas_api_key']) && !empty($asaas['asaas_api_key']->value) ? 'text-success' : 'text-danger' }}">
                                                {{ isset($asaas['asaas_api_key']) && !empty($asaas['asaas_api_key']->value) ? '✓ CONFIGURADA' : '✗ NÃO ENCONTRADA' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA SPARK --}}
            <div class="tab-pane fade" id="spark" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #8e44ad;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-bolt mr-1"></i> Configuração da API SPARK
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> URL da API
                                </label>
                                <input type="text" name="spark_api_url" class="form-control"
                                    value="{{ $spark['spark_api_url']->value ?? '' }}"
                                    placeholder="https://api.spark.com.br/v1">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-id-badge mr-1"></i> Client ID
                                </label>
                                <input type="text" name="spark_client_id" class="form-control"
                                    value="{{ $spark['spark_client_id']->value ?? '' }}"
                                    placeholder="client_id">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> Client Secret
                                </label>
                                <div class="input-group">
                                    <input type="password" name="spark_client_secret" id="spark_secret" class="form-control"
                                        value="{{ $spark['spark_client_secret']->value ?? '' }}"
                                        placeholder="client_secret">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('spark_secret');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-server mr-1"></i> Ambiente
                                </label>
                                <select name="spark_environment" class="form-control">
                                    <option value="sandbox" {{ ($spark['spark_environment']->value ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testes)</option>
                                    <option value="production" {{ ($spark['spark_environment']->value ?? '') === 'production' ? 'selected' : '' }}>Produção</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA EVOLUTION API --}}
            <div class="tab-pane fade" id="evolution" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #25d366;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fab fa-whatsapp mr-1" style="color:#25d366;"></i> Configuração da Evolution API (WhatsApp)
                        </h6>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> URL do Servidor Evolution
                                </label>
                                <input type="text" name="evolution_api_url" class="form-control"
                                    value="{{ $evolution['evolution_api_url']->value ?? '' }}"
                                    placeholder="https://evolution.seuservidor.com.br">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-mobile-alt mr-1"></i> Nome da Instância
                                </label>
                                <input type="text" name="evolution_instance" class="form-control"
                                    value="{{ $evolution['evolution_instance']->value ?? '' }}"
                                    placeholder="rastertech">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> API Key (Global)
                                </label>
                                <div class="input-group">
                                    <input type="password" name="evolution_api_key" id="evolution_key" class="form-control"
                                        value="{{ $evolution['evolution_api_key']->value ?? '' }}"
                                        placeholder="sua-api-key">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('evolution_key');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-bell mr-1"></i> URL do Webhook (Recebimento)
                                </label>
                                <input type="text" name="evolution_webhook_url" class="form-control"
                                    value="{{ $evolution['evolution_webhook_url']->value ?? '' }}"
                                    placeholder="https://sistema.rastertech.com.br/webhook/whatsapp">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ABA EVOLUTION GO --}}
            <div class="tab-pane fade" id="evolution-go" role="tabpanel">
                <div class="card shadow-sm mb-4" style="border-radius: 12px; border-top: 4px solid #3498db;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-rocket mr-1" style="color:#3498db;"></i> Integração Evolution GO
                        </h6>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> URL do Servidor Evolution GO
                                </label>
                                <input type="text" name="evolution_go_api_url" class="form-control"
                                    value="{{ $evolution_go['evolution_go_api_url']->value ?? '' }}"
                                    placeholder="https://go.evolution.com.br">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-key mr-1"></i> API Key (Global)
                                </label>
                                <div class="input-group">
                                    <input type="password" name="evolution_go_api_key" id="evolution_go_key" class="form-control"
                                        value="{{ $evolution_go['evolution_go_api_key']->value ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('evolution_go_key');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA EVOLUTION CRM --}}
            <div class="tab-pane fade" id="evolution-crm" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #e67e22;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-users-cog mr-1" style="color:#e67e22;"></i> Integração Evolution CRM
                        </h6>
                        <div class="row">
                            <div class="col-md-8 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-link mr-1"></i> Base URL Evolution CRM
                                </label>
                                <input type="text" name="evolution_crm_base_url" class="form-control"
                                    value="{{ $evolution_crm['evolution_crm_base_url']->value ?? '' }}"
                                    placeholder="https://crm.evolution.com.br/api/v1">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-ticket-alt mr-1"></i> API Access Token
                                </label>
                                <div class="input-group">
                                    <input type="password" name="evolution_crm_access_token" id="evolution_crm_token" class="form-control"
                                        value="{{ $evolution_crm['evolution_crm_access_token']->value ?? '' }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="var i=document.getElementById('evolution_crm_token');i.type=i.type==='password'?'text':'password';this.querySelector('i').classList.toggle('fa-eye');this.querySelector('i').classList.toggle('fa-eye-slash')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ABA GERAL --}}
            <div class="tab-pane fade" id="general" role="tabpanel">
                <div class="card shadow-sm" style="border-radius: 12px; border-top: 4px solid #6c757d;">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-4">
                            <i class="fas fa-sliders-h mr-1"></i> Configurações Gerais
                        </h6>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-globe mr-1"></i> URL do Sistema
                                </label>
                                <input type="text" name="app_url" class="form-control"
                                    value="{{ $general['app_url']->value ?? config('app.url') }}"
                                    placeholder="https://sistema.rastertech.com.br">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="small font-weight-bold text-uppercase text-muted">
                                    <i class="fas fa-tag mr-1"></i> Nome do Sistema
                                </label>
                                <input type="text" name="app_name" class="form-control"
                                    value="{{ $general['app_name']->value ?? 'Rastertech Fleet' }}"
                                    placeholder="Rastertech Fleet">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- BOTÃO SALVAR --}}
        <div class="mt-4 text-right">
            <button type="submit" class="btn btn-lg text-white font-weight-bold px-5 shadow"
                style="background: linear-gradient(135deg, #20c997, #17a2b8); border-radius: 10px; border: none;">
                <i class="fas fa-save mr-2"></i> SALVAR CONFIGURAÇÕES
            </button>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
    @if(session('success'))
    Swal.fire({
        position: 'center',
        icon: 'success',
        title: '{{ session("success") }}',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
    });
    @endif
</script>
@endpush
