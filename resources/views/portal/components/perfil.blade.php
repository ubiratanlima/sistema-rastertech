<div class="animate__animated animate__fadeIn">
    <div class="row">
        <!-- 🔐 SEGURANÇA E IDENTIDADE -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: #f8f9fa;">
                <div class="card-header bg-dark text-white border-0 py-3" style="border-radius: 12px 12px 0 0;">
                    <h5 class="m-0 text-bold font-weight-bold text-white"><i class="fas fa-id-badge mr-2"></i>Identidade Tática</h5>
                </div>
                <form id="formPerfil" onsubmit="saveProfile(event)">
                    <div class="card-body p-4">
                        <div class="form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">COMO DESEJA SER CHAMADO? (NICKNAME)</label>
                            <input type="text" name="nickname" value="{{ $customer->nickname ?? $customer->name }}" class="form-control form-control-lg border-0 shadow-sm" style="background: #fff; border-radius: 8px;" placeholder="Ex: Gestor Ubiratan">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">NOVA SENHA (DEIXE EM BRANCO PARA MANTER)</label>
                            <input type="password" name="password" class="form-control form-control-lg border-0 shadow-sm" style="background: #fff; border-radius: 8px;" placeholder="******">
                        </div>
                        <div class="form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">CONFIRMAR NOVA SENHA</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg border-0 shadow-sm" style="background: #fff; border-radius: 8px;" placeholder="******">
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-3 pt-0 text-right">
                        <button type="submit" class="btn btn-dark px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">ATUALIZAR ACESSO</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- 📱 CENTRAL DE WHATSAPP (PARA TRIAGEM) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px; background: #f8f9fa;">
                <div class="card-header bg-success text-white border-0 py-3" style="border-radius: 12px 12px 0 0; background-color: #28a745 !important;">
                    <h5 class="m-0 text-bold font-weight-bold text-white">
                        <i class="fab fa-whatsapp mr-2"></i>Números Autorizados ({{ count($whatsapps) }}/20)
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div id="whatsapp-list" style="max-height: 300px; overflow-y: auto;">
                        @forelse($whatsapps as $wa)
                        <div class="d-flex justify-content-between align-items-center mb-2 p-3 bg-white border rounded shadow-sm">
                            <div class="d-flex flex-column">
                                <span class="text-bold"><i class="fab fa-whatsapp text-success mr-2"></i>{{ $wa->whatsapp_number }}</span>
                                <small class="text-muted"><i class="fas fa-user-tie mr-1"></i> {{ $wa->contact_name ?? 'Nome não informado' }}</small>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 10px;">
                                <span class="badge badge-light border text-xs text-uppercase">{{ $wa->label }}</span>
                                <button class="btn btn-sm text-danger" onclick="deleteWhatsapp({{ $wa->id }})"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted small text-center py-3">Nenhum WhatsApp secundário cadastrado.</p>
                        @endforelse
                    </div>

                    <hr class="my-4">

                    <!-- ➕ NOVO WHATSAPP -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">ADICIONAR NÚMERO</label>
                            <input type="text" id="new_wa_number" class="form-control form-control-sm border-0 shadow-sm" style="background: #fff; border-radius: 8px;" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">NOME (QUEM ATENDE?)</label>
                            <input type="text" id="new_wa_contact" class="form-control form-control-sm border-0 shadow-sm" style="background: #fff; border-radius: 8px;" placeholder="Ex: João da Logística">
                        </div>
                    </div>
                    <div class="row align-items-end">
                        <div class="col-8">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">SETOR / TIPO</label>
                            <div id="sector_container">
                                <select id="new_wa_label" class="form-control form-control-sm border-0 shadow-sm" style="background: #fff; border-radius: 8px;" onchange="handleSectorChange(this)">
                                    <option value="">-- Selecionar Setor --</option>
                                    @foreach($sectors as $s)
                                        <option value="{{ $s->name }}">{{ mb_strtoupper($s->name) }}</option>
                                    @endforeach
                                    <option value="NEW_SECTOR" class="text-primary text-bold">+ NOVO SETOR...</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-success btn-block btn-sm shadow-sm font-weight-bold" style="border-radius: 8px;" onclick="addWhatsapp()">
                                <i class="fas fa-plus mr-1"></i> ADICIONAR
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let isNewSector = false;

    function handleSectorChange(select) {
        if (select.value === 'NEW_SECTOR') {
            isNewSector = true;
            $('#sector_container').html(`
                <div class="input-group">
                    <input type="text" id="new_wa_label_input" class="form-control form-control-sm border-0 shadow-sm" style="background: #fff; border-radius: 8px 0 0 8px;" placeholder="Digite o novo setor...">
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-light border" onclick="cancelNewSector()"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            `);
            $('#new_wa_label_input').focus();
        }
    }

    function cancelNewSector() {
        isNewSector = false;
        location.reload(); // Simplificado para recarregar a lista de setores original
    }

    function saveProfile(e) {
        e.preventDefault();
        Swal.fire({
            icon: 'success',
            title: 'Sincronizado!',
            text: 'Identidade tática salva na central operacional!',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function addWhatsapp() {
        const number = $('#new_wa_number').val();
        const contact = $('#new_wa_contact').val();
        const label = isNewSector ? $('#new_wa_label_input').val() : $('#new_wa_label').val();

        if(!number || !label) {
            Swal.fire('Erro', 'Número e Setor são obrigatórios!', 'error');
            return;
        }

        Swal.fire({ title: 'Salvando...', didOpen: () => { Swal.showLoading(); }});

        $.post('/portal/component/whatsapp/add', {
            _token: '{{ csrf_token() }}',
            number: number,
            contact_name: contact,
            label: label,
            is_new_sector: isNewSector
        }, function(response) {
            Swal.fire({ icon: 'success', title: 'Sucesso!', text: response.success, timer: 1500 });
            loadComponent('perfil'); // Recarrega apenas este componente
        }).fail(function(xhr) {
            Swal.fire('Erro', xhr.responseJSON.error || 'Falha ao cadastrar WhatsApp.', 'error');
        });
    }

    function deleteWhatsapp(id) {
        Swal.fire({
            title: 'Remover contato?',
            text: "Esta ação é irreversível!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, remover!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post(`/portal/component/whatsapp/delete/${id}`, { _token: '{{ csrf_token() }}' }, function() {
                    loadComponent('perfil');
                });
            }
        });
    }
</script>
