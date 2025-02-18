<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?= getenv('APP_NAME') ?></title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar a.active {
            background-color: #0d6efd;
        }
        .main-content {
            padding: 20px;
        }
        .stats-overview .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .stats-overview .card-title {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        .stats-overview .card-text {
            color: #343a40;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 px-0 sidebar">
                <div class="py-4 px-3 mb-4 text-white">
                    <h5><?= getenv('APP_NAME') ?></h5>
                </div>
                <nav>
                    <a href="/admin" class="<?= current_url() == base_url('admin') ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                    </a>
                    <a href="/admin/bookings" class="<?= strpos(current_url(), 'admin/bookings') !== false ? 'active' : '' ?>">
                        <i class="fas fa-calendar-alt me-2"></i> Bookings
                    </a>
                    <a href="/admin/services" class="<?= strpos(current_url(), 'admin/services') !== false ? 'active' : '' ?>">
                        <i class="fas fa-concierge-bell me-2"></i> Services
                    </a>
                    <a href="/admin/users" class="<?= strpos(current_url(), 'admin/users') !== false ? 'active' : '' ?>">
                        <i class="fas fa-users me-2"></i> Users
                    </a>
                    <a href="/admin/time-slots" class="<?= strpos(current_url(), 'admin/time-slots') !== false ? 'active' : '' ?>">
                        <i class="fas fa-clock me-2"></i> Time Slots
                    </a>
                    <a href="/admin/settings" class="<?= strpos(current_url(), 'admin/settings') !== false ? 'active' : '' ?>">
                        <i class="fas fa-cog me-2"></i> Settings
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <header class="mb-4">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4><?= $this->renderSection('title') ?? 'Dashboard' ?></h4>
                        </div>
                        <div class="col text-end">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                    <i class="fas fa-user me-2"></i><?= session()->get('user_name') ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/profile"><i class="fas fa-user-circle me-2"></i>Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="/auth/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Content -->
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@3.2.26/dist/vue.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
