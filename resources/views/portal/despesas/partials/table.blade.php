<div class="table-responsive">
    <table class="table table-hover align-middle m-0 print-break-inside-avoid">
        <thead class="bg-light border-bottom">
            <tr>
                <th class="py-3 px-4 text-muted small text-uppercase" style="width: 120px;">Data</th>
                <th class="py-3 text-muted small text-uppercase">Veículo</th>
                <th class="py-3 text-muted small text-uppercase">Categoria</th>
                <th class="py-3 text-muted small text-uppercase">Descrição</th>
                <th class="py-3 text-muted small text-uppercase text-right">Odômetro</th>
                <th class="py-3 px-4 text-muted small text-uppercase text-right">Valor (R$)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expensesList as $exp)
            <tr class="animate__animated animate__fadeInUp animate__faster border-bottom print-break-inside-avoid">
                <td class="py-3 px-4 align-middle">
                    <div class="d-flex flex-column text-muted">
                        <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $exp->created_at->format('d/m/Y') }}</span>
                        <small style="font-size: 0.75rem;">{{ $exp->created_at->format('H:i') }}h</small>
                    </div>
                </td>
                <td class="align-middle">
                    <div class="d-flex align-items-center">
                        <div class="bg-light p-2 rounded mr-3 text-muted d-print-none">
                            <i class="fas fa-truck text-teal"></i>
                        </div>
                        <div>
                            <span class="d-block font-weight-bold">{{ $exp->vehicle->plate ?? 'N/A' }}</span>
                            <small class="text-muted d-print-none">{{ $exp->vehicle->brand ?? '' }} / {{ $exp->vehicle->model ?? '' }}</small>
                        </div>
                    </div>
                </td>
                <td class="align-middle">
                    @php
                        $badges = [
                            'Abastecimento' => 'badge-success',
                            'Troca de Óleo' => 'badge-primary',
                            'Manutenção' => 'badge-danger',
                            'Lavagem' => 'badge-info',
                            'Pneus' => 'badge-dark',
                            'Outros Gastos' => 'badge-secondary'
                        ];
                        $badgeClass = $badges[$exp->type] ?? 'badge-secondary';
                    @endphp
                    <span class="badge {{ $badgeClass }} px-3 py-2 shadow-none text-uppercase" style="border-radius: 8px; font-size: 0.75rem;">
                        {{ $exp->type }}
                    </span>
                </td>
                <td class="align-middle" style="max-width: 250px;">
                    <span class="text-dark font-weight-bold d-block text-truncate" title="{{ $exp->description }}">{{ $exp->description }}</span>
                    @if($exp->receipt_photo)
                        <a href="{{ asset('storage/' . $exp->receipt_photo) }}" onclick="event.preventDefault(); viewPhoto(this.href, 'COMPROVANTE: {{ $exp->type }}')" class="mt-1 d-inline-block text-orange small ripple-effect d-print-none" style="cursor: zoom-in;">
                            <i class="fas fa-camera"></i> Comprovante
                        </a>
                    @endif
                </td>
                <td class="align-middle text-right">
                    <span class="font-weight-bold text-muted" style="font-size: 1.1rem;">
                        {{ number_format($exp->odometer, 0, ',', '.') }}
                    </span>
                    <small class="text-muted">KM</small>
                </td>
                <td class="py-3 px-4 align-middle text-right">
                    <span class="text-dark font-weight-bold" style="font-size: 1.2rem;">
                        R$ {{ number_format($exp->amount, 2, ',', '.') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
