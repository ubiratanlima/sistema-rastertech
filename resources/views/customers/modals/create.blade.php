<!-- 📦 MODAL: NOVO CLIENTE (FLUXO INTEGRADO RASTERTECH) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalCreateCustomer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i> Novo Cliente & Frota Inicial
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            
            <form action="{{ route('customers.store') }}" method="POST" id="formCreateCustomer">
                @csrf
                <input type="hidden" name="vehicles" id="create_vehicles_json">
                
                <div class="modal-body p-0">
                    <!-- 🧭 NAVEGAÇÃO DE ABAS -->
                    <ul class="nav nav-tabs border-0 bg-light" id="createTabs" role="tablist">
                        <li class="nav-item flex-fill">
                            <a class="nav-link active border-0 text-center py-3 font-weight-bold text-uppercase" id="tab-identity-link" data-toggle="tab" href="#tab-identity" role="tab" style="letter-spacing: 1px; color: #64748b;">
                                <i class="fas fa-id-card mr-2"></i> 1. Identidade
                            </a>
                        </li>
                        <li class="nav-item flex-fill">
                            <a class="nav-link border-0 text-center py-3 font-weight-bold text-uppercase" id="tab-fleet-link" data-toggle="tab" href="#tab-fleet" role="tab" style="letter-spacing: 1px; color: #64748b;">
                                <i class="fas fa-truck-moving mr-2"></i> 2. Frota Inicial
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content p-4" id="createTabsContent" style="min-height: 450px;">
                        <!-- 🏷️ ABA 1: IDENTIDADE -->
                        <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Nome do Cliente / Responsável <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control gold-input" required placeholder="Ex: João da Silva">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Razão Social / Nome Fantasia</label>
                                    <input type="text" name="company_name" class="form-control gold-input" placeholder="Ex: Transportes S.A.">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Email Principal <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control gold-input" required placeholder="contato@exemplo.com">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">CPF / CNPJ</label>
                                    <input type="text" name="document" class="form-control gold-input" placeholder="00.000.000/0000-00">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Código de Segurança (RTECH)</label>
                                    <input type="text" name="code" class="form-control gold-input bg-light font-weight-bold text-primary" placeholder="Gerado automaticamente se vazio">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Celular / WhatsApp</label>
                                    <input type="text" name="cell_phone" class="form-control gold-input" placeholder="(00) 00000-0000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Telefone Fixo</label>
                                    <input type="text" name="landline_phone" class="form-control gold-input" placeholder="(00) 0000-0000">
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">CEP</label>
                                    <input type="text" name="zip_code" id="create_zip" class="form-control gold-input cep-lookup" data-prefix="create_" placeholder="00000-000">
                                </div>
                                <div class="col-md-7 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Logradouro / Endereço</label>
                                    <input type="text" name="street" id="create_street" class="form-control gold-input">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Número</label>
                                    <input type="text" name="number" id="create_number" class="form-control gold-input">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Bairro</label>
                                    <input type="text" name="neighborhood" id="create_neigh" class="form-control gold-input">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Cidade</label>
                                    <input type="text" name="city" id="create_city" class="form-control gold-input">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Complemento</label>
                                    <input type="text" name="complement" class="form-control gold-input">
                                </div>
                            </div>
                        </div>

                        <!-- 🚛 ABA 2: FROTA -->
                        <div class="tab-pane fade" id="tab-fleet" role="tabpanel">
                            <div class="row mb-4 bg-light p-3 rounded mx-0 border">
                                <div class="col-md-12">
                                    <h6 class="font-weight-bold text-primary mb-3"><i class="fas fa-plus-circle mr-2"></i>Adicionar Veículo à Frota</h6>
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Placa</label>
                                    <input type="text" id="temp_plate" class="form-control gold-input text-uppercase" placeholder="ABC-1234">
                                </div>
                                <div class="col-md-3">
                                    <label class="font-weight-bold small text-muted text-uppercase">Marca</label>
                                    <input type="text" id="temp_brand" class="form-control gold-input" placeholder="Ex: VW, Mercedes">
                                </div>
                                <div class="col-md-4">
                                    <label class="font-weight-bold small text-muted text-uppercase">Modelo</label>
                                    <input type="text" id="temp_model" class="form-control gold-input" placeholder="Ex: Gol, Actros">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary btn-block font-weight-bold" onclick="addVehicleToCreateList()" style="height: 45px; border-radius: 10px;">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-sm table-hover border" id="tableCreateVehicles">
                                    <thead class="bg-light">
                                        <tr class="text-uppercase small font-weight-bold text-muted">
                                            <th class="px-3 py-2">Placa</th>
                                            <th class="py-2">Marca</th>
                                            <th class="py-2">Modelo</th>
                                            <th class="text-center py-2" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="listCreateVehicles">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small opacity-50">
                                                <i class="fas fa-truck-moving fa-2x mb-2 d-block"></i>
                                                Nenhum veículo adicionado ainda.
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light border-0 py-3">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold px-4 shadow-none" data-dismiss="modal" style="border-radius: 10px;">CANCELAR</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-5 shadow-sm" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-2"></i> CONCLUIR E SALVAR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gold-input { border-radius: 10px; height: 45px; border: 1px solid #dee2e6; transition: all 0.2s; background: #fff; }
    .gold-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); outline: none; }
    .nav-tabs .nav-link.active { background-color: #fff !important; border-bottom: 3px solid #3b82f6 !important; color: #3b82f6 !important; }
    .border-dashed { border-style: dashed !important; }
</style>

<script>
    if (typeof tempVehicles === 'undefined') {
        var tempVehicles = [];
    }

    window.addVehicleToCreateList = () => {
        const plate = $('#temp_plate').val().trim().toUpperCase();
        const brand = $('#temp_brand').val().trim();
        const model = $('#temp_model').val().trim();

        if (plate.length < 7) {
            Swal.fire({ icon: 'warning', title: 'Placa Inválida', text: 'Informe uma placa válida.' });
            return;
        }

        tempVehicles.push({ plate, brand, model });
        $('#temp_plate').val('').focus();
        $('#temp_brand').val('');
        $('#temp_model').val('');
        renderCreateVehicles();
    };

    window.removeVehicleFromCreateList = (index) => {
        tempVehicles.splice(index, 1);
        renderCreateVehicles();
    };

    function renderCreateVehicles() {
        const container = $('#listCreateVehicles');
        if (tempVehicles.length === 0) {
            container.html('<tr><td colspan="4" class="text-center py-4 text-muted small opacity-50"><i class="fas fa-truck-moving fa-2x mb-2 d-block"></i>Nenhum veículo adicionado ainda.</td></tr>');
            return;
        }

        let html = '';
        tempVehicles.forEach((v, i) => {
            html += `
                <tr class="animate__animated animate__fadeIn">
                    <td class="align-middle px-3 font-weight-bold text-primary" style="font-size: 1.1rem;">${v.plate}</td>
                    <td class="align-middle text-muted text-uppercase small">${v.brand || '---'}</td>
                    <td class="align-middle text-muted text-uppercase small">${v.model || '---'}</td>
                    <td class="text-center align-middle">
                        <button type="button" class="btn btn-xs text-danger shadow-none" onclick="removeVehicleFromCreateList(${i})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        container.html(html);
        $('#create_vehicles_json').val(JSON.stringify(tempVehicles));
    }

    // Busca CEP para novo cliente removida daqui e centralizada no index

    $('#formCreateCustomer').on('submit', function() {
        $('#create_vehicles_json').val(JSON.stringify(tempVehicles));
        Swal.fire({ title: 'Salvando Cliente...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    });
</script>
