@extends('layouts.app')

@section('title', 'Acesso Validado | Rastertech')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="card shadow-lg border-0 animate__animated animate__zoomIn" style="border-radius: 20px; max-width: 500px; width: 100%; overflow: hidden;">
        <div class="card-header bg-teal text-white text-center py-4 border-0" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
            <i class="fas fa-check-circle fa-5x mb-3 animate__animated animate__bounceInDown animate__delay-1s"></i>
            <h2 class="font-weight-bold mb-0">ACESSO ATIVADO!</h2>
        </div>
        <div class="card-body p-5 text-center bg-white">
            <h4 class="text-dark font-weight-bold mb-3">{{ $subUser->name }}</h4>
            <p class="text-muted mb-4">Sua credencial de acesso ao portal e aplicativos foi validada com sucesso pela Central Rastertech.</p>
            
            <div class="bg-light p-3 rounded border mb-4" style="border-radius: 12px !important;">
                <div class="small text-muted font-weight-bold text-uppercase mb-1">USUÁRIO DE ACESSO</div>
                <div class="h5 font-weight-bold text-teal mb-0">{{ $subUser->external_username }}</div>
            </div>

            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 10px; background-color: rgba(23, 162, 184, 0.1); color: #0c5460;">
                <i class="fas fa-info-circle mr-2"></i> Você já pode utilizar estas credenciais para entrar no sistema ou aplicativo.
            </div>

            <a href="{{ route('customer-sub-users.index') }}" class="btn btn-teal btn-lg btn-block shadow-sm font-weight-bold py-3" style="border-radius: 12px; background-color: #20c997; color: white;">
                <i class="fas fa-arrow-left mr-2"></i> VOLTAR PARA CREDENCIAIS
            </a>
        </div>
        <div class="card-footer bg-light border-0 py-3 text-center">
            <small class="text-muted font-weight-bold" style="letter-spacing: 1px;">RASTERTECH - SEGURANÇA E TECNOLOGIA</small>
        </div>
    </div>
</div>

<style>
    .bg-teal { background-color: #20c997 !important; }
    .text-teal { color: #20c997 !important; }
    .btn-teal:hover { filter: brightness(1.1); transform: translateY(-2px); transition: all 0.2s; }
</style>
@endsection
