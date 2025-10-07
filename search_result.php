<?php
require_once 'dbconnect.php';

$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

$results = [];
$search_performed = false;
$search_info = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_type = $_POST['search_type'] ?? '';
    
    if ($search_type == 'participant') {
        $search_by = mysqli_real_escape_string($conn, $_POST['search_by']);
        $search_value = mysqli_real_escape_string($conn, $_POST['search_value']);
        
        if ($search_by == 'email') {
            $sql = "SELECT p.*, t.name as team_name 
                    FROM participant p 
                    LEFT JOIN team t ON p.team_id = t.id 
                    WHERE p.email LIKE '%$search_value%'";
            $search_info = "Searching participants by email: '$search_value'";
        } else if ($search_by == 'gamertag') {
            $sql = "SELECT p.*, t.name as team_name 
                    FROM participant p 
                    LEFT JOIN team t ON p.team_id = t.id 
                    WHERE p.gamertag LIKE '%$search_value%'";
            $search_info = "Searching participants by gamertag: '$search_value'";
        }
        
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
        $search_performed = true;
        
    } else if ($search_type == 'team') {
        $team_name = mysqli_real_escape_string($conn, $_POST['team_name']);
        
        $sql = "SELECT p.*, t.name as team_name, t.city 
                FROM participant p 
                INNER JOIN team t ON p.team_id = t.id 
                WHERE t.name LIKE '%$team_name%'";
        
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $results[] = $row;
        }
        $search_performed = true;
        $search_info = "Searching for team: '$team_name'";
    }
}

// Calculate team statistics if team search
$team_stats = [];
if ($search_type == 'team' && !empty($results)) {
    $total_kills = 0;
    $total_deaths = 0;
    foreach ($results as $member) {
        $total_kills += $member['kills'];
        $total_deaths += $member['deaths'];
    }
    $team_stats['total_kills'] = $total_kills;
    $team_stats['total_deaths'] = $total_deaths;
    $team_stats['kd_ratio'] = $total_deaths > 0 ? round($total_kills / $total_deaths, 2) : $total_kills;
    $team_stats['team_name'] = $results[0]['team_name'] ?? 'Unknown';
    $team_stats['city'] = $results[0]['city'] ?? 'Unknown';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00D2D3;
            --dark-bg: #0A0E27;
            --darker-bg: #060818;
            --success-color: #4CAF50;
            --warning-color: #FFC107;
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

        .results-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .results-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            margin-bottom: 2rem;
        }

        .results-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .table-responsive {
            margin-top: 1.5rem;
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

        .kd-ratio {
            font-weight: bold;
            color: var(--secondary-color);
        }

        .team-stats {
            background: rgba(108, 92, 231, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
        }

        .stat-item {
            display: inline-block;
            margin-right: 2rem;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .no-results {
            text-align: center;
            padding: 3rem;
            color: var(--warning-color);
        }

        .back-btn {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 10px 25px;
            color: white;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.4);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $is_admin ? 'admin_menu.php' : 'index.html'; ?>">
                <i class="fas fa-gamepad"></i> <?php echo $is_admin ? 'ADMIN PANEL' : 'UK E-SPORTS LEAGUE'; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_admin): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_menu.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="view_participants_edit_delete.php"><i class="fas fa-users"></i> Participants</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search_form.php"><i class="fas fa-search"></i> Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.html"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register_form.html"><i class="fas fa-user-plus"></i> Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="search_form.php"><i class="fas fa-search"></i> Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_login.html"><i class="fas fa-lock"></i> Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="results-container">
        <div class="results-card">
            <h1 class="results-title">
                <i class="fas fa-search"></i> Search Results
            </h1>
            
            <?php if ($search_performed): ?>
                <p class="text-muted"><?php echo htmlspecialchars($search_info); ?></p>
                
                <?php if (!empty($results)): ?>
                    <?php if ($search_type == 'team' && !empty($team_stats)): ?>
                        <div class="team-stats">
                            <h3><?php echo htmlspecialchars($team_stats['team_name']); ?></h3>
                            <p>City: <?php echo htmlspecialchars($team_stats['city']); ?></p>
                            <div class="stat-item">
                                <span>Total Kills:</span>
                                <span class="stat-value"><?php echo $team_stats['total_kills']; ?></span>
                            </div>
                            <div class="stat-item">
                                <span>Total Deaths:</span>
                                <span class="stat-value"><?php echo $team_stats['total_deaths']; ?></span>
                            </div>
                            <div class="stat-item">
                                <span>Team K/D Ratio:</span>
                                <span class="stat-value"><?php echo $team_stats['kd_ratio']; ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Gamertag</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Team</th>
                                    <th>Kills</th>
                                    <th>Deaths</th>
                                    <th>K/D Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $row): ?>
                                    <?php 
                                        $kd_ratio = $row['deaths'] > 0 ? 
                                            round($row['kills'] / $row['deaths'], 2) : 
                                            $row['kills'];
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['gamertag']); ?></td>
                                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                                        <td><?php echo htmlspecialchars($row['team_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo $row['kills']; ?></td>
                                        <td><?php echo $row['deaths']; ?></td>
                                        <td class="kd-ratio"><?php echo $kd_ratio; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-exclamation-circle" style="font-size: 3rem;"></i>
                        <h3>No Results Found</h3>
                        <p>Try adjusting your search criteria</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="no-results">
                    <p>No search performed yet</p>
                </div>
            <?php endif; ?>
            
            <div class="mt-4">
                <a href="search_form.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Search
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>