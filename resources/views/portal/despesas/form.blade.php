@extends('layouts.app')

@section('title', 'Registrar Despesa | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DO FORMULÁRIO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.despesas.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar ao Histórico
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-cart-plus mr-2 text-orange"></i>Registrar Nova DESPESA
            </h1>
            <p class="text-muted mb-0">Preencha os dados do lançamento para o dossiê do veículo.</p>
        </div>
    </div>

    <!-- 💰 FORMULÁRIO DE LANÇAMENTO -->
    <form action="{{ route('portal.despesas.store') }}" method="POST" enctype="multipart/form-data" id="expenseForm">
        @csrf
        <input type="hidden" name="driver_id" value="{{ $driver->id }}">

        <div class="row">
            <!-- 🚛 DADOS DO VEÍCULO E CATEGORIA -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-info-circle mr-2"></i>Detalhes da Operação</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Selecione o Veículo</label>
                            <select name="vehicle_id" class="form-control form-control-lg border-0 bg-light select2" style="border-radius: 10px;" required>
                                <option value="">--- ESCOLHA O VEÍCULO ---</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}">{{ $v->plate }} ({{ $v->brand }} / {{ $v->model }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Tipo de Despesa</label>
                            <select name="type" class="form-control form-control-lg border-0 bg-light" style="border-radius: 10px;" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ strtoupper($cat) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-0">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Descrição / Local (Ex: Posto Ipiranga)</label>
                            <input type="text" name="description" class="form-control form-control-lg border-0 bg-light" 
                                   placeholder="Nome do estabelecimento ou serviço..." style="border-radius: 10px;" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 💰 FINANCEIRO & COMPROVANTE -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-dollar-sign mr-2"></i>Valores & Evidência</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Valor Total (R$)</label>
                                    <input type="text" name="amount_display" id="amount_mask" class="form-control form-control-lg border-0 bg-light font-weight-bold text-orange" 
                                           placeholder="0,00" style="border-radius: 10px; font-size: 1.5rem;" required>
                                    <input type="hidden" name="amount" id="amount_raw">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group mb-4">
                                    <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Odômetro (KM)</label>
                                    <input type="number" name="odometer" class="form-control form-control-lg border-0 bg-light font-weight-bold" 
                                           placeholder="0" step="1" style="border-radius: 10px; font-size: 1.5rem;" required>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center bg-light mx-0 p-3 rounded" style="border: 2px dashed #ddd;">
                            <div class="col-md-8">
                                <h5 class="m-0 font-weight-bold text-dark">Foto do Comprovante</h5>
                                <p class="text-muted small mb-0">Capture a nota fiscal ou recibo do serviço.</p>
                            </div>
                            <div class="col-md-4 text-right">
                                <label for="receipt_photo" class="btn btn-dark btn-block shadow-none m-0" style="border-radius: 8px;">
                                    <i class="fas fa-camera mr-2"></i> FOTOGRAFAR
                                </label>
                                <input type="file" name="receipt_photo" id="receipt_photo" class="d-none" accept="image/*">
                            </div>
                        </div>
                        <div id="preview-container" class="mt-3 text-center d-none">
                            <img id="photo-preview" src="#" class="img-fluid rounded shadow-sm border" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- 🏁 BOTÃO DE SALVAMENTO -->
                <button type="submit" class="btn btn-orange btn-lg btn-block shadow-sm py-3 text-bold text-uppercase" 
                        style="border-radius: 12px; font-size: 1.2rem; background-color: #fd7e14 !important; border: 0; color: white;">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Finalizar Lançamento de Despesa
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .text-orange { color: #fd7e14 !important; }
    .bg-light { background: #f8f9fa !important; }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
<script>
    $(function() {
        // 💰 MÁSCARA FINANCEIRA RTECH
        $('#amount_mask').maskMoney({
            prefix: 'R$ ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: true
        });

        // 📸 PREVIEW DA FOTO
        $('#receipt_photo').change(function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview').attr('src', e.target.result);
                    $('#preview-container').removeClass('d-none').addClass('animate__animated animate__zoomIn');
                }
                reader.readAsDataURL(file);
            }
        });

        // 🏗️ PREPARAR SUBMIT (Converte BRL para FLOAT)
        $('#expenseForm').submit(function() {
            const rawValue = $('#amount_mask').maskMoney('unmasked')[0];
            $('#amount_raw').val(rawValue);

            const btn = $(this).find('button[type="submit"]');
            btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> PROCESSANDO LANÇAMENTO...').prop('disabled', true);
        });
    });
</script>
@endpush
@endsection
