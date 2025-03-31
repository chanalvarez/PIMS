<?php
require_once "config/database.php";

// Get total number of phones
$total_phones = mysqli_query($conn, "SELECT COUNT(*) as total FROM phones")->fetch_assoc()['total'];

// Get low stock items (less than 5 units)
$low_stock = mysqli_query($conn, "SELECT COUNT(*) as total FROM phones WHERE quantity < 5")->fetch_assoc()['total'];

// Get total inventory value
$total_value = mysqli_query($conn, "SELECT SUM(price * quantity) as total FROM phones")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Inventory Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --background-color: #f8fafc;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
        }

        body {
            background-color: var(--background-color);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .navbar {
            background-color: var(--card-background) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 1rem 0;
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            background-color: var(--background-color);
            color: var(--primary-color) !important;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .stat-card .card-title {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
        }

        .stat-card .display-4 {
            font-weight: 700;
            margin: 1rem 0;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .list-group-item {
            border: none;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            background-color: var(--background-color);
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mobile-alt me-2"></i>Phone Inventory Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="phones/index.php">
                            <i class="fas fa-mobile-alt me-2"></i>Phones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories/index.php">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions/index.php">
                            <i class="fas fa-exchange-alt me-2"></i>Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Phones</h5>
                        <p class="display-4"><?php echo $total_phones; ?></p>
                        <i class="fas fa-mobile-alt position-absolute top-0 end-0 mt-3 me-3 fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock Items</h5>
                        <p class="display-4"><?php echo $low_stock; ?></p>
                        <i class="fas fa-exclamation-triangle position-absolute top-0 end-0 mt-3 me-3 fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Inventory Value</h5>
                        <p class="display-4">$<?php echo number_format($total_value, 2); ?></p>
                        <i class="fas fa-dollar-sign position-absolute top-0 end-0 mt-3 me-3 fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="section-title">Quick Actions</h5>
                        <div class="d-grid gap-3">
                            <a href="phones/create.php" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add New Phone
                            </a>
                            <a href="transactions/create.php" class="btn btn-primary">
                                <i class="fas fa-exchange-alt me-2"></i>Record Transaction
                            </a>
                            <a href="categories/create.php" class="btn btn-primary">
                                <i class="fas fa-tag me-2"></i>Add New Category
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="section-title">Low Stock Alert</h5>
                        <?php
                        $low_stock_items = mysqli_query($conn, "SELECT brand, model, quantity FROM phones WHERE quantity < 5 ORDER BY quantity ASC LIMIT 5");
                        if (mysqli_num_rows($low_stock_items) > 0) {
                            echo '<div class="list-group">';
                            while ($item = mysqli_fetch_assoc($low_stock_items)) {
                                echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<div>';
                                echo '<i class="fas fa-mobile-alt me-2 text-primary"></i>';
                                echo $item['brand'] . ' ' . $item['model'];
                                echo '</div>';
                                echo '<span class="badge bg-danger">' . $item['quantity'] . ' units</span>';
                                echo '</div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p class="text-muted">No low stock items found.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 