<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem;
            font-weight: 500;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
        
        .credential-info {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .credential-info h6 {
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .credential-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        
        .credential-item strong {
            color: #495057;
        }
        
        .credential-item span {
            color: #6c757d;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <h2 class="mb-2">
                <i class="bi bi-truck"></i> VehicleFlow
            </h2>
            <p class="mb-0 opacity-75">Mining Company</p>
            <small class="opacity-50">Sistem Pemesanan Kendaraan</small>
        </div>
        
        <div class="login-body">
            <!-- Flash Messages -->
            <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?php echo $this->session->flashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo $this->session->flashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if (validation_errors()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php echo validation_errors(); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php echo form_open('auth/login', array('class' => 'needs-validation', 'novalidate' => '')); ?>
                <div class="form-floating">
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Username"
                           value="<?php echo set_value('username'); ?>"
                           required>
                    <label for="username">
                        <i class="bi bi-person"></i> Username
                    </label>
                    <div class="invalid-feedback">
                        Username harus diisi.
                    </div>
                </div>

                <div class="form-floating">
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Password"
                           required>
                    <label for="password">
                        <i class="bi bi-lock"></i> Password
                    </label>
                    <div class="invalid-feedback">
                        Password harus diisi.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            <?php echo form_close(); ?>

            <!-- Login Credentials Info -->
            <div class="credential-info">
                <h6><i class="bi bi-info-circle"></i> Login Credentials untuk Testing</h6>
                
                <div class="credential-item">
                    <strong>Admin:</strong>
                    <span>admin / admin123</span>
                </div>
                
                <div class="credential-item">
                    <strong>Manager L1:</strong>
                    <span>manager1 / manager123</span>
                </div>
                
                <div class="credential-item">
                    <strong>Manager L2:</strong>
                    <span>manager2 / manager123</span>
                </div>
                
                <div class="credential-item">
                    <strong>User:</strong>
                    <span>user1 / user123</span>
                </div>
                
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-shield-check"></i> 
                    Data akan tersedia setelah generate sample data melalui dashboard admin.
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>