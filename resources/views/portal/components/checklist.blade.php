<div class="animate__animated animate__fadeIn">
    <!-- 📋 CABEÇALHO DO TERMINAL -->
    <div class="text-center mb-4">
        <h5 class="text-bold"><i class="fas fa-clipboard-check mr-2 text-warning"></i>Vistoria Operacional</h5>
        <p class="text-muted small">Veículo: <b class="text-dark">{{ $vehicle->plate }}</b> | Motorista: <b class="text-dark">{{ auth()->user()->name ?? 'Condutor' }}</b></p>
    </div>

    <!-- 🚥 SELETOR DE TIPO (ENTRADA/SAÍDA) -->
    <div class="btn-group btn-group-toggle d-flex mb-4 shadow-sm" style="border-radius: 12px; overflow: hidden;" data-toggle="buttons">
        <label class="btn btn-light active w-100 py-3" style="border-radius: 0;">
            <input type="radio" name="type" id="type_entry" checked> <i class="fas fa-sign-in-alt mr-2"></i>ENTRADA
        </label>
        <label class="btn btn-light w-100 py-3" style="border-radius: 0;">
            <input type="radio" name="type" id="type_exit"> <i class="fas fa-sign-out-alt mr-2"></i>SAÍDA
        </label>
    </div>

    <form id="formChecklist" onsubmit="submitChecklist(event)">
        <!-- 📸 CAPTURA DE ESTADO (DNA MOBILE) -->
        <div class="row">
            <div class="col-6 mb-3">
                <label class="text-xs text-uppercase text-muted font-weight-bold">FOTO FRENTE / LATERAL</label>
                <div class="upload-box border-dashed rounded p-3 text-center bg-light" style="border: 2px dashed #ddd; cursor: pointer;" onclick="$('#photo_1').click()">
                    <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                    <span class="d-block text-xs text-muted">CLIQUE PARA FOTOGRAFAR</span>
                    <input type="file" id="photo_1" class="d-none" capture="camera" accept="image/*">
                </div>
            </div>
            <div class="col-6 mb-3">
                <label class="text-xs text-uppercase text-muted font-weight-bold">FOTO TRASEIRA / INTERIOR</label>
                <div class="upload-box border-dashed rounded p-3 text-center bg-light" style="border: 2px dashed #ddd; cursor: pointer;" onclick="$('#photo_2').click()">
                    <i class="fas fa-camera fa-2x text-muted mb-2"></i>
                    <span class="d-block text-xs text-muted">CLIQUE PARA FOTOGRAFAR</span>
                    <input type="file" id="photo_2" class="d-none" capture="camera" accept="image/*">
                </div>
            </div>
        </div>

        <!-- 📟 DADOS OPERACIONAIS -->
        <div class="form-group mb-3">
            <label class="text-xs text-uppercase text-muted font-weight-bold">QUILOMETRAGEM ATUAL (ODÔMETRO)</label>
            <input type="number" name="odometer" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: 125430" required>
        </div>

        <div class="form-group mb-4">
            <label class="text-xs text-uppercase text-muted font-weight-bold">RELATÓRIO DE ESTADO (AVARIAS / OBSERVAÇÕES)</label>
            <textarea name="notes" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" rows="3" placeholder="Descreva se algo estiver ruim no veículo..."></textarea>
        </div>

        <!-- 🚀 BOTÃO DE DISPARO -->
        <button type="submit" class="btn btn-warning btn-block btn-lg shadow-sm font-weight-bold py-3" style="border-radius: 12px; letter-spacing: 1px;">
            <i class="fas fa-cloud-upload-alt mr-2"></i>ENVIAR VISTORIA RTECH
        </button>
    </form>
</div>

<script>
    function submitChecklist(e) {
        e.preventDefault();
        alert('Enviando Dossiê de Vistoria para a Central de Triagem...');
        loadComponent('veiculos'); // Volta para a frota após o envio
    }

    // Feedback visual do upload de fotos
    $('input[type="file"]').change(function() {
        const box = $(this).parent('.upload-box');
        box.addClass('bg-success-light border-success').find('span').text('FOTO CAPTURADA ✅').addClass('text-success');
        box.find('i').removeClass('text-muted').addClass('text-success');
    });
</script>

<style>
    .bg-success-light { background-color: rgba(40, 167, 69, 0.05) !important; }
    .border-success { border-color: #28a745 !important; }
</style>
