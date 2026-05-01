<div class="table-responsive">
    <table class="table table-hover align-middle m-0 print-break-inside-avoid">
        <thead class="bg-light border-bottom">
            <tr>
                <th class="py-3 px-4 text-muted small text-uppercase" style="width: 100px;">Data</th>
                <th class="py-3 text-muted small text-uppercase text-center">Veículo</th>
                <th class="py-3 text-muted small text-uppercase d-none d-md-table-cell">Categoria</th>
                
                <!-- 📝 HEADER DESCRIÇÃO RESPONSIVO -->
                <th class="py-3 text-muted small text-uppercase d-none d-md-table-cell">Descrição</th>
                <th class="py-3 text-muted small text-uppercase text-center d-md-none">DESC</th>
                
                <th class="py-3 text-muted small text-uppercase text-right d-none d-md-table-cell">Odômetro</th>
                <th class="py-3 px-4 text-muted small text-uppercase text-right">
                    <span class="d-none d-md-inline">Valor (R$)</span>
                    <span class="d-md-none">VALOR</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($expensesList as $exp)
            <tr class="animate__animated animate__fadeInUp animate__faster border-bottom print-break-inside-avoid">
                <td class="py-3 px-4 align-middle">
                    <div class="d-flex flex-column text-muted">
                        <span class="font-weight-bold text-dark" style="font-size: 0.9rem;">{{ $exp->created_at->format('d/m/y') }}</span>
                        <small style="font-size: 0.7rem;" class="d-none d-md-inline">{{ $exp->created_at->format('H:i') }}h</small>
                    </div>
                </td>
                <td class="align-middle text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        <!-- 🏷️ PLACA MERCOSUL RESPONSIVA -->
                        <div class="mercosul-plate">
                            <div class="plate-header">
                                <span>BRASIL</span>
                                <i class="fas fa-certificate" style="font-size: 0.3rem;"></i>
                            </div>
                            <span class="plate-text">{{ $exp->vehicle->plate ?? 'N/A' }}</span>
                        </div>
                    </div>
                </td>
                <td class="align-middle d-none d-md-table-cell">
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
                <td class="align-middle text-center" style="max-width: 250px;">
                    <!-- DESKTOP: TEXTO + LINK -->
                    <div class="d-none d-md-block text-left">
                        <span class="text-dark font-weight-bold d-block text-truncate" title="{{ $exp->description }}">{{ $exp->description }}</span>
                        @if($exp->receipt_photo)
                            <a href="{{ asset('storage/' . $exp->receipt_photo) }}" onclick="event.preventDefault(); viewPhoto(this.href, 'COMPROVANTE: {{ $exp->type }}')" class="mt-1 d-inline-block text-orange small ripple-effect d-print-none" style="cursor: zoom-in;">
                                <i class="fas fa-camera"></i> Ver Comprovante
                            </a>
                        @endif
                    </div>

                    <!-- MOBILE: APENAS ÍCONE DE FOTO -->
                    <div class="d-md-none text-center">
                        @if($exp->receipt_photo)
                            <button onclick="viewPhoto('{{ asset('storage/' . $exp->receipt_photo) }}', 'COMPROVANTE: {{ $exp->type }}')" class="btn btn-light btn-sm shadow-sm ripple-effect" style="border-radius: 10px; border: 1px solid #ddd;">
                                <i class="fas fa-camera fa-lg text-orange"></i>
                            </button>
                        @else
                            <i class="fas fa-minus text-muted opacity-50"></i>
                        @endif
                    </div>
                </td>
                <td class="align-middle text-right d-none d-md-table-cell">
                    <span class="font-weight-bold text-muted" style="font-size: 1.1rem;">
                        {{ number_format($exp->odometer, 0, ',', '.') }}
                    </span>
                    <small class="text-muted">KM</small>
                </td>
                <td class="py-3 px-4 align-middle text-right">
                    <span class="text-dark font-weight-bold value-responsive" style="font-size: 1.1rem;">
                        <span class="d-none d-md-inline">R$ </span>{{ number_format($exp->amount, 2, ',', '.') }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
