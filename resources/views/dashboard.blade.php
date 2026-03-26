@extends('layouts.app')

@section('title', 'Comando de Operações')
 
@push('styles')
    <!-- Estão de Telemetria e BI -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .info-box { transition: transform 0.2s; cursor: pointer; }
        .info-box:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important; }
        #map { background: #f8f9fa; border-radius: 0 0 12px 12px; }
    </style>
@endpush

@section('content')
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden">
        <div class="col-sm-12 p-0 p-sm-2">
            <h1 class="m-0 text-bold" style="font-size: 2.2rem; color: #343a40;">
                <i class="fas fa-terminal mr-2 text-primary"></i>Comando de Operações
            </h1>
            <p class="text-muted mb-0">Visão geral em tempo real da infraestrutura Rastertech.</p>
        </div>
    </div>
    
    <div class="row mt-4">
        <!-- Info Boxes -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm elevation-1 border-0">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-car-side"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase small font-weight-bold">Dispositivos</span>
                    <span class="info-box-number h3 mb-0">{{ number_format($totalDevices, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm elevation-1 border-0">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-signal"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase small font-weight-bold">SIM Cards ativos</span>
                    <span class="info-box-number h3 mb-0">{{ number_format($totalSims, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm elevation-1 border-0">
                <span class="info-box-icon bg-warning elevation-1 text-white"><i class="fas fa-bolt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase small font-weight-bold">Online Agora</span>
                    <span class="info-box-number h3 mb-0">{{ number_format($onlineNow, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box shadow-sm elevation-1 border-0">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-uppercase small font-weight-bold">Alertas</span>
                    <span class="info-box-number h3 mb-0">{{ $criticalAlerts }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-5">
            <div class="card card-outline card-primary shadow-sm border-0" style="border-radius: 12px; border-top: 3px solid #007bff !important;">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <h3 class="card-title text-bold" style="font-size: 1.1rem; color: #333;">Distribuição por Modelo</h3>
                </div>
                <div class="card-body" style="height: 350px;"><canvas id="deviceChart"></canvas></div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card card-outline card-info shadow-sm border-0" style="border-radius: 12px; border-top: 3px solid #17a2b8 !important;">
                <div class="card-header border-0 bg-transparent px-4 pt-4 pb-0">
                    <h3 class="card-title text-bold" style="font-size: 1.1rem; color: #333;">Mapa Operacional</h3>
                </div>
                <div class="card-body p-0" style="height: 350px;"><div id="map" style="width: 100%; height: 100%; border-radius: 0 0 12px 12px;"></div></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<!-- Motores de Gráficos e Mapas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var isLight = document.body.classList.contains('light-mode');
        
        // 📊 Donut Chart
        var ctx = document.getElementById('deviceChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{ 
                    data: {!! json_encode($chartData) !!}, 
                    backgroundColor: ['#00ff88', '#00ccff', '#ff4d4d', '#a855f7', '#facc15'], 
                    borderWidth: 0 
                }]
            },
            options: { maintainAspectRatio: false, plugins: { legend: { position: 'right', labels: { color: isLight ? '#000' : '#888b91' } } } }
        });

        // 🛰️ Mapa
        var map = L.map('map', { zoomControl: false, attributionControl: false }).setView([-23.5505, -46.6333], 12);
        var tileLayer = isLight ? 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png' : 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png';
        L.tileLayer(tileLayer, { maxZoom: 19 }).addTo(map);
        [[-23.5505, -46.6333], [-23.5600, -46.6500], [-23.5400, -46.6200]].forEach(p => { 
            L.circleMarker(p, { color: '#00ff88', fillColor: '#00ff88', fillOpacity: 0.8, radius: 6 }).addTo(map); 
        });
        setTimeout(function(){ map.invalidateSize(); }, 500);
    });
</script>
@endpush
