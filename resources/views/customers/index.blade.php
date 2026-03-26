@extends('layouts.app')

@section('title', 'Gestão de Clientes')

@section('content')
<div class="container-fluid">
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem; color: #343a40;">
                <i class="fas fa-users mr-2 text-primary"></i>Clientes & Frotas
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-users mr-1 text-primary"></i>Clientes & Frotas
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gerenciamento de proprietários e carteira de ativos.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-user-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Cliente</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success mx-0 mb-4 animate__animated animate__fadeIn shadow-sm border-0" style="border-radius: 8px;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mx-0 mb-4 animate__animated animate__shakeX shadow-sm border-0" style="border-radius: 8px;">
            <i class="fas fa-shield-alt mr-2"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Tabela de Clientes -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; border-top: 4px solid #007bff !important; margin-left: 0; margin-right: 0;">
        <div class="card-header border-0 bg-transparent px-2 px-sm-4 pt-4 pb-0 pb-sm-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title text-bold" style="font-size: 1.1rem; margin: 0;">
                <i class="fas fa-list-ul mr-2 text-primary"></i> <span style="color: #333;">Carteira de Clientes</span>
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnToggleInativos" style="border-radius: 6px; padding: 5px 10px;">
                    <i class="fas fa-eye-slash mr-1"></i> <span class="d-none d-sm-inline">Mostrar Inativos</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0 pt-3">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0" id="tableClientes">
                    <thead style="background: #343a40; color: white;">
                        <tr>
                            <th class="py-3 pl-4 text-center d-none d-md-table-cell border-0" style="width: 70px;">#</th>
                            <th class="py-3 pl-3 pl-sm-4 text-uppercase small font-weight-bold border-0" style="letter-spacing: 1px;">CLIENTE</th>
                            <th class="py-3 text-uppercase small font-weight-bold border-0 d-none d-md-table-cell text-center" style="letter-spacing: 1px;">COD. CLIENTE</th>
                            <th class="py-3 text-center text-uppercase small font-weight-bold border-0" style="letter-spacing: 1px;">
                                <span class="d-none d-md-inline">EQUIPAMENTOS RASTREADOS</span>
                                <span class="d-inline d-md-none">EQUIP.</span>
                            </th>
                            <th class="py-3 text-center text-uppercase small font-weight-bold border-0" style="letter-spacing: 1px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        @php $isInactive = $customer->device_count == 0; @endphp
                        <tr class="customer-row {{ $isInactive ? 'inactive-row d-none' : 'active-row' }}" style="border-bottom: 1px solid #f2f2f2;">
                            <td class="py-4 text-center align-middle d-none d-md-table-cell text-bold text-dark" style="font-size: 1.1rem;">{{ $customer->id }}</td>
                            <td class="py-4 pl-3 pl-sm-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-2 mr-sm-3 bg-{{ ['primary', 'success', 'info', 'warning', 'danger'][($customer->id % 5)] }}" style="width: 35px; height: 35px; min-width: 35px;">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div class="d-flex flex-column overflow-hidden">
                                        <span class="text-bold text-dark d-none d-sm-inline" title="{{ $customer->name }}" style="font-size: 1.1rem; color: #333;">
                                            {{ $customer->name }}
                                        </span>
                                        <span class="text-bold text-dark d-inline d-sm-none" title="{{ $customer->name }}" style="font-size: 1rem; color: #333;">
                                            {{ \Illuminate\Support\Str::limit($customer->name, 10, '...') }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 align-middle d-none d-md-table-cell text-center">
                                <code class="text-primary font-weight-bold" style="font-size: 0.95rem; background: #eef2f7; padding: 4px 8px; border-radius: 4px;">{{ $customer->code ?? str_pad($customer->id, 12, '0', STR_PAD_LEFT) }}</code>
                            </td>
                            <td class="py-4 text-center align-middle">
                                <span class="badge badge-pill badge-light border px-2 px-sm-4 py-2 text-primary font-weight-bold shadow-sm" style="font-size: 0.9rem; background: #f8f9fa;">
                                    <i class="fas fa-microchip mr-1 mr-sm-2" style="opacity: 0.7;"></i>
                                    {{ $customer->device_count }}<span class="d-none d-sm-inline"> unidades</span>
                                </span>
                            </td>
                            <td class="py-4 text-center align-middle">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="btn-group shadow-sm" style="border-radius: 6px; overflow: hidden;">
                                        <button class="btn btn-sm btn-info px-2 px-sm-3" title="Visualizar Detalhes"><i class="fas fa-eye"></i></button>
                                        <button class="btn btn-sm btn-primary px-2 px-sm-3" title="Editar"><i class="fas fa-edit"></i></button>
                                        
                                        <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="return confirm('Deseja realmente inativar este cliente? Esta ação preservará os dados históricos.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger px-3 d-none d-lg-inline h-100" title="Inativar Cliente">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted font-italic small">Nenhum cliente ativo no momento.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix bg-transparent border-0">
            <div class="float-right pagination-relative">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .card-indigo { border-top: 3px solid #6610f2 !important; }
    .avatar-circle {
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 16px;
        text-transform: uppercase;
    }

    /* 📏 Ajuste de Margens e Paddings para Zero Overflow */
    .card { margin-left: -5px; margin-right: -5px; } /* Ajuste fino lateral */
    
    @media (max-width: 576px) {
        .card-header { padding-left: 15px !important; padding-right: 15px !important; }
        .table td { padding-left: 10px !important; padding-right: 10px !important; }
        .table thead th { padding-left: 10px !important; padding-right: 10px !important; }
        .avatar-circle { width: 32px !important; height: 32px !important; min-width: 32px !important; font-size: 12px; }
        .name-truncate { font-size: 0.95rem !important; }
    }

    .btn-action-eye, .btn-action-edit {
        background: transparent;
        border: none;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #333;
        transition: all 0.2s;
    }

    .btn-action-edit {
        border: 2px solid #007bff !important;
        border-radius: 4px;
        color: #007bff;
        padding: 4px;
    }

    .btn-action-eye:hover { color: #007bff; transform: scale(1.1); }
    .btn-action-edit:hover { background: #007bff; color: white; }

    .inactive-row { background-color: rgba(0,0,0,0.02); opacity: 0.7; }
</style>
@endsection
