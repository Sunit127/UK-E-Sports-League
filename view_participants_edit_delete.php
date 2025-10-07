<?php
require_once 'dbconnect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.html');
    exit();
}

// Get all participants with team info
$sql = "SELECT p.*, t.name as team_name 
        FROM participant p 
        LEFT JOIN team t ON p.team_id = t.id 
        ORDER BY p.id";
$result = mysqli_query($conn, $sql);
$participants = [];
while ($row = mysqli_fetch_assoc($result)) {
    $participants[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participants - UK E-Sports League</title>
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

        .page-title {
            font-size: 2.5rem;
            margin: 2rem 0;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-align: center;
            animation: fadeInDown 1s ease;
        }

        .table-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(108, 92, 231, 0.3);
            animation: fadeInUp 1s ease;
        }

        .table {
            color: white;
            transition: all 0.3s ease;
        }

        .table thead {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
        }

        .table tbody tr {
            background: rgba(255, 255, 255, 0.02);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(108, 92, 231, 0.2);
            transform: scale(1.01);
            box-shadow: 0 5px 20px rgba(108, 92, 231, 0.4);
        }

        .btn-edit {
            background: var(--success-color);
            border: none;
            padding: 5px 15px;
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-edit:hover {
            background: #45a049;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-delete {
            background: var(--error-color);
            border: none;
            padding: 5px 15px;
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: #f44336;
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 5px 20px rgba(244, 67, 54, 0.4);
        }

        .btn-kd {
            border: none;
            padding: 3px 8px;
            margin: 0 2px;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .btn-inc {
            background: linear-gradient(45deg, #4CAF50, #2E7D32);
            color: white;
        }

        .btn-dec {
            background: linear-gradient(45deg, #E53935, #D32F2F);
            color: white;
        }

        .btn-inc:hover {
            transform: scale(1.2);
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.7);
        }

        .btn-dec:hover {
            transform: scale(1.2);
            box-shadow: 0 5px 20px rgba(229, 57, 53, 0.7);
        }

        .kd-ratio {
            font-weight: bold;
            color: var(--secondary-color);
            transition: all 0.3s ease;
        }

        .kd-ratio:hover {
            color: var(--warning-color);
            transform: scale(1.1);
        }

        /* Animations */
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInDown {
            0% { opacity: 0; transform: translateY(-20px); }
            100% { opacity: 1; transform: translateY(0); }
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_menu.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="view_participants_edit_delete.php"><i class="fas fa-users"></i> Participants</a></li>
                    <li class="nav-item"><a class="nav-link" href="search_form.php"><i class="fas fa-search"></i> Search</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="page-title"><i class="fas fa-users"></i> Manage Participants</h1>

        <div class="table-card">
            <div class="table-responsive">
                <table class="table table-dark table-striped text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Gamertag</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Team</th>
                            <th>Kills</th>
                            <th>Deaths</th>
                            <th>K/D Ratio</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                            <?php $kd_ratio = $p['deaths'] > 0 ? round($p['kills'] / $p['deaths'], 2) : $p['kills']; ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['gamertag']) ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['email']) ?></td>
                                <td><?= htmlspecialchars($p['team_name'] ?? 'N/A') ?></td>
                                
                                <!-- Kills with + / - -->
                                <td>
                                    <?= $p['kills'] ?>
                                    <a href="update_kd.php?id=<?= $p['id'] ?>&field=kills&action=inc" class="btn-kd btn-inc">+</a>
                                    <a href="update_kd.php?id=<?= $p['id'] ?>&field=kills&action=dec" class="btn-kd btn-dec">-</a>
                                </td>
                                
                                <!-- Deaths with + / - -->
                                <td>
                                    <?= $p['deaths'] ?>
                                    <a href="update_kd.php?id=<?= $p['id'] ?>&field=deaths&action=inc" class="btn-kd btn-inc">+</a>
                                    <a href="update_kd.php?id=<?= $p['id'] ?>&field=deaths&action=dec" class="btn-kd btn-dec">-</a>
                                </td>

                                <td class="kd-ratio"><?= $kd_ratio ?></td>
                                <td>
                                    <a href="edit_participant_form.php?id=<?= $p['id'] ?>" class="btn btn-edit btn-sm"><i class="fas fa-edit"></i> Edit</a>
                                    <button onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['gamertag']) ?>')" class="btn btn-delete btn-sm"><i class="fas fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title text-warning"><i class="fas fa-exclamation-triangle"></i> Confirm Deletion</h5></div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this participant?</p>
                    <p class="text-danger"><strong id="deleteParticipantInfo"></strong></p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="delete.php">
                        <input type="hidden" name="participant_id" id="deleteParticipantId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, gamertag) {
            document.getElementById('deleteParticipantId').value = id;
            document.getElementById('deleteParticipantInfo').textContent = 'Gamertag: ' + gamertag;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
