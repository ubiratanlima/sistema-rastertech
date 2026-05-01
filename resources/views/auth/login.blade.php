<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso Restrito | Rastertech Fleet</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Theme style (AdminLTE) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <style>
        :root {
            --brand-color: #008080; /* Teal Rastertech */
            --brand-gradient: linear-gradient(135deg, #008080 0%, #004d4d 100%);
        }

        body.login-page {
            background-image: url('https://images.unsplash.com/photo-1569336415962-a4bd9f69cd83?q=80&w=2062&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        body.login-page::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(255, 255, 255, 0.85); /* Mais branco/transparente */
            z-index: 0;
        }

        .login-box {
            width: 400px;
            position: relative;
            z-index: 1;
        }

        .card {
            border-top: 5px solid var(--brand-color);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }

        .login-logo img {
            max-width: 280px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .btn-brand {
            background-color: var(--brand-color);
            border-color: var(--brand-color);
            color: white;
            font-weight: bold;
            transition: all 0.3s;
        }

        .btn-brand:hover {
            background-color: #006666;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .input-group-text {
            color: var(--brand-color);
            background-color: transparent;
        }

        .login-card-body {
            border-radius: 12px;
            padding: 30px;
        }
    </style>
</head>
<body class="login-page">
<div class="login-box animate__animated animate__fadeIn">
    <div class="login-logo mb-4 text-center">
        <a href="/">
            <img src="{{ asset('img/logo_rastertech.png') }}" alt="Rastertech Logo">
        </a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg font-weight-bold text-muted">Comando Geral de Frotas</p>

            @if($errors->any())
                <div class="alert alert-danger py-2 animate__animated animate__shakeX">
                    <ul class="mb-0 small">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="text" name="login" class="form-control" placeholder="E-mail ou Usuário" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-4">
                    <input type="password" name="password" class="form-control" placeholder="Senha" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-brand btn-block btn-lg shadow-sm">
                            <i class="fas fa-sign-in-alt mr-2"></i> ACESSAR SISTEMA
                        </button>
                    </div>
                </div>
            </form>

            <div class="mt-4 text-center">
                <p class="mb-0 text-muted small">
                    © {{ date('Y') }} Rastertech Segurança. Todos os direitos reservados.
                </p>
            </div>
        </div>
        <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
