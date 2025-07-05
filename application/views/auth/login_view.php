<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Vehicle Booking</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .top-bar {
            height: 4px;
            background-color: #007bff;
        }
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 4px);
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .login-header {
            text-align: center;
            padding: 30px 20px;
        }
        .logo {
            background-color: #007bff;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        .login-header h3 {
            font-weight: 700;
            margin-bottom: 5px;
        }
        .login-header p {
            color: #6c757d;
        }
        .login-body {
            padding: 30px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            padding: 10px;
            font-weight: 600;
        }
        .credentials-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
        }
        .credentials-info h6 {
            font-weight: 700;
            margin-bottom: 10px;
        }
        .credentials-info p {
            margin-bottom: 5px;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="top-bar"></div>
    <div class="login-container">
        <div class="login-card">
            <div class="card-body">
                <div class="login-header">
                    <div class="logo">
                        <i class="fas fa-car"></i>
                    </div>
                    <h3>VehicleFlow</h3>
                    <p>Sistem Manajemen Pemesanan Kendaraan</p>
                </div>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger mx-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if(validation_errors()): ?>
                    <div class="alert alert-danger mx-3"><?php echo validation_errors(); ?></div>
                <?php endif; ?>

                <?php echo form_open('auth/process_login', ['class' => 'px-3']); ?>
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-4">Masuk</button>
                <?php echo form_close(); ?>

                <div class="credentials-info mx-3">
                    <h6>Default Login Credentials:</h6>
                    <p><strong>Admin:</strong> admin / password</p>
                    <p><strong>Approver L1:</strong> approver1 / password</p>
                    <p><strong>Approver L2:</strong> approver2 / password</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
