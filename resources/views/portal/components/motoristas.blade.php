<div class="animate__animated animate__fadeIn">
    <!-- 🏆 CABEÇALHO DE OPERAÇÃO MOTORISTA -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="m-0 text-bold text-teal"><i class="fas fa-id-card-alt mr-2"></i>Gestão de Condutores</h5>
        <button class="btn btn-teal text-white shadow-sm" style="border-radius: 8px; font-weight: 700; background-color: #20c997 !important;" onclick="showDriverForm()">
            <i class="fas fa-plus mr-2"></i>CADASTRAR MOTORISTA
        </button>
    </div>

    <!-- 🪪 LISTA EM ACORDEÃO (NIVEL TÁTICO) -->
    <div id="accordionDrivers" class="accordion-rastertech">
        @forelse($drivers as $driver)
        <div class="card shadow-sm border mb-3" style="border-radius: 12px; overflow: hidden; border-left: 5px solid #20c997 !important;">
            <div class="card-header bg-white py-3 px-4 {{ request('open_id') == $driver->id ? '' : 'collapsed' }}" id="heading-{{ $driver->id }}" data-toggle="collapse" data-target="#collapse-{{ $driver->id }}" style="cursor: pointer;">
                <div class="row align-items-center">
                    <div class="col-8">
                        <span class="text-bold d-block h5 m-0 text-uppercase" style="letter-spacing: 0.5px;">{{ $driver->name }}</span>
                        <div class="d-flex align-items-center mt-1">
                            <span class="badge badge-light border mr-3" style="font-size: 14px;">CAT {{ $driver->category }}</span>
                            <div class="text-muted">
                                CPF: <span class="text-dark">{{ $driver->cpf }}</span> | Vencimento: 
                                <span class="{{ ($driver->cnh_expiry && $driver->cnh_expiry < now()) ? 'text-danger font-weight-bold' : 'text-success font-weight-bold' }}">
                                    {{ $driver->cnh_expiry ? $driver->cnh_expiry->format('d/m/Y') : '---' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-right">
                        @if($driver->status == 'active')
                            <span class="badge badge-success px-3 py-2 mr-2" style="font-size: 12px;">OPERACIONAL</span>
                        @else
                            <span class="badge badge-danger px-3 py-2 mr-2" style="font-size: 12px;">BLOQUEADO</span>
                        @endif
                        
                        <button class="btn btn-warning btn-sm text-dark font-weight-bold px-3 py-1" 
                                style="border-radius: 6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" 
                                onclick="event.stopPropagation(); editDriver({{ json_encode($driver) }})">
                            <i class="fas fa-pencil-alt mr-1"></i>EDITAR
                        </button>

                        <i class="fas fa-chevron-down ml-3 text-muted"></i>
                    </div>
                </div>
            </div>

            <div id="collapse-{{ $driver->id }}" class="collapse {{ request('open_id') == $driver->id ? 'show' : '' }}" aria-labelledby="heading-{{ $driver->id }}" data-parent="#accordionDrivers">
                <div class="card-body bg-light border-top p-4">
                    <!-- 📊 BLOCO 1: DADOS DE IDENTIDADE -->
                    <div class="row mb-5">
                        <div class="col-12"><h5 class="text-bold border-bottom pb-2 mb-4 text-teal"><i class="fas fa-id-badge mr-2"></i>DADOS DE IDENTIDADE</h5></div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Nascimento</label>
                            <span class="text-dark h6">{{ $driver->birth_date ? $driver->birth_date->format('d/m/Y') : '---' }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">CPF</label>
                            <span class="text-dark h6">{{ $driver->cpf }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">RG</label>
                            <span class="text-dark h6">{{ $driver->rg }} ({{ $driver->issuer }}/{{ $driver->uf }})</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Nacionalidade</label>
                            <span class="text-dark h6 text-uppercase">{{ $driver->nationality }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Filiação</label>
                            <span class="text-dark h6 d-block">{{ $driver->mother_name }} <small>(Mãe)</small></span>
                            <span class="text-dark h6 d-block">{{ $driver->father_name }} <small>(Pai)</small></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Local de Nascimento</label>
                            <span class="text-dark h6 text-uppercase">{{ $driver->birth_place }}</span>
                        </div>
                    </div>

                    <!-- 📊 BLOCO 2: DETALHES TÉCNICOS CNH -->
                    <div class="row mb-5">
                        <div class="col-12"><h5 class="text-bold border-bottom pb-2 mb-4 text-teal"><i class="fas fa-address-card mr-2"></i>CNH</h5></div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Nº Registro</label>
                            <span class="text-dark h6">{{ $driver->cnh_number }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Data de Emissão</label>
                            <span class="text-dark h6">{{ $driver->issue_date ? $driver->issue_date->format('d/m/Y') : '---' }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Categoria Atual</label>
                            <span class="text-dark h6 font-weight-bold">CAT {{ $driver->category }}</span>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="text-muted text-uppercase d-block font-weight-bold" style="font-size: 13px;">Validade de Território</label>
                            <span class="text-dark h6 text-uppercase">{{ $driver->territory_validity }}</span>
                        </div>
                    </div>

                    <!-- 📸 BLOCO 3: IMAGENS EM ALTA -->
                    <div class="row mb-3">
                        <div class="col-md-6 mb-4">
                            <label class="text-uppercase text-muted font-weight-bold d-block mb-2" style="font-size: 13px;">CNH Digital (Frente)</label>
                            <div class="p-3 border bg-white rounded text-center shadow-sm" style="cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'" onclick="triggerDirectUpload({{ $driver->id }}, 'front')">
                                @if($driver->cnh_front_path)
                                    <img src="{{ asset('storage/' . $driver->cnh_front_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                                @else
                                    <div class="py-5 text-muted small">
                                        <i class="fas fa-camera fa-2x mb-2 d-block text-teal"></i>
                                        <span class="text-bold text-teal">CLIQUE PARA TIRAR FOTO</span><br>
                                        <small>(FRENTE DA CNH)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="text-uppercase text-muted font-weight-bold d-block mb-2" style="font-size: 13px;">CNH Digital (Verso)</label>
                            <div class="p-3 border bg-white rounded text-center shadow-sm" style="cursor: pointer; transition: 0.3s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background='#fff'" onclick="triggerDirectUpload({{ $driver->id }}, 'back')">
                                @if($driver->cnh_back_path)
                                    <img src="{{ asset('storage/' . $driver->cnh_back_path) }}" class="img-fluid rounded shadow-sm" style="max-height: 250px;">
                                @else
                                    <div class="py-5 text-muted small">
                                        <i class="fas fa-camera fa-2x mb-2 d-block text-teal"></i>
                                        <span class="text-bold text-teal">CLIQUE PARA TIRAR FOTO</span><br>
                                        <small>(VERSO DA CNH)</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="py-5 text-center text-muted">Ainda não há motoristas táticos registrados na sua frota.</div>
        @endforelse
    </div>

    <!-- 🏗️ FORMULÁRIO DE CADASTRO EXPANDIDO (NÍVEL OPERACIONAL) -->
    <div id="driverFormArea" style="display: none;">
        <div class="card shadow-sm border border-teal" style="border-radius: 12px; overflow: hidden;">
             <div class="card-header bg-teal text-white py-3">
                 <h5 class="m-0 text-bold font-weight-bold text-white"><i class="fas fa-user-plus mr-2"></i>Inscrição de Novo Condutor</h5>
             </div>
             <form id="formMotorista">
                @csrf
                <input type="hidden" name="driver_id" id="field-driver_id">
                <div class="card-body p-4">
                    <!-- 🛡️ Identidade -->
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="text-bold text-teal border-bottom pb-2 mb-4">1. DADOS DE IDENTIDADE</h5></div>
                        <div class="col-md-6 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Nome Completo <span class="text-danger">*</span></label><input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-6 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">E-mail (Login do App) <span class="text-danger">*</span></label><input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;" placeholder="exemplo@rastertech.com.br"></div>
                        <div class="col-md-6 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Senha do App (Deixe em branco para 123456)</label><input type="password" name="external_password" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;" placeholder="••••••••"></div>
                        <div class="col-md-3 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Nascimento</label><input type="date" name="birth_date" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-3 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">CPF <span class="text-danger">*</span></label><input type="text" name="cpf" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;" placeholder="000.000.000-00"></div>
                        <div class="col-md-4 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">RG</label><input type="text" name="rg" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-4 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Órgão Emissor / UF</label><input type="text" name="issuer_uf" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;" placeholder="Ex: SSP / SP"></div>
                        <div class="col-md-4 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Nacionalidade</label><input type="text" name="nationality" value="Brasileira" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-12 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Local de Nascimento</label><input type="text" name="birth_place" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;" placeholder="Cidade / Estado"></div>
                    </div>

                    <!-- 🛡️ Filiação -->
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="text-bold text-teal border-bottom pb-2 mb-4">2. FILIAÇÃO</h5></div>
                        <div class="col-md-6 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Nome da Mãe</label><input type="text" name="mother_name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-6 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Nome do Pai</label><input type="text" name="father_name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                    </div>

                    <!-- 🛡️ CNH Técnico -->
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="text-bold text-teal border-bottom pb-2 mb-4">3. CNH</h5></div>
                        <div class="col-md-4 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Número de Registro <span class="text-danger">*</span></label><input type="text" name="cnh_number" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-2 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Categoria</label><select name="category" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;">
                            <option value="A">A</option><option value="B">B</option><option value="AB">AB</option><option value="C">C</option><option value="D">D</option><option value="AD">AD</option><option value="AE">AE</option>
                        </select></div>
                        <div class="col-md-3 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Emissão <span class="text-danger">*</span></label><input type="date" name="issue_date" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                        <div class="col-md-3 mb-3"><label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Validade <span class="text-danger">*</span></label><input type="date" name="cnh_expiry" class="form-control form-control-lg border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px;"></div>
                    </div>

                    <!-- 🛡️ CNH Digital -->
                    <div class="row mb-4">
                        <div class="col-12"><h5 class="text-bold text-teal border-bottom pb-2 mb-4">4. FOTOS DA CNH</h5></div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Cnh Digital (Frente)</label>
                            <input type="file" name="cnh_front" class="form-control border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px; padding: 10px;" accept="image/*">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-muted text-uppercase mb-1" style="font-size: 13px;">Cnh Digital (Verso)</label>
                            <input type="file" name="cnh_back" class="form-control border-0 shadow-sm" style="background: #f1f3f4; border-radius: 8px; padding: 10px;" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light p-3 text-right">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-3" onclick="showDriverList()">CANCELAR</button>
                    <button type="button" class="btn btn-teal px-5 py-3 text-white shadow-sm font-weight-bold" style="border-radius: 10px; background-color: #20c997 !important; letter-spacing: 1px;" onclick="saveDriver(event)">SALVAR</button>
                </div>
             </form>
        </div>
    </div>
</div>

<script>
    function editDriver(driver) {
        // Altera o título do card para Edição
        $('#driverFormArea h5').first().html('<i class="fas fa-edit mr-2"></i>Edição de Condutor: ' + driver.name);
        
        // Popula os campos do formulário
        $('#field-driver_id').val(driver.id);
        $('input[name="name"]').val(driver.name);
        $('input[name="email"]').val(driver.email);
        $('input[name="cpf"]').val(driver.cpf);
        $('input[name="external_password"]').val(driver.external_password);
        $('input[name="rg"]').val(driver.rg);
        $('input[name="issuer_uf"]').val(driver.issuer + ' / ' + driver.uf);
        $('input[name="nationality"]').val(driver.nationality);
        $('input[name="birth_place"]').val(driver.birth_place);
        $('input[name="mother_name"]').val(driver.mother_name);
        $('input[name="father_name"]').val(driver.father_name);
        $('input[name="cnh_number"]').val(driver.cnh_number);
        $('select[name="category"]').val(driver.category);
        
        // Formata datas para o input date (YYYY-MM-DD)
        if(driver.birth_date) $('input[name="birth_date"]').val(driver.birth_date.substring(0, 10));
        if(driver.issue_date) $('input[name="issue_date"]').val(driver.issue_date.substring(0, 10));
        if(driver.cnh_expiry) $('input[name="cnh_expiry"]').val(driver.cnh_expiry.substring(0, 10));

        // Troca para o formulário
        showDriverForm();
    }

    function showDriverForm() {
        // Se for inclusão nova, reseta o form
        if(!$('#field-driver_id').val()) {
            $('#formMotorista')[0].reset();
            $('#driverFormArea h5').first().html('<i class="fas fa-user-plus mr-2"></i>Inscrição de Novo Condutor');
        }

        $('#accordionDrivers').fadeOut(200, function() {
            $('#driverFormArea').fadeIn(300);
        });
    }

    function showDriverList() {
        $('#driverFormArea').fadeOut(200, function() {
            $('#accordionDrivers').fadeIn(300);
        });
    }

    function saveDriver(e) {
        if(e) e.preventDefault();

        const name = $('input[name="name"]').val();
        const email = $('input[name="email"]').val();
        const cpf = $('input[name="cpf"]').val();
        const cnh = $('input[name="cnh_number"]').val();
        const issue = $('input[name="issue_date"]').val();
        const expiry = $('input[name="cnh_expiry"]').val();

        if (!name || !email || !cpf || !cnh || !issue || !expiry) {
            Swal.fire('ATENÇÃO', 'Preencha todos os campos obrigatórios (marcados com *) antes de prosseguir.', 'warning');
            return;
        }
        
        // 🚥 PREPARAÇÃO DOS DADOS MULTIPART
        const form = $('#formMotorista')[0];
        const formData = new FormData(form);
        
        // 🚥 ENVIO TÁTICO VIA AJAX (MODALIDADE MULTIPART)
        $.ajax({
            url: '{{ route("portal.driver.save") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'SALVO COM SUCESSO!',
                    text: 'Os dados do condutor e imagens foram atualizados.',
                    icon: 'success',
                    confirmButtonColor: '#20c997'
                }).then(() => {
                    const openId = $('#field-driver_id').val();
                    loadComponent('motoristas', openId ? 'open_id=' + openId : '');
                });
            },
            error: function(err) {
                console.error('❌ [PORTAL] ERRO NO UPLOAD:', err);
                let msg = 'Falha na comunicação com o servidor central.';
                
                if (err.status === 413) {
                    msg = 'O arquivo é muito grande para o servidor. Tente uma foto menor ou com menos resolução.';
                } else if (err.responseJSON && err.responseJSON.error) {
                    if (typeof err.responseJSON.error === 'object') {
                        msg = Object.values(err.responseJSON.error).flat().join('<br>');
                    } else {
                        msg = err.responseJSON.error;
                    }
                } else if (err.responseJSON && err.responseJSON.message) {
                    msg = err.responseJSON.message;
                }

                Swal.fire({
                    title: 'FALHA NO ENVIO',
                    html: msg, // Usar html para suportar as quebras de linha <br>
                    icon: 'error',
                    confirmButtonColor: '#d33'
                });
            }
        });
    }

    // 📸 UPLOAD DIRETO PELO CARD
    function triggerDirectUpload(driverId, side) {
        $('#hidden_upload_driver_id').val(driverId);
        $('#hidden_upload_side').val(side);
        $('#global_file_input').click();
    }

    function processDirectUpload(input) {
        if (!input.files || !input.files[0]) return;

        const driverId = $('#hidden_upload_driver_id').val();
        const side = $('#hidden_upload_side').val();
        
        const formData = new FormData();
        formData.append('driver_id', driverId);
        formData.append(side === 'front' ? 'cnh_front' : 'cnh_back', input.files[0]);
        formData.append('_token', '{{ csrf_token() }}');

        Swal.fire({
            title: 'ENVIANDO IMAGEM...',
            text: 'Aguarde a sincronização com a central Rastertech.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '{{ route("portal.driver.save") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'SUCESSO!',
                    text: 'A imagem foi vinculada ao condutor.',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    loadComponent('motoristas', 'open_id=' + driverId);
                });
            },
            error: function(err) {
                let msg = 'Falha ao enviar arquivo para a central.';
                if (err.status === 413) {
                    msg = 'Arquivo muito grande. O servidor rejeitou o envio.';
                } else if (err.responseJSON && err.responseJSON.error) {
                    if (typeof err.responseJSON.error === 'object') {
                        msg = Object.values(err.responseJSON.error).flat().join('<br>');
                    } else {
                        msg = err.responseJSON.error;
                    }
                }
                Swal.fire({
                    title: 'ERRO NO UPLOAD',
                    html: msg,
                    icon: 'error'
                });
            }
        });
    }
</script>

<!-- 🕵️ ELEMENTOS OCULTOS PARA UPLOAD TÁTICO -->
<input type="file" id="global_file_input" style="display: none;" accept="image/*" onchange="processDirectUpload(this)">
<input type="hidden" id="hidden_upload_driver_id">
<input type="hidden" id="hidden_upload_side">
