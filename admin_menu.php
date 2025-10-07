<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Get statistics
$participants_query = "SELECT COUNT(*) as count FROM participant";
$participants_result = mysqli_query($conn, $participants_query);
$total_participants = mysqli_fetch_assoc($participants_result)['count'];

$teams_query = "SELECT COUNT(*) as count FROM team";
$teams_result = mysqli_query($conn, $teams_query);
$total_teams = mysqli_fetch_assoc($teams_result)['count'];

$merchandise_query = "SELECT COUNT(*) as count FROM merchandise";
$merchandise_result = mysqli_query($conn, $merchandise_query);
$total_registrations = mysqli_fetch_assoc($merchandise_result)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00D2D3;
            --dark-bg: #0A0E27;
            --darker-bg: #060818;
            --success-color: #4CAF50;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--darker-bg) 100%);
            color: white;
            min-height: 100vh;
        }

        .navbar {
            background: rgba(10, 14, 39, 0.95) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(108, 92, 231, 0.3);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .dashboard-header {
            margin-top: 80px;
            padding: 2rem 0;
            text-align: center;
        }

        .dashboard-title {
            font-size: 2.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            transition: all 0.3s;
            height: 100%;
            text-align: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(108, 92, 231, 0.4);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .stat-label {
            color: #B8BCC8;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            transition: all 0.3s;
            height: 100%;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(108, 92, 231, 0.4);
            border-color: var(--secondary-color);
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .btn-action {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.4);
            color: white;
        }

        .welcome-message {
            background: rgba(108, 92, 231, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .btn-success {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            font-weight: 600;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.4);
        }

        .btn-info {
            background: linear-gradient(45deg, var(--secondary-color), #00b8ba);
            border: none;
            font-weight: 600;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 210, 211, 0.4);
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-gamepad"></i> ADMIN PANEL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_participants_edit_delete.php">
                            <i class="fas fa-users"></i> Participants
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="search_form.php">
                            <i class="fas fa-search"></i> Search
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="dashboard-header">
            <h1 class="dashboard-title">
                <i class="fas fa-chart-line"></i> Admin Dashboard
            </h1>
            <div class="welcome-message">
                <p class="mb-0">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong>!</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Total Participants -->
            <div class="col-md-4 mb-3">
                <a href="view_participants_edit_delete.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <i class="fas fa-users stat-icon" style="font-size: 3rem; color: var(--primary-color);"></i>
                        <div class="stat-number"><?php echo $total_participants; ?></div>
                        <div class="stat-label">Total Participants</div>
                    </div>
                </a>
            </div>

            <!-- Total Teams -->
            <div class="col-md-4 mb-3">
                <a href="view_teams.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <i class="fas fa-people-group stat-icon" style="font-size: 3rem; color: var(--secondary-color);"></i>
                        <div class="stat-number"><?php echo $total_teams; ?></div>
                        <div class="stat-label">Total Teams</div>
                    </div>
                </a>
            </div>

            <!-- Merchandise Registrations -->
            <div class="col-md-4 mb-3">
                <a href="view_merchandise.php" class="text-decoration-none text-white">
                    <div class="stat-card">
                        <i class="fas fa-gift stat-icon" style="font-size: 3rem; color: var(--success-color);"></i>
                        <div class="stat-number"><?php echo $total_registrations; ?></div>
                        <div class="stat-label">Merchandise Registrations</div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <h3>Manage Participants</h3>
                    <p>View, edit, and delete participant records. Update kills and deaths statistics.</p>
                    <a href="view_participants_edit_delete.php" class="btn-action">
                        <i class="fas fa-arrow-right"></i> Manage
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3>Search System</h3>
                    <p>Search for participants by email or gamertag. Find teams and view statistics.</p>
                    <a href="search_form.php" class="btn-action">
                        <i class="fas fa-arrow-right"></i> Search
                    </a>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>View Reports</h3>
                    <p>Generate reports and view tournament analytics and performance metrics.</p>
                    <a href="view_reports.php" class="btn-action">
                         <i class="fas fa-arrow-right"></i> Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4 mb-5">
            <div class="col-12">
                <div class="action-card">
                    <h3><i class="fas fa-clock"></i> Quick Actions</h3>
                    <div class="row mt-3">
                        <div class="col-md-6 mb-2">
                            <a href="view_participants_edit_delete.php" class="btn btn-success w-100">
                                <i class="fas fa-plus"></i> Update Participant Scores
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="search_form.php" class="btn btn-info w-100">
                                <i class="fas fa-search"></i> Search Database
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>