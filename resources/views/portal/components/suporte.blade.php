<div class="animate__animated animate__fadeIn">
    <div class="row align-items-stretch">
        <!-- ⚓ IDENTIFICAÇÃO PRIORITÁRIA (RTECH CODE & LOGO) -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 text-white" style="border-radius: 12px; background: linear-gradient(135deg, #6610f2 0%, #4e08c1 100%); min-height: 380px;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <!-- 🖼️ LOGO RASTERTECH -->
                    <div class="bg-white p-2 mb-4" style="width: 100%; border-radius: 8px;">
                        <img src="https://rastertech.com.br/site/wp-content/uploads/2022/10/logo-07-1024x180.png" class="img-fluid" style="max-height: 40px; object-fit: contain;">
                    </div>
                    
                    <div class="text-uppercase font-weight-bold opacity-75 mb-1" style="font-size: 0.75rem; letter-spacing: 2.5px;">CÓDIGO DO CLIENTE</div>
                    <h2 class="text-bold m-0 mb-3" style="font-size: 3rem; letter-spacing: -1px;">#{{ $customer->code ?? str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    
                    @php
                        // 🔗 Fonte Oficial: Localizamos o usuário "Gestor" vinculado a este cliente específico
                        $linkedUser = \App\Models\User::where('customer_id', $customer->id)->where('role', 'Cliente')->first();
                        $extUser = ($linkedUser && $linkedUser->external_username) ? $linkedUser->external_username : 'acesso_rtech';
                        $extPass = ($linkedUser && $linkedUser->external_password) ? $linkedUser->external_password : 'password_indisponivel';
                    @endphp
                    <div class="w-100 p-2 mt-2 rounded border border-white-50" style="background: rgba(255,255,255,0.05); font-size: 0.85rem;">
                        <span class="mr-3 text-nowrap" title="Usuário Externo (Vínculo)"><i class="fas fa-user-circle mr-1 opacity-75"></i> {{ $extUser }}</span>
                        <span class="text-nowrap" title="Senha de App (Vínculo)"><i class="fas fa-key mr-1 opacity-75"></i> {{ $extPass }}</span>
                    </div>

                    <p class="mt-4 small opacity-75 mb-0">Informe este código em qualquer canal para atendimento imediato.</p>
                </div>
            </div>
        </div>

        <!-- 💰 PAINEL FINANCEIRO (ASAAS) -->
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: #fff; max-height: 380px; overflow: hidden; display: flex; flex-direction: column;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center" style="flex-shrink: 0;">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-file-invoice-dollar mr-2 text-success"></i> Central de Faturas & Notas Fiscais</h6>
                </div>
                <div class="card-body p-0" style="overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small font-weight-bold text-muted">
                                    <th class="px-4 py-3">Vencimento</th>
                                    <th class="py-3 text-center">Mês Referência</th>
                                    <th class="py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-left">Downloads</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $p)
                                    @php
                                        $dueDate = \Carbon\Carbon::parse($p['dueDate']);
                                        $refDate = $dueDate->copy()->subMonth();
                                        $months = [
                                            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
                                            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
                                            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
                                        ];
                                        $statusColors = [
                                            'RECEIVED' => 'success',
                                            'CONFIRMED' => 'primary',
                                            'OVERDUE' => 'danger',
                                            'PENDING' => 'warning',
                                            'REFUNDED' => 'secondary'
                                        ];
                                        $statusLabels = [
                                            'RECEIVED' => 'PAGO',
                                            'CONFIRMED' => 'RECEBIDO',
                                            'OVERDUE' => 'VENCIDO',
                                            'PENDING' => 'ABERTO',
                                            'REFUNDED' => 'ESTORNADO'
                                        ];
                                    @endphp
                                    <tr style="height: 60px; vertical-align: middle;">
                                        <td class="align-middle px-4 font-weight-bold text-dark">
                                            {{ $dueDate->format('d/m/Y') }}
                                        </td>
                                        <td class="align-middle text-center text-muted">
                                            {{ $months[$refDate->month] }}/{{ $refDate->year }}
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge badge-{{ $statusColors[$p['status']] ?? 'light' }} px-2 py-1" style="font-size: 0.7rem;">
                                                {{ $statusLabels[$p['status']] ?? $p['status'] }}
                                            </span>
                                        </td>
                                        <td class="align-middle px-4 text-left">
                                            <div class="d-flex" style="gap: 5px;">
                                                <!-- 📄 BOLETO / FATURA (Link Principal de Pagamento) -->
                                                @if(!empty($p['bankSlipUrl']))
                                                    <a href="{{ $p['bankSlipUrl'] }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Baixar Boleto/Fatura">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @elseif(!empty($p['invoiceUrl']) && empty($p['invoiceNumber']))
                                                    {{-- Fallback para fatura se não for nota --}}
                                                    <a href="{{ $p['invoiceUrl'] }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver Fatura">
                                                        <i class="fas fa-file-invoice"></i>
                                                    </a>
                                                @endif

                                                <!-- 🧾 NOTA FISCAL (Link Direto para o PDF se estiver Autorizada) -->
                                                @if(!empty($p['direct_invoice_pdf']))
                                                    <a href="{{ $p['direct_invoice_pdf'] }}" target="_blank" class="btn btn-sm btn-outline-info" title="Baixar Nota Fiscal Direto">
                                                        <i class="fas fa-receipt"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted small">
                                            <i class="fas fa-history fa-2x mb-2 opacity-20 d-block"></i>
                                            Nenhum registro de cobrança localizado no momento.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>                          </tbody>
                        </table>
                    </div>
                </div>
                <!-- 🔘 PAGINAÇÃO FINANCEIRA -->
                @if($page > 1 || $hasMore)
                <div class="card-footer bg-white border-0 py-2 px-4 d-flex justify-content-between align-items-center" style="flex-shrink: 0;">
                    <button class="btn btn-xs btn-outline-secondary px-3" 
                            {{ $page <= 1 ? 'disabled' : '' }}
                            onclick="loadComponent('suporte', 'page={{ $page - 1 }}')">
                        <i class="fas fa-chevron-left mr-1"></i> Anterior
                    </button>
                    <span class="small font-weight-bold text-muted">PÁGINA {{ $page }}</span>
                    <button class="btn btn-xs btn-outline-secondary px-3" 
                            {{ !$hasMore ? 'disabled' : '' }}
                            onclick="loadComponent('suporte', 'page={{ $page + 1 }}')">
                        Próxima <i class="fas fa-chevron-right ml-1"></i>
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 🚥 CONSELHO TÁTICO FINAL -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #fdfdfd; border-left: 5px solid #ffc107 !important;">
        <div class="card-body p-3 small text-muted">
            <i class="fas fa-info-circle mr-2 text-warning"></i> 
            <b>DICA RASTERTECH:</b> Mantenha seu <b>RTech Code</b> sempre acessíveis. Clique no ícone de PDF para visualizar ou baixar sua fatura em uma nova aba.
        </div>
    </div>
</div>
