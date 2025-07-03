<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'VehicleFlow'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/custom.css'); ?>" rel="stylesheet">
    
    <style>
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 0.5rem;
            margin: 0.2rem 0;
            padding: 0.8rem 1rem;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.2);
        }
        
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.2rem;
        }
        
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .stats-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0,0,0,.125);
            transition: transform 0.2s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .card-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .badge-status {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,123,255,.05);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php if (isset($user) && $this->session->userdata('logged_in')): ?>
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white fw-bold">
                            <i class="bi bi-truck"></i> VehicleFlow
                        </h4>
                        <p class="text-white-50 small mb-0">Mining Company</p>
                    </div>
                    
                    <div class="text-center mb-4">
                        <div class="avatar bg-white rounded-circle mx-auto mb-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-person-fill text-primary fs-4"></i>
                        </div>
                        <h6 class="text-white mb-0"><?php echo $user['full_name']; ?></h6>
                        <small class="text-white-50"><?php echo ucfirst($user['role']); ?></small>
                        <?php if (!empty($user['department'])): ?>
                        <br><small class="text-white-50"><?php echo $user['department']; ?></small>
                        <?php endif; ?>
                    </div>

                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (uri_string() == 'dashboard' || uri_string() == '') ? 'active' : ''; ?>" href="<?php echo base_url('dashboard'); ?>">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'bookings') !== false) ? 'active' : ''; ?>" href="<?php echo base_url('bookings'); ?>">
                                <i class="bi bi-calendar-check"></i>
                                Pemesanan
                            </a>
                        </li>
                        
                        <?php if ($user['role'] == 'approver' || $user['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'approvals') !== false) ? 'active' : ''; ?>" href="<?php echo base_url('approvals'); ?>">
                                <i class="bi bi-check-circle"></i>
                                Persetujuan
                                <?php 
                                $this->load->model('Approval_model');
                                $pending_count = count($this->Approval_model->get_pending_approvals_for_user($user['id']));
                                if ($pending_count > 0): 
                                ?>
                                <span class="badge bg-warning rounded-pill ms-auto"><?php echo $pending_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($user['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'vehicles') !== false) ? 'active' : ''; ?>" href="<?php echo base_url('vehicles'); ?>">
                                <i class="bi bi-truck"></i>
                                Kendaraan
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'drivers') !== false) ? 'active' : ''; ?>" href="<?php echo base_url('drivers'); ?>">
                                <i class="bi bi-person-badge"></i>
                                Driver
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link <?php echo (strpos(uri_string(), 'reports') !== false) ? 'active' : ''; ?>" href="<?php echo base_url('reports'); ?>">
                                <i class="bi bi-graph-up"></i>
                                Laporan
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <hr class="text-white-50 my-3">
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('auth/profile'); ?>">
                                <i class="bi bi-person-gear"></i>
                                Profile
                            </a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('auth/logout'); ?>" onclick="return confirm('Yakin ingin logout?')">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <?php endif; ?>

            <!-- Main content -->
            <main class="<?php echo (isset($user) && $this->session->userdata('logged_in')) ? 'col-md-9 ms-sm-auto col-lg-10 px-md-4' : 'col-12'; ?> main-content">
                <?php if (isset($user) && $this->session->userdata('logged_in')): ?>
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                </div>
                <?php endif; ?>

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

                <?php if ($this->session->flashdata('info')): ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <?php echo $this->session->flashdata('info'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>