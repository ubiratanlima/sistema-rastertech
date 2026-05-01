@extends('layouts.app')

@section('title', 'Comandos SMS | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-terminal mr-2 text-indigo"></i>Comandos SMS
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Biblioteca tática de scripts para configuração de hardware e automação.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL (Padrão Ouro) -->
    <div class="card card-outline shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #6610f2;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-indigo"></i>Biblioteca de Configuração
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="{{ route('device-commands.index') }}" method="GET" class="d-flex align-items-center">
                    
                    <!-- 🔍 PESQUISAR -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Buscar comando..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search text-indigo"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO -->
                    <div class="ml-4 d-flex align-items-center border-right pr-4">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 130px; font-weight: bold; border-radius: 6px;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ CADASTRO EM LOTE -->
                    <button type="button" class="btn btn-sm btn-indigo ml-4 px-3 font-weight-bold shadow-sm text-white" 
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center; background-color: #6610f2;" 
                            data-toggle="modal" data-target="#modalBatchCreate">
                        <i class="fas fa-plus-circle mr-2"></i> CADASTRAR EM LOTE
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="commandsTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 1rem;">
                            <th style="width: 80px;">ID</th>
                            <th class="text-left px-4">MODELO DE RASTREADOR / HARDWARE</th>
                            <th style="width: 200px;">FABRICANTE</th>
                            <th style="width: 150px;">SCRIPTS</th>
                            <th style="width: 180px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commandsGrouped as $modelId => $commands)
                            @php $model = $commands->first()->deviceModel; @endphp
                            <!-- 🏁 LINHA MASTER (MODELO) -->
                            <tr style="transition: background 0.3s; height: 70px;">
                                <td class="align-middle text-center text-muted font-weight-bold small cursor-pointer" data-toggle="collapse" data-target="#row-model-{{ $modelId }}">
                                    {{ $modelId }}
                                </td>
                                <td class="align-middle px-4 cursor-pointer" data-toggle="collapse" data-target="#row-model-{{ $modelId }}">
                                    <div class="d-flex align-items-center">
                                        <div class="mr-3 bg-indigo text-white rounded-circle d-flex align-items-center justify-content-center font-weight-bold shadow-sm" style="width: 38px; height: 38px; background: #6610f2;">
                                            {{ substr($model->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark" style="font-size: 11pt;">{{ $model->name }}</div>
                                            <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 7.5pt;">CÓDIGO DE ENGENHARIA: #MOD-{{ str_pad($modelId, 3, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle cursor-pointer font-weight-bold text-dark" data-toggle="collapse" data-target="#row-model-{{ $modelId }}">
                                    <span class="text-muted small text-uppercase d-block" style="font-size: 0.65rem;">MARCA</span>
                                    {{ $model->manufacturer }}
                                </td>
                                <td class="text-center align-middle cursor-pointer" data-toggle="collapse" data-target="#row-model-{{ $modelId }}">
                                    <span class="badge badge-light border px-3 py-1 font-weight-bold" style="border-radius: 20px;">
                                        {{ $commands->count() }} itens
                                    </span>
                                </td>
                                <td class="text-center align-middle px-4">
                                    <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                        <button class="btn btn-light btn-square-sm border-right btn-edit-batch" 
                                                data-model-id="{{ $modelId }}" 
                                                data-model-name="{{ $model->name }}"
                                                title="Sincronizar Lote">
                                            <i class="fas fa-tools text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-square-sm" data-toggle="collapse" data-target="#row-model-{{ $modelId }}" title="Ver Scripts">
                                            <i class="fas fa-chevron-down text-muted"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- 🛠️ DETALHE ACORDEÃO (LISTA DE COMANDOS) -->
                            <tr class="detail-row">
                                <td colspan="5" class="p-0 border-0">
                                    <div id="row-model-{{ $modelId }}" class="collapse {{ $search ? 'show' : '' }}" data-parent="#commandsTable">
                                        <div class="p-4 shadow-inner" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
                                            <div class="bg-white border rounded shadow-sm overflow-hidden" style="border-radius: 12px !important;">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead>
                                                        <tr class="bg-light text-muted small font-weight-bold text-uppercase" style="border-bottom: 1px solid #eee;">
                                                            <th class="px-4 py-2" style="width: 80px;">ORDEM</th>
                                                            <th class="py-2">DESCRIÇÃO DO COMANDO</th>
                                                            <th class="py-2">SINTAXE SMS</th>
                                                            <th class="py-2 text-right px-4">AÇÕES</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($commands as $cmd)
                                                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                                                <td class="align-middle text-center font-weight-bold text-muted small">#{{ $cmd->execution_order }}</td>
                                                                <td class="align-middle py-2 font-weight-bold text-dark">{{ $cmd->description }}</td>
                                                                <td class="align-middle py-2">
                                                                    <code class="text-indigo font-weight-bold">{{ $cmd->command_template }}</code>
                                                                </td>
                                                                <td class="align-middle text-right px-4">
                                                                    @if(!$cmd->deleted_at)
                                                                        <button class="btn btn-xs btn-outline-danger border-0" onclick="deleteSingleCommand({{ $cmd->id }})" title="Inativar">
                                                                            <i class="fas fa-times-circle"></i>
                                                                        </button>
                                                                    @else
                                                                        <form action="{{ route('device-commands.restore', $cmd->id) }}" method="POST" class="d-inline">
                                                                            @csrf @method('PUT')
                                                                            <button class="btn btn-xs btn-success px-2 py-0 font-weight-bold" style="font-size: 0.6rem;">REATIVAR</button>
                                                                        </form>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-terminal fa-4x mb-3 opacity-10"></i><br>
                                    Nenhuma configuração localizada nesta visão estratégica.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 🏗️ MODAL: CADASTRO EM LOTE -->
<div class="modal fade" id="modalBatchCreate" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white border-0 py-3 shadow-sm" style="background: #6610f2;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-layer-group mr-2"></i>Cadastro em Lote de Hardware</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row mb-4">
                    <div class="col-md-6 mx-auto text-center">
                        <label class="text-uppercase text-muted font-weight-bold small mb-2">MODELO DE RASTREADOR</label>
                        <select id="create_model_id" class="form-control form-control-lg border-0 shadow-sm text-center font-weight-bold" style="border-radius: 10px; height: 50px; color: #6610f2;">
                            <option value="">--- ESCOLHER HARDWARE ---</option>
                            @foreach($deviceModels as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->manufacturer }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="table-responsive bg-white rounded shadow-sm border">
                    <table class="table table-borderless mb-0" id="tableBatchCreate">
                        <thead class="bg-dark text-white text-xs text-uppercase">
                            <tr>
                                <th style="width: 100px;" class="py-3 px-4">ORDEM</th>
                                <th class="py-3">DESCRIÇÃO DO COMANDO</th>
                                <th class="py-3">SINTAXE SMS</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBatchCreate"></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-indigo btn-block mt-3 font-weight-bold py-3" onclick="addRow('Create')" style="border-style: dashed; border-width: 2px; border-radius: 10px;">
                    <i class="fas fa-plus-circle mr-2"></i> ADICIONAR LINHA DE COMANDO
                </button>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                <button type="button" class="btn btn-indigo px-5 shadow-sm font-weight-bold py-2 text-white" style="border-radius: 10px; background: #6610f2;" onclick="saveBatchCreate()">
                    <i class="fas fa-save mr-2"></i> SALVAR BIBLIOTECA
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ✏️ MODAL: EDIÇÃO EM LOTE -->
<div class="modal fade" id="modalBatchEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-warning text-white border-0 py-3 shadow-sm">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-tools mr-2"></i>Sincronizar Lote: <span id="edit_model_name_label"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 bg-light">
                <input type="hidden" id="edit_model_id">
                <div class="table-responsive bg-white rounded shadow-sm border">
                    <table class="table table-borderless mb-0">
                        <thead class="bg-dark text-white text-xs text-uppercase">
                            <tr>
                                <th style="width: 100px;" class="py-3 px-4">ORDEM</th>
                                <th class="py-3">DESCRIÇÃO</th>
                                <th class="py-3">SINTAXE SMS</th>
                                <th style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="tbodyBatchEdit"></tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-warning btn-block mt-3 font-weight-bold py-3" onclick="addRow('Edit')" style="border-style: dashed; border-width: 2px; border-radius: 10px; color: #856404;">
                    <i class="fas fa-plus-circle mr-2"></i> ADICIONAR NOVO COMANDO AO MODELO
                </button>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                <button type="button" class="btn btn-warning text-white px-5 shadow-sm font-weight-bold py-2" style="border-radius: 10px;" onclick="saveBatchEdit()">
                    <i class="fas fa-sync-alt mr-2"></i> ATUALIZAR MODELO
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let removedIds = [];

    window.addRow = function(type, data = {}) {
        const tbody = $(`#tbodyBatch${type}`);
        const rowCount = tbody.find('tr').length;
        const html = `
            <tr class="animate__animated animate__fadeIn border-bottom">
                <td class="px-4 py-3">
                    <input type="hidden" class="cmd-id" value="${data.id || ''}">
                    <input type="number" class="form-control text-center font-weight-bold cmd-order" value="${data.execution_order || (rowCount + 1)}" style="border-radius: 8px; background: #f8f9fa;">
                </td>
                <td class="py-3">
                    <input type="text" class="form-control font-weight-bold cmd-desc" value="${data.description || ''}" placeholder="Nome do Comando" style="border-radius: 8px; background: #f8f9fa;">
                </td>
                <td class="py-3">
                    <input type="text" class="form-control font-family-monospace cmd-template" value="${data.command_template || ''}" placeholder="RESET#" style="border-radius: 8px; background: #f8f9fa; color: #6610f2;">
                </td>
                <td class="text-right py-3 pr-4">
                    <button class="btn btn-outline-danger btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="removeRow(this, '${type}', ${data.id || 0})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.append(html);
    }

    window.removeRow = function(btn, type, id) {
        if (id) removedIds.push(id);
        $(btn).closest('tr').remove();
    }

    // 💾 SALVAR CADASTRO EM LOTE
    window.saveBatchCreate = function() {
        const btn = $('#modalBatchCreate .btn-indigo');
        const modelId = $('#create_model_id').val();
        
        if (!modelId) return Swal.fire('Atenção', 'Selecione o modelo de rastreador!', 'warning');

        const commands = [];
        $('#tbodyBatchCreate tr').each(function() {
            const desc = $(this).find('.cmd-desc').val();
            const template = $(this).find('.cmd-template').val();
            if (desc && template) {
                commands.push({
                    execution_order: $(this).find('.cmd-order').val(),
                    description: desc,
                    command_template: template
                });
            }
        });

        if (commands.length === 0) return Swal.fire('Atenção', 'Preencha pelo menos um comando completo!', 'warning');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> SALVANDO...');

        $.ajax({
            url: "{{ route('device-commands.batch-store') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                device_model_id: modelId,
                commands: commands
            },
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'BIBLIOTECA CRIADA', text: res.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-save mr-2"></i> SALVAR BIBLIOTECA');
                Swal.fire('ERRO', xhr.responseJSON?.message || 'Falha ao processar requisição em lote.', 'error');
            }
        });
    }

    // ✏️ ABRIR EDIÇÃO EM LOTE
    $('.btn-edit-batch').click(function(e) {
        e.stopPropagation();
        const modelId = $(this).data('model-id');
        const modelName = $(this).data('model-name');
        
        $('#edit_model_id').val(modelId);
        $('#edit_model_name_label').text(modelName);
        $('#tbodyBatchEdit').empty();
        removedIds = [];

        $.get(`{{ url('/device-commands/by-model') }}/${modelId}`, function(commands) {
            if (commands.length > 0) {
                commands.forEach(cmd => addRow('Edit', cmd));
            } else {
                addRow('Edit');
            }
            $('#modalBatchEdit').modal('show');
        });
    });

    // 🔄 SALVAR EDIÇÃO EM LOTE
    window.saveBatchEdit = function() {
        const btn = $('#modalBatchEdit .btn-warning');
        const modelId = $('#edit_model_id').val();
        const commands = [];
        
        $('#tbodyBatchEdit tr').each(function() {
            const desc = $(this).find('.cmd-desc').val();
            const template = $(this).find('.cmd-template').val();
            if (desc && template) {
                commands.push({
                    id: $(this).find('.cmd-id').val(),
                    execution_order: $(this).find('.cmd-order').val(),
                    description: desc,
                    command_template: template
                });
            }
        });

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> SINCRONIZANDO...');

        $.ajax({
            url: `{{ url('/device-commands/batch') }}/${modelId}`,
            method: 'PUT',
            data: {
                _token: "{{ csrf_token() }}",
                commands: commands,
                removed_ids: removedIds
            },
            success: function(res) {
                Swal.fire({ icon: 'success', title: 'SINCRONIZADO', text: res.message, timer: 2000, showConfirmButton: false }).then(() => location.reload());
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fas fa-sync-alt mr-2"></i> ATUALIZAR MODELO');
                Swal.fire('FALHA NA SINCRONIZAÇÃO', xhr.responseJSON?.message || 'Erro ao atualizar os comandos do modelo.', 'error');
            }
        });
    }

    window.deleteSingleCommand = function(id) {
        Swal.fire({
            title: 'Inativar comando?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, inativar'
        }).then((res) => {
            if (res.isConfirmed) {
                $.ajax({
                    url: `/device-commands/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: () => location.reload()
                });
            }
        });
    }

    $('#modalBatchCreate').on('shown.bs.modal', function() {
        if ($('#tbodyBatchCreate tr').length === 0) addRow('Create');
    });
</script>
@endpush

<style>
    .btn-square-sm { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 6px; }
    .text-indigo { color: #6610f2 !important; }
    .btn-indigo { background: #6610f2; color: white; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .transition-icon { transition: transform 0.3s ease; }
    tr[aria-expanded="true"] .transition-icon { transform: rotate(180deg); color: #6610f2 !important; }
    code { font-family: 'Courier New', Courier, monospace; letter-spacing: 0.5px; }
</style>
@endsection
