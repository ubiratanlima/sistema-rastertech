<!-- 🛠️ MODAL: CONSOLE DE EDIÇÃO TÁTICA (RESTRICTED ACCESS) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalEditCustomer" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px);">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 20px; overflow: hidden; background: #f8fafc;">
            
            <!-- 🚩 HEADER PREMIUM -->
            <div class="modal-header border-0 bg-dark py-4 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-lg mr-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="fas fa-user-cog text-dark fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0" style="letter-spacing: -0.5px;">Console de Edição</h5>
                        <p class="small text-muted mb-0 opacity-75 text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Gestão de Portfólio e Equipes</p>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <!-- ➕ BOTÃO DE ADIÇÃO NO TOPO (SOLICITADO) -->
                    <button type="button" class="btn btn-warning btn-sm font-weight-bold px-3 py-2 mr-4 shadow-sm" onclick="showNewMemberForm()" style="border-radius: 8px;">
                        <i class="fas fa-plus-circle mr-2"></i>ADICIONAR MEMBRO
                    </button>
                    <button type="button" class="close text-white opacity-50 hover-opacity-100 transition-all ml-0" data-dismiss="modal" style="outline: none;">&times;</button>
                </div>
            </div>

            <!-- 🧭 NAVEGAÇÃO POR ABAS -->
            <div class="px-4 pt-3 bg-white border-bottom">
                <ul class="nav nav-pills nav-justified mb-0 p-1 bg-light rounded-xl" id="editTabs" role="tablist" style="border: 1px solid #e2e8f0;">
                    <li class="nav-item">
                        <a class="nav-link active font-weight-bold py-3 transition-all rounded-lg" id="tab-data-link" data-toggle="pill" href="#edit-data" role="tab">
                            <i class="fas fa-id-card mr-2"></i>DADOS PRINCIPAIS
                        </a>
                    </li>
                    <li class="nav-item pl-1">
                        <a class="nav-link font-weight-bold py-3 transition-all rounded-lg" id="tab-team-link" data-toggle="pill" href="#edit-team" role="tab" onclick="loadTeamMembers()">
                            <i class="fas fa-users-cog mr-2"></i>EQUIPE & ACESSOS
                        </a>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 bg-white" style="max-height: 65vh; overflow-y: auto;">
                <div class="tab-content" id="editTabsContent">
                    
                    <!-- 📑 ABA 1: DADOS PRINCIPAIS -->
                    <div class="tab-pane fade show active" id="edit-data" role="tabpanel">
                        <form id="formEditCustomer" method="POST">
                            @csrf @method('PUT')
                            
                            <!-- 🆔 IDENTIDADE -->
                            <div class="mb-4">
                                <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-id-badge mr-2"></i>Identidade e Registro</div>
                                <div class="row">
                                    <div class="col-md-8 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Nome Completo / Razão Social</label>
                                        <input type="text" name="name" id="edit_name" class="form-control gold-input" required placeholder="Ex: Master Transportes LTDA">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">CPF/CNPJ</label>
                                        <input type="text" name="document" id="edit_doc" class="form-control gold-input" placeholder="Apenas números">
                                    </div>
                                    <div class="col-md-12 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Nome Fantasia (Opcional)</label>
                                        <input type="text" name="company_name" id="edit_company" class="form-control gold-input" placeholder="Como o cliente é conhecido">
                                    </div>
                                </div>
                            </div>

                            <!-- 📞 COMUNICAÇÃO -->
                            <div class="mb-4">
                                <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-envelope-open-text mr-2"></i>Canais de Comunicação</div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Email(s) para Notificação <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="edit_email" class="form-control gold-input" placeholder="Ex: teste1@rtech.com, teste2@rtech.com" required>
                                        <small class="text-muted"><i class="fas fa-info-circle mr-1"></i>Separe múltiplos emails por vírgula.</small>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">WhatsApp / Celular</label>
                                        <input type="text" name="cell_phone" id="edit_cell" class="form-control gold-input" placeholder="(00) 00000-0000">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Telefone Fixo</label>
                                        <input type="text" name="landline_phone" id="edit_landline" class="form-control gold-input" placeholder="(00) 0000-0000">
                                    </div>
                                </div>
                            </div>

                            <!-- 📍 LOGÍSTICA -->
                            <div class="mb-4">
                                <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-map-marked-alt mr-2"></i>Endereço e Logística</div>
                                <div class="row">
                                    <div class="col-md-4 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">CEP</label>
                                        <input type="text" name="zip_code" id="edit_zip" class="form-control gold-input cep-lookup" data-prefix="edit_" placeholder="00000-000">
                                    </div>
                                    <div class="col-md-8 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Rua / Avenida</label>
                                        <input type="text" name="street" id="edit_street" class="form-control gold-input">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Nº</label>
                                        <input type="text" name="number" id="edit_number" class="form-control gold-input">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Bairro</label>
                                        <input type="text" name="neighborhood" id="edit_neigh" class="form-control gold-input">
                                    </div>
                                    <div class="col-md-5 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Cidade</label>
                                        <input type="text" name="city" id="edit_city" class="form-control gold-input">
                                    </div>
                                </div>
                            </div>

                            <!-- 🛡️ SEGURANÇA -->
                            <div class="mb-0">
                                <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-shield-alt mr-2"></i>Configurações de Segurança</div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Código de Segurança Operacional</label>
                                        <input type="text" name="code" id="edit_code" class="form-control gold-input font-weight-bold text-primary" style="background: #f1f5f9; font-size: 1.2rem;">
                                    </div>
                                    <div class="col-md-12 form-group mb-0">
                                        <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Notas Internas</label>
                                        <textarea name="notes" id="edit_notes" class="form-control gold-input" rows="2" style="height: auto;"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <!-- 👥 ABA 2: EQUIPE E ACESSOS (RECONSTRUÇÃO REAL) -->
                    <div class="tab-pane fade" id="edit-team" role="tabpanel">
                        
                        <!-- SEÇÃO: ADMINISTRATIVO -->
                        <div class="mb-5">
                            <div class="small text-info font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;">
                                <i class="fas fa-user-shield mr-2"></i>Gestão Administrativa (Operadores)
                            </div>
                            <div id="list-operators" class="row mx-0">
                                <div class="col-12 text-center py-4 text-muted small border rounded-lg border-dashed">Nenhum operador administrativo.</div>
                            </div>
                        </div>

                        <!-- SEÇÃO: MOTORISTAS -->
                        <div>
                            <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;">
                                <i class="fas fa-truck-moving mr-2"></i>Corpo de Motoristas
                            </div>
                            <div id="list-drivers" class="row mx-0">
                                <div class="col-12 text-center py-4 text-muted small border rounded-lg border-dashed">Nenhum motorista cadastrado.</div>
                            </div>
                        </div>

                    </div>

                    </div>

                </div>

                <!-- 🏁 RODAPÉ TÁTICO -->
                <div class="modal-footer border-0 bg-light py-4 px-4">
                    <button type="button" class="btn btn-white border px-4 font-weight-bold shadow-sm" data-dismiss="modal" style="border-radius: 10px;">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-lg transform-hover" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-2"></i>SALVAR ALTERAÇÕES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .gold-input { height: 48px; border-radius: 10px !important; border: 1px solid #e2e8f0; font-size: 0.95rem; transition: all 0.2s; background: #fff; }
    .gold-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 131, 246, 0.1); background: #fff; }
    .tiny-text { font-size: 0.65rem; letter-spacing: 1.2px; }
    .nav-pills .nav-link.active { background: #fff !important; color: #3b82f6 !important; shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .nav-pills .nav-link { color: #64748b; background: transparent; }
    .rounded-xl { border-radius: 12px; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .transition-all { transition: all 0.3s ease; }
    .hover-opacity-100:hover { opacity: 1 !important; }
    .transform-hover:hover { transform: translateY(-1px); }
</style>

