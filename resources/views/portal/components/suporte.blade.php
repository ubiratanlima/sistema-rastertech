<div class="animate__animated animate__fadeIn">
    <div class="row align-items-stretch">
        <!-- ⚓ IDENTIFICAÇÃO PRIORITÁRIA (RTECH CODE & LOGO) -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 h-100 text-white" style="border-radius: 12px; background: linear-gradient(135deg, #6610f2 0%, #4e08c1 100%);">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <!-- 🖼️ LOGO RASTERTECH -->
                    <div class="bg-white p-2 mb-4" style="width: 100%; border-radius: 8px;">
                        <img src="https://rastertech.com.br/site/wp-content/uploads/2022/10/logo-07-1024x180.png" class="img-fluid" style="max-height: 40px; object-fit: contain;">
                    </div>
                    
                    <div class="text-uppercase font-weight-bold opacity-75 mb-1" style="font-size: 0.75rem; letter-spacing: 2.5px;">CÓDIGO DO CLIENTE</div>
                    <h2 class="text-bold m-0 mb-3" style="font-size: 3rem; letter-spacing: -1px;">#{{ $customer->code ?? str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    
                    @php
                        // 🔗 Fonte Oficial: Localizamos o usuário "Gestor" vinculado a este cliente específico
                        $linkedUser = \App\Models\User::where('customer_id', $customer->id)->where('role', 'customer')->first();
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

        <!-- 📡 CANAIS DE COMUNICAÇÃO (GRID TÁTICO) -->
        <div class="col-md-8">
            <div class="row h-100">
                <!-- 📞 TELEFONE -->
                <div class="col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 hover-zoom" style="border-radius: 12px; background: #fff; cursor: pointer;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-light p-3 mr-3" style="border-radius: 10px;">
                                <i class="fas fa-phone-alt fa-2x text-primary font-weight-bold"></i>
                            </div>
                            <div>
                                <h6 class="text-bold mb-1">TELEFONE (0800)</h6>
                                <p class="text-muted text-xs mb-0">Atendimento imediato e resposta tática em tempo real.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 💬 WHATSAPP -->
                <div class="col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 hover-zoom" style="border-radius: 12px; background: #fff; cursor: pointer;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-light p-3 mr-3" style="border-radius: 10px;">
                                <i class="fab fa-whatsapp fa-2x text-success font-weight-bold"></i>
                            </div>
                            <div>
                                <h6 class="text-bold mb-1">WHATSAPP RTECH</h6>
                                <p class="text-muted text-xs mb-0">Atendimento automatizado com triagem humanizada.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ✉️ E-MAIL -->
                <div class="col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 hover-zoom" style="border-radius: 12px; background: #fff; cursor: pointer;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-light p-3 mr-3" style="border-radius: 10px;">
                                <i class="fas fa-envelope fa-2x text-warning font-weight-bold"></i>
                            </div>
                            <div>
                                <h6 class="text-bold mb-1">E-MAIL SUPORTE</h6>
                                <p class="text-muted text-xs mb-0">Atendimento oficial com resposta rápida e técnica.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📸 REDES SOCIAIS -->
                <div class="col-sm-6 mb-4">
                    <div class="card shadow-sm border-0 h-100 hover-zoom" style="border-radius: 12px; background: #fff; cursor: pointer;">
                        <div class="card-body p-4 d-flex align-items-center">
                            <div class="icon-box bg-light p-3 mr-3" style="border-radius: 10px;">
                                <i class="fab fa-instagram-square fa-2x text-danger font-weight-bold"></i>
                            </div>
                            <div>
                                <h6 class="text-bold mb-1">SOCIAL (IG/FB)</h6>
                                <p class="text-muted text-xs mb-0">Acompanhe nossas atualizações e atendimento via direct.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🚥 CONSELHO TÁTICO FINAL -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; background: #fdfdfd; border-left: 5px solid #ffc107 !important;">
        <div class="card-body p-3 small text-muted">
            <i class="fas fa-info-circle mr-2 text-warning"></i> 
            <b>DICA RASTERTECH:</b> Mantenha seu <b>RTech Code</b> e seu <b>Cartão de Suporte</b> sempre acessíveis. Eles são a sua identidade oficial dentro do nosso ecossistema de segurança.
        </div>
    </div>
</div>
