<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $title ?></title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor.bundle.base.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/vertical-layout-light/style.css') ?>">
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= base_url('images/favicon.png') ?>" />
    
    <style>
        .auth-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        
        .login-header h3 {
            margin: 0;
            font-weight: 300;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border-radius: 25px;
            padding: 0.75rem 1.25rem;
            border: 2px solid #e3e6f0;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            color: white;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .company-logo {
            max-height: 60px;
            margin-bottom: 1rem;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
    </style>
</head>

<body>
    <div class="auth-wrapper">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <img src="<?= base_url('images/logo-delafiber.png') ?>" alt="Delafiber" class="company-logo">
                <h3>CRM Delafiber</h3>
                <p class="mb-0">Inicia sesión para continuar</p>
            </div>
            
            <!-- Body -->
            <div class="login-body">
                <!-- Mensajes de error/éxito -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="ti-alert"></i> <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success mb-4">
                        <i class="ti-check"></i> <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?= $error ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="<?= base_url('auth/login') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="usuario">Usuario o Email</label>
                        <input type="text" 
                               class="form-control" 
                               id="usuario" 
                               name="usuario" 
                               placeholder="Ingresa tu nombre o email"
                               value="<?= old('usuario') ?>"
                               required
                               autofocus>
                    </div>
                    
                    <div class="form-group">
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Ingresa tu contraseña"
                               required>
                    </div>
                    
                    <div class="form-group d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="recordar">
                            <label class="form-check-label" for="recordar">
                                Recordarme
                            </label>
                        </div>
                        <a href="#" class="text-muted small">¿Olvidaste tu contraseña?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="ti-lock"></i> Iniciar Sesión
                    </button>
                </form>
                
                <!-- Información adicional -->
                <div class="text-center mt-4">
                    <small class="text-muted">
                        Para soporte técnico contacta a: 
                        <a href="mailto:soporte@delafiber.com">soporte@delafiber.com</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Demo credentials (remover en producción) -->
    <div class="position-fixed" style="bottom: 20px; left: 20px;">
        <div class="card border-info" style="max-width: 250px;">
            <div class="card-header bg-info text-white py-2">
                <small><i class="ti-info"></i> Credenciales de prueba</small>
            </div>
            <div class="card-body py-2">
                <small>
                    <strong>Admin:</strong><br>
                    admin@delafiber.com<br>
                    Password: password<br><br>
                    <strong>Vendedor:</strong><br>
                    carlos@delafiber.com<br>
                    Password: password
                </small>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="<?= base_url('assets/js/vendor.bundle.base.js') ?>"></script>
    
    <script>
        // Auto-focus en el campo usuario
        document.getElementById('usuario').focus();
        
        // Limpiar mensajes después de unos segundos
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.remove();
                    }
                }, 500);
            });
        }, 5000);
        
        // Validación simple del formulario
        document.querySelector('form').addEventListener('submit', function(e) {
            var usuario = document.getElementById('usuario').value.trim();
            var password = document.getElementById('password').value.trim();
            
            if (!usuario || !password) {
                e.preventDefault();
                alert('Por favor completa todos los campos');
                return false;
            }
            
            if (usuario.length < 3 || password.length < 3) {
                e.preventDefault();
                alert('Usuario y contraseña deben tener al menos 3 caracteres');
                return false;
            }
        });
    </script>
</body>
</html>