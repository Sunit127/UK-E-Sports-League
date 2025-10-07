<?php
require_once 'dbconnect.php';

// Check if this is an admin page access
$is_admin = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00D2D3;
            --dark-bg: #0A0E27;
            --darker-bg: #060818;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--darker-bg) 100%);
            color: white;
            min-height: 100vh;
            padding-top: 80px;
            padding-bottom: 40px;
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

        .search-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            margin-bottom: 2rem;
        }

        .search-title {
            font-size: 2rem;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(108, 92, 231, 0.3);
            color: white;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--secondary-color);
            box-shadow: 0 0 15px rgba(0, 210, 211, 0.3);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .form-select option {
            background: var(--dark-bg);
            color: white;
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .btn-search {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.4);
        }

        .search-type-card {
            background: rgba(108, 92, 231, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            transition: all 0.3s;
        }

        .search-type-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .back-dashboard-btn {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            margin-bottom: 2rem;
        }

        .back-dashboard-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
            color: white;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .admin-header {
                flex-direction: column;
                gap: 1rem;
            }
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
                            <a class="nav-link active" href="search_form.php">
                                <i class="fas fa-search"></i> Search
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.html"><i class="fas fa-home"></i> Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register_form.html"><i class="fas fa-user-plus"></i> Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="search_form.php"><i class="fas fa-search"></i> Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_login.html"><i class="fas fa-lock"></i> Admin</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="search-container">
        <div class="admin-header">
            <h1 class="search-title mb-0">
                <i class="fas fa-search"></i> Search Database
            </h1>
            
        </div>

        <div class="row">
            <!-- Search Participant -->
            <div class="col-md-6">
                <div class="search-card">
                    <h3><i class="fas fa-user"></i> Search Participant</h3>
                    <form action="search_result.php" method="POST">
                        <input type="hidden" name="search_type" value="participant">
                        
                        <div class="mb-3">
                            <label for="search_by" class="form-label">Search By</label>
                            <select class="form-select" id="search_by" name="search_by" required>
                                <option value="">Select search type</option>
                                <option value="email">Email Address</option>
                                <option value="gamertag">Gamertag</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="search_value" class="form-label">Search Value</label>
                            <input type="text" class="form-control" id="search_value" name="search_value" 
                                   placeholder="Enter search term" required>
                        </div>

                        <button type="submit" class="btn btn-search w-100">
                            <i class="fas fa-search"></i> Search Participant
                        </button>
                    </form>
                </div>
            </div>

            <!-- Search Team -->
            <div class="col-md-6">
                <div class="search-card">
                    <h3><i class="fas fa-users"></i> Search Team</h3>
                    <form action="search_result.php" method="POST">
                        <input type="hidden" name="search_type" value="team">
                        
                        <div class="mb-3">
                            <label for="team_name" class="form-label">Team Name</label>
                            <input type="text" class="form-control" id="team_name" name="team_name" 
                                   placeholder="Enter team name" required>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> Search will show all team members and calculate team statistics
                            </small>
                        </div>

                        <button type="submit" class="btn btn-search w-100">
                            <i class="fas fa-search"></i> Search Team
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Search Tips -->
        <div class="search-card">
            <h4><i class="fas fa-lightbulb"></i> Search Tips</h4>
            <ul>
                <li>Participant search is case-insensitive and supports partial matches</li>
                <li>Team search will display all members with individual and team K/D ratios</li>
                <li>Email search requires at least part of the email address</li>
                <li>Gamertag search supports partial matching</li>
            </ul>
        </div>
        <?php if ($is_admin): ?>
                <a href="admin_menu.php" class="back-dashboard-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>