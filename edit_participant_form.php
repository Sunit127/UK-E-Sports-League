<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Get participant ID
$participant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($participant_id <= 0) {
    header('Location: view_participants_edit_delete.php?error=Invalid participant ID');
    exit();
}

// Get participant data
$sql = "SELECT p.*, t.name as team_name 
        FROM participant p 
        LEFT JOIN team t ON p.team_id = t.id 
        WHERE p.id = $participant_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    header('Location: view_participants_edit_delete.php?error=Participant not found');
    exit();
}

$participant = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant - UK E-Sports League</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6C5CE7;
            --secondary-color: #00D2D3;
            --dark-bg: #0A0E27;
            --darker-bg: #060818;
            --success-color: #4CAF50;
            --error-color: #FF5252;
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

        .edit-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .edit-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid rgba(108, 92, 231, 0.3);
            color: white;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--secondary-color);
            box-shadow: 0 0 15px rgba(0, 210, 211, 0.3);
            color: white;
        }

        .form-control:disabled {
            background: rgba(255, 255, 255, 0.03);
            color: #999;
            cursor: not-allowed;
        }

        .form-label {
            color: var(--secondary-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .btn-update {
            background: linear-gradient(45deg, var(--success-color), #66BB6A);
            border: none;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(76, 175, 80, 0.4);
        }

        .btn-cancel {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: var(--secondary-color);
            color: white;
        }

        .info-box {
            background: rgba(108, 92, 231, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
        }

        .error-text {
            color: var(--error-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .form-control.is-invalid {
            border-color: var(--error-color);
        }

        .current-stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 2rem;
            text-align: center;
        }

        .stat-box {
            background: rgba(0, 210, 211, 0.1);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid rgba(0, 210, 211, 0.3);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--secondary-color);
        }

        .stat-label {
            color: #B8BCC8;
            font-size: 0.9rem;
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
                        <a class="nav-link active" href="view_participants_edit_delete.php">
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

    <div class="edit-container">
        <div class="edit-card">
            <h2 class="form-title">
                <i class="fas fa-user-edit"></i> Edit Participant Scores
            </h2>

            <div class="info-box">
                <h5><?php echo htmlspecialchars($participant['gamertag']); ?></h5>
                <p class="mb-0">Name: <?php echo htmlspecialchars($participant['name']); ?></p>
                <p class="mb-0">Team: <?php echo htmlspecialchars($participant['team_name'] ?? 'N/A'); ?></p>
            </div>

            <div class="current-stats">
                <div class="stat-box">
                    <div class="stat-value"><?php echo $participant['kills']; ?></div>
                    <div class="stat-label">Current Kills</div>
                </div>
                <div class="stat-box">
                    <div class="stat-value"><?php echo $participant['deaths']; ?></div>
                    <div class="stat-label">Current Deaths</div>
                </div>
                <div class="stat-box">
                    <?php 
                        $kd_ratio = $participant['deaths'] > 0 ? 
                            round($participant['kills'] / $participant['deaths'], 2) : 
                            $participant['kills'];
                    ?>
                    <div class="stat-value"><?php echo $kd_ratio; ?></div>
                    <div class="stat-label">K/D Ratio</div>
                </div>
            </div>

            <form id="editForm" action="edit_participant.php" method="POST" novalidate>
                <input type="hidden" name="participant_id" value="<?php echo $participant['id']; ?>">

                <!-- Read-only fields -->
                <div class="mb-3">
                    <label for="gamertag" class="form-label">Gamertag</label>
                    <input type="text" class="form-control" id="gamertag" 
                           value="<?php echo htmlspecialchars($participant['gamertag']); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" 
                           value="<?php echo htmlspecialchars($participant['name']); ?>" disabled>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" 
                           value="<?php echo htmlspecialchars($participant['email']); ?>" disabled>
                </div>

                <!-- Editable fields -->
                <div class="mb-3">
                    <label for="kills" class="form-label">
                        Kills <span style="color: var(--error-color);">*</span>
                    </label>
                    <input type="number" class="form-control" id="kills" name="kills" 
                           value="<?php echo $participant['kills']; ?>" min="0" required>
                    <div class="error-text">Please enter a valid number of kills (minimum 0)</div>
                </div>

                <div class="mb-3">
                    <label for="deaths" class="form-label">
                        Deaths <span style="color: var(--error-color);">*</span>
                    </label>
                    <input type="number" class="form-control" id="deaths" name="deaths" 
                           value="<?php echo $participant['deaths']; ?>" min="0" required>
                    <div class="error-text">Please enter a valid number of deaths (minimum 0)</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
                    <button type="submit" class="btn btn-update">
                        <i class="fas fa-save"></i> Update Scores
                    </button>
                    <a href="view_participants_edit_delete.php" class="btn btn-cancel">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            let isValid = true;
            const form = this;
            
            // Reset previous errors
            form.querySelectorAll('.form-control').forEach(field => {
                if (!field.disabled) {
                    field.classList.remove('is-invalid');
                    const errorText = field.nextElementSibling;
                    if (errorText && errorText.classList.contains('error-text')) {
                        errorText.style.display = 'none';
                    }
                }
            });
            
            // Validate kills
            const kills = form.kills.value;
            if (kills === '' || parseInt(kills) < 0 || isNaN(parseInt(kills))) {
                showError(form.kills);
                isValid = false;
            }
            
            // Validate deaths
            const deaths = form.deaths.value;
            if (deaths === '' || parseInt(deaths) < 0 || isNaN(parseInt(deaths))) {
                showError(form.deaths);
                isValid = false;
            }
            
            if (isValid) {
                form.submit();
            }
        });
        
        function showError(field) {
            field.classList.add('is-invalid');
            const errorText = field.nextElementSibling;
            if (errorText && errorText.classList.contains('error-text')) {
                errorText.style.display = 'block';
            }
        }
    </script>
</body>
</html>