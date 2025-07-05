<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - VehicleFlow</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #007bff;
            --light-blue: #e7f1ff;
            --text-color: #4a5568;
            --sidebar-bg: #ffffff;
            --body-bg: #f7f8fc;
            --border-color: #e2e8f0;
        }
        body {
            background-color: var(--body-bg);
            color: var(--text-color);
            font-family: 'Poppins', sans-serif; /* A more modern font, add google font link if needed */
        }
        .main-wrapper {
            display: flex;
        }
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            border-right: 1px solid var(--border-color);
            padding: 20px;
            display: flex;
            flex-direction: column;
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        .sidebar-logo {
            width: 40px;
            height: 40px;
            background-color: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-size: 20px;
            margin-right: 10px;
        }
        .sidebar-title h5 {
            margin: 0;
            font-weight: 600;
            font-size: 18px;
        }
        .sidebar-title p {
            margin: 0;
            font-size: 12px;
            color: #a0aec0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            flex-grow: 1;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-color);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-menu li a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
        .sidebar-menu li.active a, .sidebar-menu li a:hover {
            background-color: var(--light-blue);
            color: var(--primary-color);
        }
        .sidebar-footer {
            margin-top: auto;
        }
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 20px;
        }
        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid var(--border-color);
        }
        .top-navbar .search-box {
            position: relative;
        }
        .top-navbar .search-box input {
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 8px 15px 8px 35px;
        }
        .top-navbar .search-box i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
        }
        .top-navbar-right {
            display: flex;
            align-items: center;
        }
        .top-navbar-right .icon-btn {
            font-size: 20px;
            color: var(--text-color);
            margin-left: 20px;
        }
    </style>
    <style>
        /* Custom Dashboard Styles */
        .stat-card, .chart-card, .list-card {
            background-color: #fff;
            border-radius: 12px; /* Softer corners */
            padding: 25px; /* More padding */
            border: 1px solid #e2e8f0;
            margin-bottom: 24px; /* Consistent margin */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.04); /* Subtle shadow */
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }
        .stat-card:hover, .chart-card:hover, .list-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .stat-card .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .stat-card .stat-icon.bg-blue { background-color: #e7f1ff; color: #007bff; }
        .stat-card .stat-icon.bg-orange { background-color: #fff4e5; color: #ff9f0a; }
        .stat-card .stat-icon.bg-green { background-color: #eaf6f0; color: #28a745; }
        .stat-card .stat-icon.bg-purple { background-color: #f3e8ff; color: #6f42c1; }
        .stat-card h3 { font-size: 2rem; font-weight: 700; margin-bottom: 0; }
        .stat-card p { color: #a0aec0; margin: 0; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
        .chart-card {
            height: 420px; /* Fixed height for alignment */
        }
        .list-card .list-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0.5rem;
            transition: background-color 0.2s ease;
        }
        .list-card .list-item:hover {
            background-color: #f8f9fa;
        }
        .list-card .list-item:last-child { border-bottom: none; }
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 20px;
            text-transform: capitalize;
        }
        .status-completed { background-color: #eaf6f0; color: #28a745; }
        .status-pending { background-color: #fff4e5; color: #ff9f0a; }
        .status-approved { background-color: #e7f1ff; color: #007bff; }
        .status-rejected { background-color: #fbeaea; color: #dc3545; }
        .action-card .btn {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
            padding: 12px;
        }
        .action-card .btn .fas {
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <nav class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo"><i class="fas fa-car-side"></i></div>
            <div class="sidebar-title">
                <h5>VehicleFlow</h5>
                <p>Manajemen Kendaraan</p>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="<?php echo ($this->uri->segment(1) == 'dashboard') ? 'active' : ''; ?>">
                <a href="<?php echo site_url('dashboard'); ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            </li>
            <li>
                <a href="#"><i class="fas fa-plus"></i> Pemesanan Baru</a>
            </li>
            <li class="<?php echo ($this->uri->segment(2) == 'bookings') ? 'active' : ''; ?>">
                <a href="<?php echo site_url('admin/bookings'); ?>"><i class="fas fa-list-alt"></i> Daftar Pemesanan</a>
            </li>
             <?php if (strpos($this->session->userdata('role'), 'approver') !== false || $this->session->userdata('role') == 'admin'): ?>
                <li class="<?php echo ($this->uri->segment(1) == 'approvals') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('approvals'); ?>"><i class="fas fa-check-square"></i> Persetujuan</a>
                </li>
            <?php endif; ?>
            <?php if ($this->session->userdata('role') == 'admin'): ?>
                <li class="<?php echo ($this->uri->segment(2) == 'vehicles') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('admin/vehicles'); ?>"><i class="fas fa-car"></i> Kelola Kendaraan</a>
                </li>
                <li class="<?php echo ($this->uri->segment(2) == 'drivers') ? 'active' : ''; ?>">
                    <a href="<?php echo site_url('admin/drivers'); ?>"><i class="fas fa-id-card"></i> Kelola Driver</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-chart-line"></i> Laporan</a>
                </li>
                 <li>
                    <a href="#"><i class="fas fa-cog"></i> Pengaturan</a>
                </li>
            <?php endif; ?>
        </ul>
        <div class="sidebar-footer">
             <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo site_url('auth/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-content">
        <header class="top-navbar">
            <div class="top-navbar-left">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" class="form-control" placeholder="Cari pemesanan...">
                </div>
            </div>
        </nav>
