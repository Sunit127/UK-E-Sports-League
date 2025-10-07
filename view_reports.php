<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Overall Statistics
$total_participants = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM participant"))['count'];
$total_teams = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM team"))['count'];
$total_kills = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(kills) as total FROM participant"))['total'] ?? 0;
$total_deaths = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(deaths) as total FROM participant"))['total'] ?? 0;
$total_merchandise = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM merchandise"))['count'];

// Average K/D Ratio
$avg_kd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(kills/NULLIF(deaths, 0)) as avg_kd FROM participant"))['avg_kd'];
$avg_kd = $avg_kd ? round($avg_kd, 2) : 0;

// Top 5 Players by K/D Ratio
$top_players_query = "SELECT p.*, t.name as team_name, 
                      (p.kills/NULLIF(p.deaths, 0)) as kd_ratio 
                      FROM participant p 
                      LEFT JOIN team t ON p.team_id = t.id 
                      ORDER BY kd_ratio DESC 
                      LIMIT 5";
$top_players = mysqli_query($conn, $top_players_query);

// Top 5 Players by Kills
$top_killers_query = "SELECT p.*, t.name as team_name 
                      FROM participant p 
                      LEFT JOIN team t ON p.team_id = t.id 
                      ORDER BY p.kills DESC 
                      LIMIT 5";
$top_killers = mysqli_query($conn, $top_killers_query);

// Team Performance
$team_performance_query = "SELECT t.name, t.city,
                           COUNT(p.id) as member_count,
                           SUM(p.kills) as total_kills,
                           SUM(p.deaths) as total_deaths,
                           AVG(p.kills/NULLIF(p.deaths, 0)) as avg_kd
                           FROM team t
                           LEFT JOIN participant p ON t.id = p.team_id
                           GROUP BY t.id
                           ORDER BY avg_kd DESC";
$team_performance = mysqli_query($conn, $team_performance_query);

// Merchandise Statistics by Size
$merch_by_size = mysqli_query($conn, "SELECT tshirt_size, COUNT(*) as count 
                                      FROM merchandise 
                                      GROUP BY tshirt_size 
                                      ORDER BY count DESC");

// Recent Registrations (Last 7 days)
$recent_registrations = mysqli_fetch_assoc(mysqli_query($conn, 
    "SELECT COUNT(*) as count 
     FROM merchandise 
     WHERE registered_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['count'];

// City Distribution
$city_distribution = mysqli_query($conn, 
    "SELECT t.city, COUNT(p.id) as participant_count 
     FROM team t 
     LEFT JOIN participant p ON t.id = p.team_id 
     GROUP BY t.city 
     ORDER BY participant_count DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports - UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00D2D3;
            --dark-bg: #0A0E27;
            --darker-bg: #060818;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
            --danger-color: #FF5252;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--darker-bg) 100%);
            color: white;
            min-height: 100vh;
            padding-top: 80px;
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
            -webkit-text-fill-color: transparent;
        }

        .page-title {
            font-size: 2.5rem;
            margin: 2rem 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
        }

        .report-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            transition: all 0.3s;
        }

        .report-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(108, 92, 231, 0.2);
        }

        .stat-box {
            background: linear-gradient(135deg, rgba(108, 92, 231, 0.2), rgba(0, 210, 211, 0.2));
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .stat-label {
            color: #B8BCC8;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .table {
            color: white;
        }

        .table thead {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s;
        }

        .table tbody tr:hover {
            background: rgba(108, 92, 231, 0.2);
        }

        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
        }

        .badge-rank {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
        }

        .rank-1 { background: linear-gradient(45deg, #FFD700, #FFA500); }
        .rank-2 { background: linear-gradient(45deg, #C0C0C0, #808080); }
        .rank-3 { background: linear-gradient(45deg, #CD7F32, #8B4513); }

        .export-btn {
            background: linear-gradient(45deg, var(--success-color), #66BB6A);
            border: none;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
            color: white;
        }

        .print-btn {
            background: linear-gradient(45deg, var(--warning-color), #FFB300);
            border: none;
            padding: 10px 25px;
            color: var(--dark-bg);
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .print-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 193, 7, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(108, 92, 231, 0.5);
            color: white;
            padding: 10px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: rgba(108, 92, 231, 0.2);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.3);
            color: white;
        }

        .btn-lg {
            padding: 12px 30px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="admin_menu.php">
                <i class="fas fa-gamepad"></i> ADMIN PANEL
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_menu.php">
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
                        <a class="nav-link active" href="view_reports.php">
                            <i class="fas fa-chart-line"></i> Reports
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
        <h1 class="page-title">
            <i class="fas fa-chart-line"></i> Tournament Analytics & Reports
        </h1>

        
        <!-- Overall Statistics -->
        <div class="report-card">
            <h3><i class="fas fa-chart-bar"></i> Overall Statistics</h3>
            <div class="row mt-3">
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $total_participants; ?></div>
                        <div class="stat-label">Participants</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $total_teams; ?></div>
                        <div class="stat-label">Teams</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo number_format($total_kills); ?></div>
                        <div class="stat-label">Total Kills</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo number_format($total_deaths); ?></div>
                        <div class="stat-label">Total Deaths</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $avg_kd; ?></div>
                        <div class="stat-label">Avg K/D</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <div class="stat-box">
                        <div class="stat-value"><?php echo $total_merchandise; ?></div>
                        <div class="stat-label">Merch Orders</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top Players by K/D -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4><i class="fas fa-trophy"></i> Top 5 Players by K/D Ratio</h4>
                    <table class="table table-dark table-sm mt-3">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Gamertag</th>
                                <th>Team</th>
                                <th>K/D Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            while ($player = mysqli_fetch_assoc($top_players)): 
                                $kd = $player['deaths'] > 0 ? round($player['kills'] / $player['deaths'], 2) : $player['kills'];
                                $badge_class = $rank <= 3 ? "badge-rank rank-$rank" : "";
                            ?>
                                <tr>
                                    <td>
                                        <?php if ($rank <= 3): ?>
                                            <span class="<?php echo $badge_class; ?>">#<?php echo $rank; ?></span>
                                        <?php else: ?>
                                            #<?php echo $rank; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($player['gamertag']); ?></td>
                                    <td><?php echo htmlspecialchars($player['team_name'] ?? 'N/A'); ?></td>
                                    <td style="color: var(--secondary-color); font-weight: bold;"><?php echo $kd; ?></td>
                                </tr>
                            <?php 
                                $rank++;
                            endwhile; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Players by Kills -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4><i class="fas fa-crosshairs"></i> Top 5 Players by Kills</h4>
                    <table class="table table-dark table-sm mt-3">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Gamertag</th>
                                <th>Team</th>
                                <th>Kills</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $rank = 1;
                            while ($killer = mysqli_fetch_assoc($top_killers)): 
                                $badge_class = $rank <= 3 ? "badge-rank rank-$rank" : "";
                            ?>
                                <tr>
                                    <td>
                                        <?php if ($rank <= 3): ?>
                                            <span class="<?php echo $badge_class; ?>">#<?php echo $rank; ?></span>
                                        <?php else: ?>
                                            #<?php echo $rank; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($killer['gamertag']); ?></td>
                                    <td><?php echo htmlspecialchars($killer['team_name'] ?? 'N/A'); ?></td>
                                    <td style="color: var(--danger-color); font-weight: bold;"><?php echo $killer['kills']; ?></td>
                                </tr>
                            <?php 
                                $rank++;
                            endwhile; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        <div class="report-card">
            <h4><i class="fas fa-people-group"></i> Team Performance Analysis</h4>
            <div class="table-responsive mt-3">
                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Team Name</th>
                            <th>City</th>
                            <th>Members</th>
                            <th>Total Kills</th>
                            <th>Total Deaths</th>
                            <th>Team K/D Ratio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($team = mysqli_fetch_assoc($team_performance)): 
                            $team_kd = $team['total_deaths'] > 0 ? round($team['total_kills'] / $team['total_deaths'], 2) : $team['total_kills'];
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($team['name']); ?></td>
                                <td><?php echo htmlspecialchars($team['city']); ?></td>
                                <td><?php echo $team['member_count']; ?></td>
                                <td><?php echo $team['total_kills'] ?? 0; ?></td>
                                <td><?php echo $team['total_deaths'] ?? 0; ?></td>
                                <td style="color: var(--secondary-color); font-weight: bold;"><?php echo $team_kd; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="row">
            <!-- City Distribution Chart -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4><i class="fas fa-map"></i> Participants by City</h4>
                    <div class="chart-container">
                        <canvas id="cityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Merchandise Size Distribution -->
            <div class="col-md-6">
                <div class="report-card">
                    <h4><i class="fas fa-tshirt"></i> Merchandise Size Distribution</h4>
                    <div class="chart-container">
                        <canvas id="merchChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Merchandise Summary -->
        <div class="report-card">
            <h4><i class="fas fa-gift"></i> Merchandise Summary</h4>
            <div class="row mt-3">
                <div class="col-md-6">
                    <p><strong>Total Registrations:</strong> <?php echo $total_merchandise; ?></p>
                    <p><strong>Recent Registrations (Last 7 days):</strong> <?php echo $recent_registrations; ?></p>
                </div>
                <div class="col-md-6">
                    <table class="table table-dark table-sm">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            mysqli_data_seek($merch_by_size, 0);
                            while ($size = mysqli_fetch_assoc($merch_by_size)): 
                            ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($size['tshirt_size']); ?></td>
                                    <td><?php echo $size['count']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Navigation and Export Options -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="admin_menu.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <div>
                <button onclick="window.print()" class="print-btn">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <button onclick="exportToCSV()" class="export-btn">
                    <i class="fas fa-download"></i> Export to CSV
                </button>
            </div>
        </div>


        <!-- Back to Dashboard Button at Bottom
        <div class="text-center mb-4 mt-4">
            <a href="admin_menu.php" class="btn btn-lg btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div> -->

    <script>
        // City Distribution Chart
        <?php
        $cities = [];
        $city_counts = [];
        mysqli_data_seek($city_distribution, 0);
        while ($city = mysqli_fetch_assoc($city_distribution)) {
            $cities[] = $city['city'];
            $city_counts[] = $city['participant_count'];
        }
        ?>
        
        const cityCtx = document.getElementById('cityChart').getContext('2d');
        new Chart(cityCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($cities); ?>,
                datasets: [{
                    label: 'Participants',
                    data: <?php echo json_encode($city_counts); ?>,
                    backgroundColor: 'rgba(108, 92, 231, 0.6)',
                    borderColor: 'rgba(108, 92, 231, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    },
                    x: {
                        ticks: { color: '#fff' },
                        grid: { color: 'rgba(255, 255, 255, 0.1)' }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: '#fff' }
                    }
                }
            }
        });

        // Merchandise Size Chart
        <?php
        $sizes = [];
        $size_counts = [];
        mysqli_data_seek($merch_by_size, 0);
        while ($size = mysqli_fetch_assoc($merch_by_size)) {
            $sizes[] = $size['tshirt_size'];
            $size_counts[] = $size['count'];
        }
        ?>
        
        const merchCtx = document.getElementById('merchChart').getContext('2d');
        new Chart(merchCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($sizes); ?>,
                datasets: [{
                    data: <?php echo json_encode($size_counts); ?>,
                    backgroundColor: [
                        'rgba(108, 92, 231, 0.8)',
                        'rgba(0, 210, 211, 0.8)',
                        'rgba(76, 175, 80, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(255, 82, 82, 0.8)',
                        'rgba(156, 39, 176, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#fff' }
                    }
                }
            }
        });

        // Export to CSV function
        function exportToCSV() {
            alert('Export functionality would download tournament data as CSV file');
            // In a real implementation, this would trigger a PHP script to generate and download CSV
        }
    </script>
</body>
</html>