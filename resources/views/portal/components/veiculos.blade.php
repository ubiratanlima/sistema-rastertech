<div class="table-responsive animate__animated animate__fadeIn">
    <table class="table table-hover mb-0">
        <thead class="bg-light">
            <tr class="text-sm">
                <th class="px-4 text-left">ESTRUTURA / VEÍCULO</th>
                <th class="text-center">STATUS</th>
                <th class="d-none d-md-table-cell">MODELO</th>
                <th class="text-center">AÇÕES OPERACIONAIS</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $vehicle)
            <tr>
                <td class="align-middle px-4 py-3">
                    <div class="mercosul-plate mb-1">
                        <div class="mercosul-header">BRASIL</div>
                        <div class="mercosul-body">{{ $vehicle->plate }}</div>
                    </div>
                    <small class="text-muted d-block">{{ $vehicle->brand }} {{ $vehicle->model }}</small>
                </td>
                <td class="text-center align-middle">
                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i>MONITORADO</span>
                </td>
                <td class="align-middle d-none d-md-table-cell">
                    <span class="text-muted text-xs">{{ $vehicle->device->model_description ?? '---' }}</span>
                </td>
                <td class="text-center align-middle">
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 140px; height: 42px; border-radius: 8px; font-weight: bold; gap: 8px;" title="Ver Localização em Tempo Real">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>LOCALIZAR</span>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center py-5 text-muted">Ainda não localizamos veículos vinculados à sua central de triagem.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<script>
    // Função global acessível para o componente
    window.loadComponentAction = function(name, params = '') {
        const contentArea = $('#portal-content');
        const titleArea = $('#component-title');
        
        contentArea.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        titleArea.text('Consulta de Veículo');

        $.get(`/portal/view/${name}?${params}`, function(html) {
            contentArea.html(html).addClass('animate__animated animate__fadeIn');
        });
    }
</script>
