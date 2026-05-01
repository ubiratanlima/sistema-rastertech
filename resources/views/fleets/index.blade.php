@extends('layouts.app')

@section('title', 'Gestão de Frotas | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro Limpo) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-truck-moving mr-2 text-primary"></i>Gestão de Frotas
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Monitoramento e controle de ativos instalados na base.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL: LISTAGEM INTEGRADA -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list-ul mr-2 text-primary"></i>Inventário Geral de Veículos
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="/fleets" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Placa, Modelo ou Cliente..." value="{{ $search }}">
                        <input type="hidden" name="view" value="{{ $view }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO -->
                    <div class="ml-4 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 150px; font-weight: bold; border-radius: 6px;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS (C/ RAST.)</option>
                            <option value="inactive" {{ $view == 'inactive' ? 'selected' : '' }}>🟡 INATIVOS (S/ RAST.)</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ EXCLUÍDOS</option>
                        </select>
                    </div>

                    <!-- ➕ NOVO VEÍCULO -->
                    <button type="button" class="btn btn-sm btn-primary ml-4 px-3 font-weight-bold shadow-sm" onclick="openCreateVehicleModal()" style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO VEÍCULO
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="fleetTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 1rem;">
                            <th style="width: 140px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'plate', 'direction' => ($sort == 'plate' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    PLACA <i class="fas fa-sort{{ $sort == 'plate' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'brand', 'direction' => ($sort == 'brand' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    VEÍCULO / FABRICANTE <i class="fas fa-sort{{ $sort == 'brand' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 200px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'customer_id', 'direction' => ($sort == 'customer_id' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    PROPRIETÁRIO <i class="fas fa-sort{{ $sort == 'customer_id' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 180px;">RST (INTERNAL)</th>
                            <th style="width: 180px;">STATUS RST</th>
                            <th style="width: 150px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $v)
                        @php
                            $device = $v->devices->first();
                            $hasDevice = $v->devices_count > 0;
                        @endphp
                        <!-- 🏁 LINHA MASTER -->
                        <tr style="transition: background 0.3s; height: 75px;">
                            <td class="align-middle text-center accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-fleet-{{ $v->id }}">
                                <div class="mercosul-plate shadow-sm mx-auto">
                                    <div class="mercosul-header">BRASIL</div>
                                    <div class="mercosul-body" style="font-size: 1.1rem;">{{ $v->plate }}</div>
                                </div>
                                @if($search)
                                    <div class="mt-1">
                                        @if($v->trashed())
                                            <span class="badge badge-danger" style="font-size: 0.6rem; letter-spacing: 0.5px;">LIXEIRA</span>
                                        @elseif(!$hasDevice)
                                            <span class="badge badge-warning text-dark" style="font-size: 0.6rem; letter-spacing: 0.5px;">INATIVO</span>
                                        @elseif($view !== 'active' && $hasDevice)
                                            <span class="badge badge-success" style="font-size: 0.6rem; letter-spacing: 0.5px;">ATIVO</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle px-4 accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-fleet-{{ $v->id }}">
                                <div class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $v->brand }}</div>
                                <div class="small text-muted font-weight-bold text-uppercase opacity-75">{{ $v->model }}</div>
                            </td>
                            <td class="text-center align-middle accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-fleet-{{ $v->id }}">
                                <div class="text-primary font-weight-bold">{{ $v->customer->company_name ?? $v->customer->name ?? '---' }}</div>
                            </td>
                            <td class="text-center align-middle accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-fleet-{{ $v->id }}">
                                @if($hasDevice)
                                    <span class="badge badge-light border px-3 py-1 font-weight-bold text-primary" style="font-size: 0.9rem; border-radius: 50px;">
                                        {{ $device->internal_code }}
                                    </span>
                                @else
                                    <span class="text-muted small">SEM EQUIPAMENTO</span>
                                @endif
                            </td>
                            <td class="text-center align-middle accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-fleet-{{ $v->id }}">
                                @if($hasDevice)
                                    @php
                                        $statusClass = [
                                            'active' => 'badge-success',
                                            'maintenance' => 'badge-warning',
                                            'canceled' => 'badge-danger'
                                        ][$device->status] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} px-3 py-1 text-uppercase shadow-none" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                                        {{ strtoupper($device->status) }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary opacity-50 px-3 py-1 text-uppercase" style="font-size: 0.7rem;">DISPONÍVEL</span>
                                @endif
                            </td>
                            <td class="text-center align-middle px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if($v->trashed())
                                        <button class="btn btn-light btn-square-sm" onclick="event.stopPropagation(); document.getElementById('form-restore-{{ $v->id }}').submit();" title="Restaurar Veículo">
                                            <i class="fas fa-undo-alt text-success"></i>
                                        </button>
                                        <form id="form-restore-{{ $v->id }}" action="{{ route('fleets.restore', $v->id) }}" method="POST" class="d-none">@csrf @method('PUT')</form>
                                    @else
                                        <button class="btn btn-light btn-square-sm border-right" onclick="viewFleetDossier(this)" 
                                                title="Visualizar Detalhes"
                                                data-id="{{ $v->id }}"
                                                data-plate="{{ $v->plate }}"
                                                data-brand="{{ $v->brand }}"
                                                data-model="{{ $v->model }}"
                                                data-year="{{ $v->year }}"
                                                data-color="{{ $v->color }}"
                                                data-renavam="{{ $v->renavam }}"
                                                data-chassi="{{ $v->chassi }}"
                                                data-photo-front="{{ $v->photo_front ? asset('storage/' . $v->photo_front) : '' }}"
                                                data-photo-back="{{ $v->photo_back ? asset('storage/' . $v->photo_back) : '' }}"
                                                data-customer="{{ $v->customer->company_name ?? $v->customer->name ?? '---' }}"
                                                data-has-device="{{ $hasDevice ? 'true' : 'false' }}"
                                                data-internal-code="{{ $hasDevice ? $device->internal_code : '' }}"
                                                data-model-device="{{ ($hasDevice && $device->deviceModel) ? $device->deviceModel->name : '' }}"
                                                data-imei="{{ $hasDevice ? $device->imei : '' }}"
                                                data-sim="{{ ($hasDevice && $device->gsmCard) ? $device->gsmCard->phone_number : '---' }}"
                                                data-updated="{{ ($hasDevice && $device->updated_at) ? $device->updated_at->format('d/m/Y H:i') : '---' }}"
                                        >
                                            <i class="fas fa-eye text-info"></i>
                                        </button>
                                        <button class="btn btn-light btn-square-sm border-right" onclick="openFleetDeviceEdit(this)"
                                                title="Gestão de Hardware"
                                                data-has-device="{{ $hasDevice ? 'true' : 'false' }}"
                                                data-id="{{ $device->id ?? 0 }}"
                                                data-internal-code="{{ $device->internal_code ?? '' }}"
                                                data-model="{{ ($hasDevice && $device->deviceModel) ? $device->deviceModel->name : '' }}"
                                                data-imei="{{ $hasDevice ? $device->imei : '' }}"
                                                data-sim="{{ ($hasDevice && $device->gsmCard) ? $device->gsmCard->phone_number : '---' }}"
                                                data-sim-operator="{{ ($hasDevice && $device->gsmCard && $device->gsmCard->provider) ? $device->gsmCard->provider->name : '---' }}"
                                                data-vehicle-plate="{{ $v->plate }}"
                                                data-vehicle-id="{{ $v->id }}"
                                                data-customer-id="{{ $v->customer_id }}"
                                                data-status="{{ $device->status ?? 'active' }}"
                                        >
                                            <i class="fas fa-tools text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-square-sm" onclick="confirmFleetDelete({{ $v->id }}, '{{ $v->plate }}')" title="Remover da Frota">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                                <form id="form-delete-{{ $v->id }}" action="{{ route('fleets.destroy', $v->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </td>
                        </tr>

                        <!-- 🛠️ DETALHE ACORDEÃO (Dossiê do Veículo) -->
                        <tr class="detail-row">
                            <td colspan="6" class="p-0 border-0">
                                <div id="row-fleet-{{ $v->id }}" class="collapse" data-parent="#fleetTable">
                                    <div class="bg-light p-4 shadow-inner" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                                        <div class="row">
                                            <!-- Coluna Ativo -->
                                            <div class="col-md-4">
                                                <label class="small text-muted font-weight-bold text-uppercase mb-2"><i class="fas fa-satellite-dish mr-1"></i> Equipamento Rastreador</label>
                                                <div class="card shadow-none border p-3" style="border-radius: 12px; background: #fff;">
                                                    @if($hasDevice)
                                                        <div class="d-flex align-items-center">
                                                            <div class="mr-3 bg-primary text-white p-2 rounded" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-microchip fa-lg"></i>
                                                            </div>
                                                            <div>
                                                                <div class="h6 font-weight-bold mb-0">{{ $device->internal_code }}</div>
                                                                <div class="small text-muted text-uppercase">{{ $device->deviceModel->name ?? 'N/A' }}</div>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="small d-flex justify-content-between">
                                                            <span class="text-muted">IMEI:</span>
                                                            <span class="font-weight-bold">{{ $device->imei }}</span>
                                                        </div>
                                                        <div class="small d-flex justify-content-between">
                                                            <span class="text-muted">Protocolo:</span>
                                                            <span class="font-weight-bold text-info">{{ $device->platform->name ?? 'N/A' }}</span>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-3">
                                                            <i class="fas fa-exclamation-triangle text-warning mb-2 opacity-50"></i>
                                                            <div class="small font-weight-bold text-muted">VEÍCULO SEM TECNOLOGIA</div>
                                                            <button class="btn btn-xs btn-outline-primary mt-2" onclick="openFleetDeviceEdit(this)" 
                                                                    data-has-device="false" data-vehicle-plate="{{ $v->plate }}" data-vehicle-id="{{ $v->id }}" data-customer-id="{{ $v->customer_id }}">
                                                                VINCULAR AGORA
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Coluna Conectividade -->
                                            <div class="col-md-4">
                                                <label class="small text-muted font-weight-bold text-uppercase mb-2"><i class="fas fa-sim-card mr-1"></i> Link de Dados</label>
                                                <div class="card shadow-none border p-3" style="border-radius: 12px; background: #fff;">
                                                    @if($hasDevice && $device->gsmCard)
                                                        <div class="d-flex align-items-center">
                                                            <div class="mr-3 bg-success text-white p-2 rounded" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-signal fa-lg"></i>
                                                            </div>
                                                            <div>
                                                                <div class="h6 font-weight-bold mb-0">+55 {{ $device->gsmCard->phone_number }}</div>
                                                                <div class="small text-muted text-uppercase">{{ $device->gsmCard->provider->name ?? 'OPERADORA' }}</div>
                                                            </div>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="small d-flex justify-content-between">
                                                            <span class="text-muted">ICCID:</span>
                                                            <span class="font-weight-bold">{{ $device->gsmCard->iccid }}</span>
                                                        </div>
                                                    @else
                                                        <div class="text-center py-3">
                                                            <i class="fas fa-times-circle text-danger mb-2 opacity-50"></i>
                                                            <div class="small font-weight-bold text-muted">LINK INATIVO O SOLTO</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Coluna Operacional -->
                                            <div class="col-md-4">
                                                <label class="small text-muted font-weight-bold text-uppercase mb-2"><i class="fas fa-terminal mr-1"></i> Comando & Controle</label>
                                                <div class="card shadow-none border p-3" style="border-radius: 12px; background: #1e293b;">
                                                    <div class="btn-group-vertical w-100">
                                                        <button class="btn btn-sm btn-outline-light text-left mb-2 d-flex align-items-center justify-content-between">
                                                            <span><i class="fas fa-power-off mr-2"></i> Bloqueio</span>
                                                            <i class="fas fa-chevron-right small opacity-50"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-light text-left mb-2 d-flex align-items-center justify-content-between">
                                                            <span><i class="fas fa-history mr-2"></i> Última Posição</span>
                                                            <i class="fas fa-chevron-right small opacity-50"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-light text-left d-flex align-items-center justify-content-between">
                                                            <span><i class="fas fa-redo mr-2"></i> Resetar Módulo</span>
                                                            <i class="fas fa-chevron-right small opacity-50"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-truck-moving fa-3x text-muted mb-3 opacity-20"></i>
                                <h4 class="text-muted font-weight-bold">Frota não encontrada</h4>
                                <p class="text-muted">Crie um novo veículo ou tente outro termo de busca.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($vehicles->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $vehicles->links() }}
        </div>
        @endif
    </div>
</div>

@include('fleets.modals.create')

@push('scripts')
<script>
    window.openCreateVehicleModal = () => $('#modalCreateVehicle').modal('show');
    
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
    // 👁️ VISUALIZAR DOSSIÊ (SIMULAÇÃO DE DOCUMENTO)
    window.viewFleetDossier = function(el) {
        const data = $(el).data();
        
        Swal.fire({
            title: `<div class="d-flex align-items-center justify-content-center" style="font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px;"><i class="fas fa-id-card mr-3 text-primary"></i> Dossiê Digital: ${data.plate}</div>`,
            width: '850px',
            confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>FECHAR DOCUMENTO',
            confirmButtonColor: '#1e293b',
            html: `
                <div class="text-left py-2" style="font-family: 'Inter', sans-serif;">
                    
                    <!-- 🚘 CABEÇALHO TIPO DOCUMENTO -->
                    <div class="row mb-4">
                        <div class="col-md-5 text-center border-right">
                            <div class="mercosul-plate shadow-sm mb-3" style="transform: scale(1.3); margin-top: 20px;">
                                <div class="mercosul-header">BRASIL</div>
                                <div class="mercosul-body">${data.plate}</div>
                            </div>
                            <div class="badge badge-primary px-3 py-1 mt-3 text-uppercase" style="font-size: 0.8rem;">Veículo Homologado</div>
                        </div>
                        <div class="col-md-7 pl-4">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">Marca / Fabricante</label>
                                    <div class="h6 font-weight-bold text-dark">${data.brand}</div>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">Modelo / Versão</label>
                                    <div class="h6 font-weight-bold text-dark">${data.model}</div>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">Ano</label>
                                    <div class="h6 font-weight-bold text-dark">${data.year || '---'}</div>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">Cor</label>
                                    <div class="h6 font-weight-bold text-dark">${data.color || '---'}</div>
                                </div>
                                <div class="col-4 mb-3">
                                    <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">ID</label>
                                    <div class="h6 font-weight-bold text-primary">#${data.id}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 🛠️ DADOS TÉCNICOS -->
                    <div class="p-3 bg-light rounded border shadow-xs mb-4">
                        <div class="row">
                            <div class="col-6 border-right">
                                <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">RENAVAM</label>
                                <div class="small font-weight-bold text-dark">${data.renavam || 'NÃO INFORMADO'}</div>
                            </div>
                            <div class="col-6 pl-3">
                                <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-0">CHASSI</label>
                                <div class="small font-weight-bold text-dark text-truncate">${data.chassi || 'NÃO INFORMADO'}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 📡 STATUS OPERACIONAL -->
                    <div class="mb-4">
                        <div class="small text-primary font-weight-bold mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-satellite-dish mr-2"></i>Status de Rastreamento</div>
                        <!-- STATUS OPERACIONAL DINÂMICO -->
                        <div class="p-3 border rounded shadow-xs ${data.hasDevice ? 'bg-white border-left-indicator' : 'bg-light'}" style="${data.hasDevice ? 'border-left: 4px solid #3b82f6 !important;' : ''}">
                            ${data.hasDevice ? `
                                <div class="row align-items-center">
                                    <div class="col-1"><i class="fas fa-microchip text-primary fa-lg opacity-50"></i></div>
                                    <div class="col-3 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Código RTECH</label><div class="small font-weight-bold text-dark">${data.internalCode}</div></div>
                                    <div class="col-5 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">IMEI / Modelo</label><div class="small text-muted">${data.imei} / <span class="text-primary">${data.modelDevice}</span></div></div>
                                    <div class="col-3"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Último Sinal</label><div class="small font-weight-bold text-success">${data.updated}</div></div>
                                </div>
                            ` : `
                                <div class="text-center py-2 text-muted small"><i class="fas fa-exclamation-triangle mr-2 text-warning"></i> Veículo sem tecnologia vinculada no momento.</div>
                            `}
                        </div>
                    </div>

                    <!-- 📸 REGISTRO FOTOGRÁFICO -->
                    <div class="row">
                        <div class="col-6">
                            <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-2">Visão Frontal</label>
                            <div class="photo-box rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 180px;">
                                ${data.photoFront ? `<img src="${data.photoFront}" class="img-fluid w-100 h-100" style="object-fit: cover;">` : '<i class="fas fa-camera fa-2x text-muted opacity-20"></i>'}
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="tiny-text text-muted d-block text-uppercase font-weight-bold mb-2">Visão Traseira / Lateral</label>
                            <div class="photo-box rounded border bg-light d-flex align-items-center justify-content-center overflow-hidden" style="height: 180px;">
                                ${data.photoBack ? `<img src="${data.photoBack}" class="img-fluid w-100 h-100" style="object-fit: cover;">` : '<i class="fas fa-camera fa-2x text-muted opacity-20"></i>'}
                            </div>
                        </div>
                    </div>

                </div>`
        });
    }

    // 🔨 GESTÃO DE HARDWARE (MÓVEL)
    window.openFleetDeviceEdit = function(el) {
        const data = $(el).data();
        
        // MODO INSERÇÃO (VEÍCULO VAZIO)
        if (String(data.hasDevice) === 'false' || !data.hasDevice) {
            Swal.fire({
                title: '<i class="fas fa-plus-circle mr-2 text-success"></i> VINCULAR HARDWARE',
                width: '500px',
                html: '<div class="text-left">' +
                        '<div class="text-center mb-5">' +
                            '<div class="font-weight-bold small text-muted text-uppercase mb-3">VEÍCULO SELECIONADO</div>' +
                            '<div class="mercosul-plate shadow-none mx-auto" style="transform: scale(1.5); transform-origin: center; margin-bottom: 20px;">' +
                                '<div class="mercosul-header">BRASIL</div>' +
                                '<div class="mercosul-body">' + (data.vehiclePlate || '---') + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="font-weight-bold small text-muted text-uppercase mb-1"><i class="fas fa-satellite-dish text-primary mr-1"></i> Rastreador no Estoque</label>' +
                            '<select id="new_device_id" class="form-control font-weight-bold">' +
                                '<option value="">--- SELECIONE UM ATIVO ---</option>' +
                                '@foreach($freeDevices as $fd)' +
                                    '<option value="{{$fd->id}}" data-code="{{$fd->internal_code}}" data-imei="{{$fd->imei}}">{{$fd->internal_code}} &bull; IMEI: {{$fd->imei}}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="font-weight-bold small text-muted text-uppercase mb-1"><i class="fas fa-sim-card text-success mr-1"></i> Link de Dados (Opcional)</label>' +
                            '<select id="new_sim_id" class="form-control font-weight-bold">' +
                                '<option value="">--- INSERIR SEM CHIP ---</option>' +
                                '@foreach($freeSims as $fs)' +
                                    '<option value="{{$fs->id}}">ICCID: {{$fs->iccid}} / +55 {{$fs->phone_number}}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</div>' +
                    '</div>',
                showCancelButton: true,
                confirmButtonText: 'SALVAR VÍNCULO',
                preConfirm: () => {
                    const devId = $('#new_device_id').val();
                    if (!devId) return Swal.showValidationMessage('Selecione um hardware.');
                    const opt = $('#new_device_id option:selected');
                    return $.ajax({
                        url: '/devices/' + devId,
                        method: 'PUT',
                        data: {
                            new_vehicle_id: data.vehicleId,
                            new_gsm_card_id: $('#new_sim_id').val() || null,
                            internal_code: opt.data('code'),
                            imei: opt.data('imei'),
                            status: 'active',
                            customer_id: data.customerId,
                            unlink_vehicle: 0,
                            unlink_chip: 0,
                            _token: '{{ csrf_token() }}'
                        }
                    }).catch(err => Swal.showValidationMessage(err.responseJSON?.message || 'Erro ao vincular.'));
                }
            }).then(r => r.isConfirmed && location.reload());
            return;
        }

        // MODO EDIÇÃO (DESVINCULAR/MANUTENÇÃO)
        Swal.fire({
            title: '<i class="fas fa-tools mr-2 text-warning"></i> GESTÃO DE HARDWARE',
            width: '600px',
            html: `
                <div class="text-left px-2">
                    <div class="row bg-light p-3 rounded border mb-4">
                        <div class="col-8">
                            <label class="small text-muted font-weight-bold text-uppercase d-block mb-1">RASTREADOR ATUAL</label>
                            <span class="h5 font-weight-bold text-primary mb-0">${data.internalCode}</span>
                            <div class="small text-muted font-weight-bold">${data.model} &bull; ${data.imei}</div>
                        </div>
                        <div class="col-4 text-right">
                             <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="$('#unlink_form').fadeIn(); $(this).hide();">
                                <i class="fas fa-unlink mr-1"></i> DESVINCULAR
                             </button>
                        </div>
                        <div id="unlink_form" class="col-12 mt-3" style="display:none; border-top: 1px dashed #ccc; padding-top: 15px;">
                            <label class="small text-danger font-weight-bold text-uppercase">Motivo da Retirada</label>
                            <textarea id="unlink_reason" class="form-control" rows="2" placeholder="Ex: Manutenção, upgrade..."></textarea>
                        </div>
                    </div>
                    
                    <div class="row bg-light p-3 rounded border">
                        <div class="col-8">
                            <label class="small text-muted font-weight-bold text-uppercase d-block mb-1">CHIP / CONECTIVIDADE</label>
                            <span class="h6 font-weight-bold text-dark mb-0">${data.sim}</span>
                            <div class="small text-muted font-weight-bold text-uppercase">${data.simOperator}</div>
                        </div>
                        <div class="col-4 text-right">
                             <input type="hidden" id="unlink_chip_hidden" value="0">
                             <button type="button" class="btn btn-sm btn-outline-danger mt-1" onclick="$(this).parent().find('#unlink_chip_hidden').val('1'); $(this).text('Aguardando Salvar...').prop('disabled', true);">
                                <i class="fas fa-sim-card mr-1"></i> SOLTAR CHIP
                             </button>
                        </div>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'SALVAR ALTERAÇÕES',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const isUnlinking = $('#unlink_form').is(':visible');
                const reason = $('#unlink_reason').val();
                if(isUnlinking && reason.length < 5) return Swal.showValidationMessage('Informe o motivo da desativação.');
                
                return $.ajax({
                    url: '/devices/' + data.id,
                    method: 'PUT',
                    data: {
                        internal_code: data.internalCode,
                        imei: data.imei,
                        status: data.status,
                        customer_id: data.customerId,
                        unlink_vehicle: isUnlinking ? 1 : 0,
                        unlink_reason: reason,
                        unlink_chip: $('#unlink_chip_hidden').val() === '1' ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    }
                }).catch(err => Swal.showValidationMessage(err.responseJSON?.message || 'Erro ao processar.'));
            }
        }).then(r => r.isConfirmed && location.reload());
    }

    // 🗑️ EXCLUSÃO
    window.confirmFleetDelete = function(id, plate) {
        Swal.fire({
            title: 'Inativar Veículo?',
            text: `Deseja remover a placa ${plate} da frota ativa?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, Remover!'
        }).then((res) => { if(res.isConfirmed) document.getElementById(`form-delete-${id}`).submit(); });
    }
</script>
@endpush

<style>
    .btn-square-sm { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .accordion-toggle[aria-expanded="true"] .accordion-icon { transform: rotate(180deg); }
    
    /* 🇧🇷 ESTILO PLACA MERCOSUL (GOLD STANDARD) */
    .mercosul-plate { 
        display: inline-flex; 
        flex-direction: column; 
        background: #fff; 
        border: 1.5px solid #000; 
        border-radius: 4px; 
        overflow: hidden; 
        min-width: 110px; 
        line-height: 1; 
        vertical-align: middle; 
    }
    .mercosul-header { 
        background: #003399; 
        color: #fff; 
        font-size: 0.45rem; 
        text-align: center; 
        padding: 2px 0; 
        font-weight: 800; 
        letter-spacing: 1.5px; 
        border-bottom: 0.5px solid #000; 
    }
    .mercosul-body { 
        color: #000; 
        font-size: 1.2rem; 
        text-align: center; 
        padding: 4px 10px; 
        font-weight: bold; 
        font-family: 'Roboto Mono', monospace; 
        letter-spacing: -1px; 
    }
</style>
@endsection
