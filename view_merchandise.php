<?php
require_once 'dbconnect.php';

// Fetch merchandise registrations
$sql = "SELECT * FROM merchandise ORDER BY registered_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Merchandise - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0A0E27 0%, #060818 100%);
            color: white;
            min-height: 100vh;
            padding-top: 80px;
            opacity: 0;
            animation: pageFade 0.8s ease forwards;
        }
        @keyframes pageFade {
            to { opacity: 1; }
        }

        h2 {
            text-align: center;
            margin-bottom: 2rem;
            background: linear-gradient(45deg, #6C5CE7, #00D2D3);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            opacity: 0;
            transform: translateY(-30px);
            animation: slideDown 0.8s ease forwards;
            animation-delay: 0.2s;
        }
        @keyframes slideDown {
            to { opacity: 1; transform: translateY(0); }
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 210, 211, 0.3);
            opacity: 0;
            transform: translateY(30px);
            animation: fadeUp 0.8s ease forwards;
            animation-delay: 0.5s;
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }

        .table tbody tr {
            opacity: 0;
            transform: translateY(20px);
            animation: rowFade 0.6s ease forwards;
        }
        .table tbody tr:nth-child(1) { animation-delay: 0.7s; }
        .table tbody tr:nth-child(2) { animation-delay: 0.9s; }
        .table tbody tr:nth-child(3) { animation-delay: 1.1s; }
        .table tbody tr:nth-child(4) { animation-delay: 1.3s; }
        @keyframes rowFade {
            to { opacity: 1; transform: translateY(0); }
        }

        .table thead {
            background: rgba(0, 210, 211, 0.2);
        }
        .table tbody tr:hover {
            background: rgba(0, 210, 211, 0.1);
            transform: scale(1.01);
        }

        .btn-action {
            margin: 2px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            padding: 6px 12px;
            transition: all 0.3s ease;
        }
        .btn-edit {
            background: linear-gradient(45deg, #FFC107, #FF9800);
            color: #fff;
        }
        .btn-edit:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(255, 193, 7, 0.5);
        }
        .btn-delete {
            background: linear-gradient(45deg, #E53935, #D32F2F);
            color: #fff;
        }
        .btn-delete:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(229, 57, 53, 0.5);
        }

        .btn-back {
            background: linear-gradient(45deg, #6C5CE7, #00D2D3);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s ease;
            opacity: 0;
            animation: fadeUp 0.8s ease forwards;
            animation-delay: 1.4s;
        }
        .btn-back:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 210, 211, 0.4);
        }
    </style>
</head>
<body>
<div class="container">
    <h2><i class="fas fa-gift"></i> Manage Merchandise</h2>
    <table class="table table-dark table-striped text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>T-Shirt Size</th>
                <th>Registered At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $delay = 0.7; while ($row = mysqli_fetch_assoc($result)): ?>
                <tr style="animation-delay: <?= $delay ?>s">
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['address']) ?></td>
                    <td><?= $row['tshirt_size'] ?></td>
                    <td><?= $row['registered_at'] ?></td>
                    <td>
                        <a href="edit_merchandise.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-action btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete.php?type=merchandise&id=<?= $row['id'] ?>" 
                           onclick="return confirm('Are you sure you want to delete this registration?');" 
                           class="btn btn-sm btn-action btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php $delay += 0.2; endwhile; ?>
        </tbody>
    </table>
    <div class="text-center mt-4">
        <a href="admin_menu.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>
</body>
</html>
