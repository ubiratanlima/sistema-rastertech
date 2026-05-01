<!-- 📦 MODAL: NOVO VEÍCULO (DOSSIÊ TÁTICO RASTERTECH) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalCreateVehicle" tabindex="-1" role="dialog" aria-hidden="true" style="backdrop-filter: blur(8px);">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 20px; overflow: hidden; background: #f8fafc;">
            
            <!-- 🚩 HEADER PREMIUM -->
            <div class="modal-header border-0 bg-dark py-4 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-lg mr-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="fas fa-truck text-white fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-weight-bold text-white mb-0" style="letter-spacing: -0.5px;">Integração de Ativo</h5>
                        <p class="small text-muted mb-0 opacity-75 text-uppercase" style="letter-spacing: 1px; font-size: 0.7rem;">Cadastro de Veículo e Documentação</p>
                    </div>
                </div>
                <button type="button" class="close text-white opacity-50 hover-opacity-100 transition-all ml-0" data-dismiss="modal" style="outline: none;">&times;</button>
            </div>

            <form action="{{ route('fleets.store') }}" method="POST" enctype="multipart/form-data" id="formCreateVehicle">
                @csrf
                <div class="modal-body p-4 bg-white" style="max-height: 70vh; overflow-y: auto;">
                    
                    <!-- 🆔 IDENTIDADE DO VEÍCULO -->
                    <div class="mb-4">
                        <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-id-badge mr-2"></i>Identidade e Registro</div>
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Placa <span class="text-danger">*</span></label>
                                <input type="text" name="plate" class="form-control gold-input text-uppercase font-weight-bold" required placeholder="ABC-1234" style="font-size: 1.2rem; letter-spacing: 1px;">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Fabricante / Marca <span class="text-danger">*</span></label>
                                <input type="text" name="brand" class="form-control gold-input" required placeholder="Ex: Mercedes, VW">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Modelo / Versão <span class="text-danger">*</span></label>
                                <input type="text" name="model" class="form-control gold-input" required placeholder="Ex: Gol 1.0">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Ano Mod/Fab</label>
                                <input type="text" name="year" class="form-control gold-input" placeholder="2024">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Cor Predominante</label>
                                <input type="text" name="color" class="form-control gold-input" placeholder="Branco">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Proprietário / Cliente <span class="text-danger">*</span></label>
                                <select name="customer_id" class="form-control gold-input select2" required>
                                    <option value="">--- SELECIONE O CLIENTE ---</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->company_name ?? $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- 🛠️ DADOS DOCUMENTAIS (RENAVAM/CHASSI) -->
                    <div class="mb-4">
                        <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-file-contract mr-2"></i>Informações Estruturais</div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">RENAVAM</label>
                                <input type="text" name="renavam" class="form-control gold-input" placeholder="00000000000">
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">CHASSI</label>
                                <input type="text" name="chassi" class="form-control gold-input" placeholder="Digite o número do chassi">
                            </div>
                        </div>
                    </div>

                    <!-- 📸 REGISTRO VISUAL (FOTOS) -->
                    <div class="mb-0">
                        <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-2" style="letter-spacing: 1px;"><i class="fas fa-camera-retro mr-2"></i>Inspeção Visual (Fotos)</div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Foto Frontal</label>
                                <div class="custom-file">
                                    <input type="file" name="photo_front" class="custom-file-input" id="create_photo_front" accept="image/*">
                                    <label class="custom-file-label gold-input d-flex align-items-center" for="create_photo_front" style="height: 48px;">Escolher arquivo</label>
                                </div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Foto Traseira / Lateral</label>
                                <div class="custom-file">
                                    <input type="file" name="photo_back" class="custom-file-input" id="create_photo_back" accept="image/*">
                                    <label class="custom-file-label gold-input d-flex align-items-center" for="create_photo_back" style="height: 48px;">Escolher arquivo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 🏁 RODAPÉ TÁTICO -->
                <div class="modal-footer border-0 bg-light py-4 px-4">
                    <button type="button" class="btn btn-white border px-4 font-weight-bold shadow-sm" data-dismiss="modal" style="border-radius: 10px;">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-5 font-weight-bold shadow-lg transform-hover" style="border-radius: 10px;">
                        <i class="fas fa-check-circle mr-2"></i>FINALIZAR CADASTRO
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
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
    .transition-all { transition: all 0.3s ease; }
    .hover-opacity-100:hover { opacity: 1 !important; }
    .transform-hover:hover { transform: translateY(-1px); }
    .custom-file-label::after { height: 46px; display: flex; align-items: center; background: #f1f5f9; border-radius: 0 10px 10px 0; border-left: 1px solid #e2e8f0; }
</style>
