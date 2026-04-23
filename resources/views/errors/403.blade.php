<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Negado | Rastertech</title>
    
    <!-- AdminLTE CSS & Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <style>
        body { background-color: #f4f6f9; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .error-container { text-align: center; max-width: 600px; padding: 40px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .error-code { font-size: 80px; font-weight: bold; color: #dc3545; line-height: 1; }
        .btn-whatsapp { background-color: #25d366; color: white; transition: 0.3s; }
        .btn-whatsapp:hover { background-color: #128c7e; color: white; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code mb-3">403</div>
        <h3 class="text-dark font-weight-bold mb-4"><i class="fas fa-hand-paper text-danger mr-2"></i> Acesso Restrito</h3>
        <p class="text-muted" style="font-size: 1.1rem;">
            {{ $exception->getMessage() ?: 'Você não possui as permissões necessárias para acessar esta área ou realizar esta ação.' }}
        </p>
        <hr class="my-4">
        <p class="small text-secondary mb-4">
            Se você acredita que isto é um erro, por favor, entre em contato através do e-mail <br>
            <a href="mailto:contato@embraet.com.br" class="text-primary font-weight-bold">contato@embraet.com.br</a> ou acione o suporte.
        </p>
        <div class="d-flex justify-content-center flex-wrap" style="gap: 15px;">
            <a href="{{ url('/') }}" class="btn btn-primary"><i class="fas fa-home mr-1"></i> Voltar ao Início</a>
            <a href="https://wa.me/551231993369" target="_blank" class="btn btn-whatsapp"><i class="fab fa-whatsapp mr-1"></i> Chamar no WhatsApp</a>
            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt mr-1"></i> Sair do Sistema</button>
            </form>
        </div>
    </div>
</body>
</html>
