@extends('layouts.app')

@section('title', 'Gestão de Comandos SMS')

@section('content')
<div class="container-fluid">

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block text-uppercase" style="font-size: 2.1rem; letter-spacing: -1px;">
                <i class="fas fa-terminal mr-2 text-indigo"></i>Comandos <span class="text-indigo">SMS</span>
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none text-uppercase" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-terminal mr-1 text-indigo"></i>Scripts
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block font-italic">Biblioteca tática de templates para automação de hardware.</p>
        </div>
    </div>

    <!-- 🛠️ TABELA TÁTICA INTEGRADA -->
    <div class="card card-outline shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #6610f2;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0 text-uppercase" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-indigo"></i>Biblioteca de Configuração
            </h3>

            <div class="card-tools ml-auto">
                <form action="{{ route('device-commands.index') }}" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR POR COMANDO -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por descrição..." value="{{ $search }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search text-indigo"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO -->
                    <div class="ml-4 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm shadow-sm" 
                                onchange="this.form.submit()"
                                style="width: 140px; border-radius: 6px; font-weight: bold; border-color: #dee2e6;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ BOTÃO NOVO SCRIPT -->
                    <button type="button" 
                            class="btn btn-sm btn-indigo ml-3 px-3 font-weight-bold shadow-sm text-white"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center; background-color: #6610f2; border-color: #6610f2;"
                            data-toggle="modal" data-target="#modalNovoComando">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO SCRIPT
                    </button>

                    @if($search || $view !== 'active')
                        <a href="{{ route('device-commands.index') }}" class="btn btn-xs btn-outline-danger ml-2" title="Limpar Filtros"><i class="fas fa-times"></i></a>
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
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'device_model_id', 'direction' => ($sort == 'device_model_id' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    MODELO <i class="fas fa-sort{{ $sort == 'device_model_id' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-2 opacity-50"></i>
                                </a>
                            </th>
                            <th class="text-left py-2-5">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'description', 'direction' => ($sort == 'description' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    DESCRIÇÃO <i class="fas fa-sort{{ $sort == 'description' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-2 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell py-2-5 text-center">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'execution_order', 'direction' => ($sort == 'execution_order' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    ORDEM <i class="fas fa-sort{{ $sort == 'execution_order' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-2 opacity-50"></i>
                                </a>
                            </th>
                            <th class="py-2-5 text-center" style="width: 140px; color: #6c757d;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commands as $command)
                        <tr class="command-row animate__animated animate__fadeIn">
                            <td class="align-middle px-4 text-uppercase" style="color: #6610f2; font-size: 1rem;">
                                {{ $command->deviceModel->name }}
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold text-dark">{{ $command->description }}</div>
                                <small class="text-muted font-family-monospace d-none d-md-block">{{ \Illuminate\Support\Str::limit($command->command_template, 40) }}</small>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell" style="font-size: 1rem;">
                                #{{ $command->execution_order }}
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm border" style="border-radius: 8px; overflow: hidden;">
                                    @if($view !== 'trash')
                                        <button type="button" class="btn btn-light btn-square btn-view-command" title="Ver Script" data-id="{{ $command->id }}"><i class="fas fa-eye fa-lg text-info"></i></button>
                                        <button type="button" class="btn btn-light btn-square border-right border-left btn-edit-command" title="Editar" data-id="{{ $command->id }}"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                        <button type="button" class="btn btn-light btn-square text-danger" title="Inativar Script" 
                                                onclick="confirmCommandDeletion({{ $command->id }})">
                                            <i class="fas fa-power-off fa-lg"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('device-commands.restore', $command->id) }}" method="POST" class="m-0 d-inline">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-light btn-square text-success" title="Reativar Script">
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
                                <i class="fas fa-terminal fa-4x mb-3 opacity-20 text-indigo"></i><br>
                                <span class="h5">Nenhum script técnico localizado sob esta visão.</span>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($commands->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $commands->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO COMANDO (MANTIDO PARA CADASTRO INICIAL) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalNovoComando" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white border-0 py-3" style="background-color: #6610f2;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-comment-medical mr-2"></i>Novo Comando SMS</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('device-commands.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 form-group mb-3">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Modelo de Rastreador</label>
                            <select name="device_model_id" class="form-control border-0 shadow-sm font-weight-bold" style="background: #f8f9fa; border-radius: 8px; height: 45px; font-size: 1.1rem;" required>
                                <option value="">Selecione o Hardware...</option>
                                @foreach($deviceModels as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->manufacturer }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-8 form-group mb-3">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Descrição</label>
                            <input type="text" name="description" class="form-control border-0 shadow-sm font-weight-bold" style="background: #f8f9fa; border-radius: 8px; height: 45px; font-size: 1.1rem;" placeholder="Ex: Resetar Equipamento" required>
                        </div>
                        <div class="col-4 form-group mb-3">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Ordem</label>
                            <input type="number" name="execution_order" class="form-control border-0 shadow-sm font-weight-bold" style="background: #f8f9fa; border-radius: 8px; height: 45px; font-size: 1.1rem;" value="1" required>
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Template SMS</label>
                            <textarea name="command_template" class="form-control border-0 shadow-sm" rows="3" style="background: #f8f9fa; border-radius: 8px; font-family: monospace; font-size: 1.1rem;" placeholder="Ex: RESET#" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn text-white px-4 shadow-sm font-weight-bold" style="border-radius: 8px; background-color: #6610f2;">CADASTRAR COMANDO</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const globalCommands = {!! json_encode($commandData) !!};

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'SUCESSO!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif

    /**
     * 👁️ DOSSIÊ TÁTICO: VISUALIZAÇÃO DO SCRIPT SMS
     */
    $('.btn-view-command').on('click', function() {
        const id = $(this).data('id');
        const data = globalCommands[id];

        Swal.fire({
            title: '<i class="fas fa-terminal mr-2 text-indigo"></i> COMANDO SMS',
            width: '550px',
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#6c757d',
            html: `
                <div class="text-left px-2">
                    <div class="p-3 bg-light rounded border-left mb-3" style="border-left: 4px solid #6610f2 !important;">
                        <label class="text-muted mb-1 d-block font-weight-bold text-uppercase text-indigo" style="font-size: 0.8rem;">Alvo do Comando</label>
                        <div class="font-weight-bold mb-0 text-dark" style="font-size: 1.1rem;">${data.model_name}</div>
                        <small class="text-muted" style="font-size: 0.75rem;">Ordem de Execução: #${data.execution_order}</small>
                    </div>

                    <label class="text-muted mb-1 d-block font-weight-bold text-uppercase" style="font-size: 0.8rem;">Descrição Técnica</label>
                    <div class="font-weight-bold mb-3 text-dark" style="font-size: 1.1rem;">${data.description}</div>

                    <div class="mt-3 p-3 rounded" style="background: #1e1e2f; border: 1px solid #334155;">
                        <label class="text-indigo mb-2 d-block font-weight-bold text-uppercase" style="font-size: 0.8rem;"><i class="fas fa-code mr-1"></i> CONTEÚDO DO SMS</label>
                        <div class="p-2 border rounded shadow-inner" style="background: rgba(255,255,255,0.05); color: #00ff00; font-family: 'Courier New', monospace; font-size: 1.15rem; min-height: 80px; display: flex; align-items: center; justify-content: center; text-align: center; letter-spacing: 1px;">
                            ${data.command_template}
                        </div>
                        <div class="mt-2 text-right">
                             <small class="text-muted" style="font-size: 0.65rem;">Bytes Estimados: ${data.command_template.length} | Caracteres: ${data.command_template.length}</small>
                        </div>
                    </div>
                </div>`
        });
    });

    /**
     * 🛠️ EDIÇÃO TÁTICA: AJAX POPUP
     */
    $('.btn-edit-command').on('click', function() {
        const id = $(this).data('id');
        const data = globalCommands[id];

        Swal.fire({
            title: '<i class="fas fa-tools mr-2 text-warning"></i> EDITAR SMS',
            width: '500px',
            showCancelButton: true,
            confirmButtonText: 'SALVAR ALTERAÇÕES',
            confirmButtonColor: '#28a745',
            html: `
                <div class="text-left px-2">
                    <div class="row mb-3">
                        <div class="col-8">
                             <label class="text-muted mb-1 d-block font-weight-bold text-uppercase" style="font-size: 0.8rem;">Descrição Técnica</label>
                             <input type="text" id="edit_description" class="form-control font-weight-bold" value="${data.description}" style="border-radius: 8px; height: 45px; font-size: 1.1rem;">
                        </div>
                        <div class="col-4">
                             <label class="text-muted mb-1 d-block font-weight-bold text-uppercase" style="font-size: 0.8rem;">Ordem</label>
                             <input type="number" id="edit_order" class="form-control font-weight-bold" value="${data.execution_order}" style="border-radius: 8px; height: 45px; font-size: 1.1rem;">
                        </div>
                    </div>
                    
                    <div class="form-group mb-0">
                         <label class="text-muted mb-1 d-block font-weight-bold text-uppercase" style="font-size: 0.8rem;">Template SMS</label>
                         <textarea id="edit_template" class="form-control font-family-monospace" rows="4" style="border-radius: 8px; background: #f8f9fa; font-size: 1.1rem;">${data.command_template}</textarea>
                         <small class="text-muted" style="font-size: 0.75rem;">Atenção: A sintaxe deve ser exata conforme o manual do hardware.</small>
                    </div>
                </div>`,
            preConfirm: () => {
                const description = $('#edit_description').val();
                const execution_order = $('#edit_order').val();
                const command_template = $('#edit_template').val();

                if (!description || !command_template) {
                    Swal.showValidationMessage('Preencha os campos obrigatórios!');
                    return false;
                }

                return { description, execution_order, command_template };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/device-commands/${id}`,
                    type: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        ...result.value
                    },
                    success: function(response) {
                        Swal.fire('SUCESSO!', response.message, 'success').then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('ERRO!', 'Falha ao atualizar o template.', 'error');
                    }
                });
            }
        });
    });

    /**
     * ⚡ INATIVAÇÃO TÁTICA (MOVER PARA LIXEIRA)
     */
    window.confirmCommandDeletion = function(id) {
        Swal.fire({
            title: '<span style="font-weight: 400; font-size: 1rem;">Tem certeza que deseja inativar este comando?</span>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'SIM, INATIVAR',
            cancelButtonText: 'CANCELAR'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/device-commands/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => {
                        Swal.fire({ icon: 'success', title: 'DESATIVADO!', text: 'Comando desativado com sucesso.', timer: 2000, showConfirmButton: false })
                            .then(() => location.reload());
                    },
                    error: (xhr) => Swal.fire('ERRO!', xhr.responseJSON.message || 'Não foi possível inativar o comando.', 'error')
                });
            }
        });
    };
</script>
@endpush
@endsection
