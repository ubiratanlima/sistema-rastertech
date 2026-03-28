@extends('layouts.app')

@section('title', 'Lixeira Tática (Chips Desativados)')

@section('content')
<div class="container-fluid">
    <!-- ⚓ CABEÇALHO TÁTICO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-8 col-sm-6 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                <i class="fas fa-trash-restore mr-2 text-danger"></i>Lixeira Tática
            </h1>
            <p class="text-muted mb-0">Ativos desativados aguardando restauração ou exclusão definitiva.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0">
            <a href="{{ route('sim-cards.index') }}" class="btn btn-dark shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-arrow-left mr-sm-2"></i>
                <span class="d-none d-sm-inline">Voltar ao Inventário</span>
            </a>
        </div>
    </div>

    <!-- 🛠️ TABELA DE RECUPERAÇÃO -->
    <div class="card card-outline card-danger shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0">
                <i class="fas fa-history mr-2 text-danger"></i>Chips em Quarentena
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(220,53,69,0.05);">
                            <th>ID</th>
                            <th class="text-left px-4">ICCID</th>
                            <th class="text-left px-4">NÚMERO</th>
                            <th>DESATIVADO EM</th>
                            <th style="width: 250px;">AÇÕES DE GESTOR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sims as $sim)
                        <tr>
                            <td class="text-center align-middle text-muted">{{ $sim->id }}</td>
                            <td class="align-middle px-4"><code>{{ $sim->iccid }}</code></td>
                            <td class="align-middle px-4 text-primary font-weight-bold">{{ $sim->phone_number ?? '---' }}</td>
                            <td class="text-center align-middle">{{ $sim->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center align-middle">
                                <div class="d-flex justify-content-center">
                                    <form action="{{ route('sim-cards.restore', $sim->id) }}" method="POST" class="mr-2">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-success btn-sm px-3" style="border-radius: 6px; font-weight: 600;">
                                            <i class="fas fa-undo mr-1"></i> RESTAURAR
                                        </button>
                                    </form>

                                    <form action="{{ route('sim-cards.force-delete', $sim->id) }}" method="POST" onsubmit="return confirm('ATENÇÃO: A exclusão definitiva não pode ser revertida. Continuar?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 6px; font-weight: 600;">
                                            <i class="fas fa-skull-crossbones mr-1"></i> ELIMINAR
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">A lixeira está vazia.</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sims->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $sims->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
