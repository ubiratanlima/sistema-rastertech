<!-- 🛠️ MODAL: EDITOR DE CLIENTE (RESTRICTED ACCESS) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalEditCustomer" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(5px);">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
            <div class="modal-header border-0 bg-dark py-3">
                <h5 class="modal-title font-weight-bold text-white"><i class="fas fa-edit mr-2 text-warning"></i>Console de Edição: Registro Ativo</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditCustomer" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4 bg-white" style="max-height: 75vh; overflow-y: auto;">
                    
                    <div class="row">
                        <!-- 🛡️ BLOCO IDENTIDADE -->
                        <div class="col-md-8 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Nome Completo</label>
                            <input type="text" name="name" id="edit_name" class="form-control border shadow-none" required>
                        </div>
                        <div class="col-md-4 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Documento (CPF/CNPJ)</label>
                            <input type="text" name="document" id="edit_doc" class="form-control border shadow-none">
                        </div>

                        <div class="col-md-12 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Razão Social / Nome Fantasia</label>
                            <input type="text" name="company_name" id="edit_company" class="form-control border shadow-none">
                        </div>

                        <div class="col-md-12 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Email Principal de Contato</label>
                            <input type="email" name="email" id="edit_email" class="form-control border shadow-none">
                        </div>

                        <!-- 📱 CONTATO -->
                        <div class="col-md-6 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Celular / WhatsApp</label>
                            <input type="text" name="cell_phone" id="edit_cell" class="form-control border shadow-none" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-6 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Telefone Fixo</label>
                            <input type="text" name="landline_phone" id="edit_landline" class="form-control border shadow-none" placeholder="(00) 0000-0000">
                        </div>

                        <!-- 📍 LOGRADOURO -->
                        <div class="col-md-3 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">CEP</label>
                            <input type="text" name="zip_code" id="edit_zip" class="form-control border shadow-none" placeholder="00000-000">
                        </div>
                        <div class="col-md-9 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Rua / Avenida</label>
                            <input type="text" name="street" id="edit_street" class="form-control border shadow-none">
                        </div>

                        <div class="col-md-3 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Nº</label>
                            <input type="text" name="number" id="edit_number" class="form-control border shadow-none">
                        </div>
                        <div class="col-md-4 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Bairro</label>
                            <input type="text" name="neighborhood" id="edit_neigh" class="form-control border shadow-none">
                        </div>
                        <div class="col-md-5 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Cidade</label>
                            <input type="text" name="city" id="edit_city" class="form-control border shadow-none">
                        </div>

                        <!-- 🛡️ SEGURANÇA -->
                        <div class="col-md-12 form-group mb-3 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Código de Segurança</label>
                            <input type="text" name="code" id="edit_code" class="form-control border shadow-none bg-light" placeholder="Chave de Autenticação">
                        </div>

                        <div class="col-md-12 form-group mb-0 text-left">
                            <label class="small text-muted text-uppercase font-weight-bold mb-1">Notas Internas</label>
                            <textarea name="notes" id="edit_notes" class="form-control border shadow-none" rows="2"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-0 bg-white shadow-top py-3">
                    <button type="button" class="btn btn-light px-4 font-weight-bold" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-sm" style="border-radius: 8px;">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>
</div>
