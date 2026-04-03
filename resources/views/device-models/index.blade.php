@extends('layouts.app')

@section('title', 'Modelos RTECH - Inteligência de Hardware')

@section('content')
<div class="container-fluid">

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block text-uppercase" style="font-size: 2.1rem; letter-spacing: -1px;">
                <i class="fas fa-microchip mr-2 text-warning"></i>Modelos <span class="text-warning">RTECH</span>
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none text-uppercase" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-microchip mr-1 text-warning"></i>Modelos
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block font-italic">Gestão de engenharia e especificações técnicas de hardware.</p>
        </div>
    </div>

    <!-- 🛠️ TABELA TÁTICA INTEGRADA -->
    <div class="card card-outline card-warning shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0 text-uppercase" style="font-size: 1.1rem;">
                <i class="fas fa-layer-group mr-2 text-warning"></i>Biblioteca de Hardware
            </h3>

            <div class="card-tools ml-auto">
                <form action="{{ route('device-models.index') }}" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR POR MODELO -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por nome ou fabricante..." value="{{ $search }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search text-warning"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO (ATIVO / LIXEIRA) -->
                    <div class="ml-4 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm shadow-sm" 
                                onchange="this.form.submit()"
                                style="width: 140px; border-radius: 6px; font-weight: bold; border-color: #dee2e6;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ BOTÃO NOVO MODELO -->
                    <button type="button" 
                            class="btn btn-sm btn-warning ml-3 px-3 font-weight-bold shadow-sm text-dark"
                            data-toggle="modal" data-target="#modalNovoModelo"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO MODELO
                    </button>

                    @if($search || $view !== 'active')
                        <a href="{{ route('device-models.index') }}" class="btn btn-xs btn-outline-danger ml-2" title="Limpar Filtros"><i class="fas fa-times"></i></a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4 py-2-5">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort == 'name' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark d-block sort-link">
                                    ESPECIFICAÇÃO DO MODELO
                                    <span class="float-right"><i class="fas fa-sort{{ $sort == 'name' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i></span>
                                </a>
                            </th>
                            <th class="py-2-5 text-center">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'manufacturer', 'direction' => ($sort == 'manufacturer' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark d-block sort-link">
                                    FABRICANTE / MARCA
                                    <i class="fas fa-sort{{ $sort == 'manufacturer' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell py-2-5 text-center">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'devices_count', 'direction' => ($sort == 'devices_count' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark d-block sort-link">
                                    ESTOQUE FISICO
                                    <i class="fas fa-sort{{ $sort == 'devices_count' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="py-2-5" style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($models as $model)
                        <tr class="model-row animate__animated animate__fadeIn">
                            <td class="align-middle px-4">
                                <div class="font-weight-bold text-dark h6 mb-0">{{ $model->name }}</div>
                                <small class="text-muted text-uppercase">ID: #{{ str_pad($model->id, 4, '0', STR_PAD_LEFT) }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border px-3 py-2 text-uppercase shadow-sm font-weight-bold" style="border-radius: 6px;">
                                    <i class="fas fa-industry mr-1 text-muted"></i> {{ $model->manufacturer ?? 'GENÉRICO' }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border px-3 py-2 font-weight-bold shadow-sm" style="border-radius: 6px; font-size: 0.8rem; color: #495057;">
                                    {{ $model->devices_count }} UNIDADES
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm model-actions" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if($view !== 'trash')
                                        <button type="button" class="btn btn-light btn-square btn-view-model" title="Ver Dossiê" data-id="{{ $model->id }}"><i class="fas fa-eye fa-lg text-info"></i></button>
                                        <button type="button" class="btn btn-light btn-square border-right border-left btn-edit-model" title="Editar" data-id="{{ $model->id }}"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                        <button type="button" class="btn btn-light btn-square btn-delete-model" title="Inativar" data-id="{{ $model->id }}" data-name="{{ $model->name }}"><i class="fas fa-power-off fa-lg text-danger"></i></button>
                                        <form id="formDelete_{{ $model->id }}" action="{{ route('device-models.destroy', $model->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    @else
                                        <form action="{{ route('device-models.restore', $model->id) }}" method="POST" class="m-0 d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-light btn-square text-success" title="Reativar Ficha Técnica">
                                                <i class="fas fa-undo fa-lg"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted font-italic">
                                <i class="fas fa-microchip fa-4x mb-3 opacity-20"></i><br>
                                <span class="h5">Nenhum modelo técnico localizado sob esta visão.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($models->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $models->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO MODELO -->
<div class="modal fade" id="modalNovoModelo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3 shadow-sm">
                <h5 class="modal-title font-weight-bold" style="letter-spacing: -0.5px;">
                    <i class="fas fa-plus-circle mr-2"></i>Nova Especificação RTECH
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('device-models.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-microchip mr-1"></i> Nome do Modelo</label>
                        <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 10px;" placeholder="Ex: GT06, TL300, Suntech ST310U" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-industry mr-1"></i> Fabricante / Marca</label>
                        <input type="text" name="manufacturer" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 10px;" placeholder="Ex: Queclink, Suntech, Concox">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5 shadow-sm font-weight-bold" style="border-radius: 10px;">SALVAR MODELO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .text-dark { color: #fff !important; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .model-row:hover { background: rgba(0, 0, 0, 0.015); }
    .model-actions .btn { width: 42px; height: 42px; border-radius: 10px; }
    .btn-group .btn { padding: 8px 12px; }
    .animate__animated { --animate-duration: 0.5s; }
    .py-2-5 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
    .sort-link { text-decoration: none !important; color: inherit !important; display: block; filter: grayscale(1); transition: 0.2s; }
    .sort-link:hover { filter: grayscale(0); background: rgba(0,0,0,0.03); color: #ffc107 !important; }
    .opacity-50 { opacity: 0.5; }
</style>

@push('scripts')
<script>
    const modelData = @json($modelData);

    $(document).ready(function(){

        // 🔎 HANDLER: DOSSIÊ DO MODELO (SWEETALERT2)
        $(document).on('click', '.btn-view-model', function() {
            const id = $(this).data('id');
            const model = modelData[id];
            
            if (!model) return;

            Swal.fire({
                title: '<i class="fas fa-microchip mr-2 text-info"></i> MODELO RTECH',
                width: '550px',
                html: `
                    <div class="text-left font-family-sans">
                        <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #17a2b8 !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">IDENTIFICAÇÃO DO PRODUTO</label>
                            <div class="h5 font-weight-bold text-dark mb-0">${model.name}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #6c757d !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">FABRICANTE</label>
                                    <div class="font-weight-bold text-dark h6 mb-0 text-uppercase">${model.manufacturer || 'GENÉRICO'}</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #ffc107 !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">ESTOQUE ATUAL</label>
                                    <div class="font-weight-bold text-warning h5 mb-0">${model.devices} un</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 mb-2 bg-light rounded border-left" style="border-left: 4px solid #28a745 !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase text-success">STATUS OPERACIONAL</label>
                            <div class="h6 font-weight-bold text-dark mb-0 text-uppercase">
                                <i class="fas fa-check-circle mr-1 text-success"></i> Homologado para operação
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'FECHAR',
                confirmButtonColor: '#343a40',
                customClass: { confirmButton: 'px-5 py-2 font-weight-bold' }
            });
        });

        // ✏️ HANDLER: EDIÇÃO TÁTICA (AJAX)
        $(document).on('click', '.btn-edit-model', function() {
            const id = $(this).data('id');
            const model = modelData[id];
            
            if (!model) return;

            Swal.fire({
                title: '<i class="fas fa-tools mr-2 text-warning"></i> EDITAR MODELO',
                width: '500px',
                html: `
                    <div class="text-left px-2">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase font-family-sans">NOME DO MODELO</label>
                            <input type="text" id="swal_model_name" class="form-control" value="${model.name}" style="height: 45px; border-radius: 8px;">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase font-family-sans">FABRICANTE / MARCA</label>
                            <input type="text" id="swal_model_man" class="form-control" value="${model.manufacturer || ''}" style="height: 45px; border-radius: 8px;">
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'SALVAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: `/device-models/${id}`,
                        method: 'PUT',
                        data: {
                            name: $('#swal_model_name').val(),
                            manufacturer: $('#swal_model_man').val(),
                            _token: '{{ csrf_token() }}'
                        }
                    }).catch(error => {
                        Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao sincronizar dados');
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SINCRONIZADO',
                        text: 'Ficha técnica atualizada na biblioteca.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        });

        // ⛔ HANDLER: INATIVAR COM SEGURANÇA
        $(document).on('click', '.btn-delete-model', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Inativar Modelo?',
                html: `Deseja realmente mover o modelo <strong>${name}</strong> para a lixeira?<br><small class='text-muted'>Apenas modelos sem estoque vinculado podem ser inativados.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, inativar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { $('#formDelete_' + id).submit(); }
            });
        });
    });

    // 🔔 CENTRALIZADOR DE ALERTAS
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Sucesso!', text: '{{ session("success") }}', confirmButtonColor: '#3085d6', timer: 3000 });
    @endif

    @if(session('error'))
        Swal.fire({ 
            html: `
                <div class="text-center">
                    <div class="mb-3"><i class="fas fa-exclamation-triangle fa-5x" style="color: #ff8c00;"></i></div>
                    <h2 class="font-weight-bold text-dark mb-2" style="font-size: 1.8rem;">ATENÇÃO!</h2>
                    <div class="text-dark h6 font-weight-normal px-3 py-2" style="line-height: 1.5;">{{ session('error') }}</div>
                </div>
            `,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'ENTENDI',
            background: '#fff3cd',
            customClass: { confirmButton: 'px-5 py-2 font-weight-bold text-dark border-0 shadow-sm mt-3' }
        });
    @endif
</script>
@endpush

@endsection
