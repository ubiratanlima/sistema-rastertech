@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-shield-alt mr-2"></i>AUDITORIA DE SISTEMA
                    </h5>
                    <form action="{{ route('audit.index') }}" method="GET" class="form-inline">
                        <input type="text" name="search" class="form-control form-control-sm mr-2" placeholder="Buscar IP, Evento..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-xs text-uppercase text-muted pl-4">Data/Hora</th>
                                    <th class="text-xs text-uppercase text-muted">Usuário</th>
                                    <th class="text-xs text-uppercase text-muted">Evento</th>
                                    <th class="text-xs text-uppercase text-muted">Módulo</th>
                                    <th class="text-xs text-uppercase text-muted">IP / Local</th>
                                    <th class="text-xs text-uppercase text-muted text-center">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td class="pl-4">
                                        <div class="d-flex flex-column">
                                            <span class="font-weight-bold">{{ $log->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-soft text-primary mr-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                {{ strtoupper(substr($log->user->name ?? 'S', 0, 1)) }}
                                            </div>
                                            <span class="font-weight-bold">{{ $log->user->name ?? 'Sistema' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = [
                                                'created' => 'badge-success',
                                                'updated' => 'badge-primary',
                                                'deleted' => 'badge-danger',
                                                'login'   => 'badge-info'
                                            ][$log->event] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} text-uppercase">{{ $log->event }}</span>
                                    </td>
                                    <td>
                                        <small class="text-muted text-uppercase d-block">{{ str_replace('App\\Models\\', '', $log->auditable_type) }}</small>
                                        <span class="text-dark font-weight-bold">#{{ $log->auditable_id }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark font-weight-bold"><i class="fas fa-network-wired mr-1 text-muted"></i> {{ $log->ip_address }}</span>
                                            <small class="text-muted text-truncate" style="max-width: 200px;" title="{{ $log->url }}">{{ $log->url }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-light btn-sm rounded-circle" data-toggle="modal" data-target="#modal-log-{{ $log->id }}">
                                            <i class="fas fa-eye text-primary"></i>
                                        </button>

                                        <!-- Modal de Detalhes -->
                                        <div class="modal fade" id="modal-log-{{ $log->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content border-0">
                                                    <div class="modal-header bg-dark text-white">
                                                        <h5 class="modal-title">Detalhes da Alteração #{{ $log->id }}</h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                    </div>
                                                    <div class="modal-body text-left">
                                                        <div class="row mb-4 text-center">
                                                            <div class="col-4">
                                                                <small class="text-muted d-block text-uppercase">Ação</small>
                                                                <span class="font-weight-bold text-uppercase">{{ $log->event }}</span>
                                                            </div>
                                                            <div class="col-4 border-left border-right">
                                                                <small class="text-muted d-block text-uppercase">Responsável</small>
                                                                <span class="font-weight-bold">{{ $log->user->name ?? 'Sistema' }}</span>
                                                            </div>
                                                            <div class="col-4">
                                                                <small class="text-muted d-block text-uppercase">IP de Origem</small>
                                                                <span class="font-weight-bold text-primary">{{ $log->ip_address }}</span>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            @if($log->old_values)
                                                            <div class="col-md-6">
                                                                <div class="alert alert-light border">
                                                                    <h6 class="text-danger font-weight-bold"><i class="fas fa-history mr-2"></i>VALORES ANTERIORES</h6>
                                                                    <pre class="bg-transparent m-0 p-0 text-xs" style="white-space: pre-wrap;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            <div class="{{ $log->old_values ? 'col-md-6' : 'col-12' }}">
                                                                <div class="alert alert-info-soft border">
                                                                    <h6 class="text-primary font-weight-bold"><i class="fas fa-check-circle mr-2"></i>VALORES ATUAIS / NOVOS</h6>
                                                                    <pre class="bg-transparent m-0 p-0 text-xs" style="white-space: pre-wrap;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <small class="text-muted mr-auto">Navegador: {{ $log->user_agent }}</small>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-soft { background-color: rgba(0, 123, 255, 0.1); }
    .alert-info-soft { background-color: rgba(23, 162, 184, 0.05); color: #0c5460; border-color: #bee5eb; }
    pre { font-family: 'Courier New', Courier, monospace; font-size: 0.85rem; }
    .badge { padding: 0.5em 0.8em; border-radius: 50rem; }
</style>
@endsection
