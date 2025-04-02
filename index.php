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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
    <style>
        :root {
            /* Dark theme variables */
            --primary-color: #0ea5e9;
            --secondary-color: #0284c7;
            --accent-color: #38bdf8;
            --background-color: #0f172a;
            --card-background: #1e293b;
            --text-primary: #ffffff;
            --text-secondary: #e2e8f0;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        :root[data-theme="light"] {
            --primary-color: #0ea5e9;
            --secondary-color: #0284c7;
            --accent-color: #38bdf8;
            --background-color: #f1f5f9;
            --card-background: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #475569;
            --success-color: #22c55e;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        body {
            background: var(--background-color);
            color: var(--text-primary);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            min-height: 100vh;
            position: relative;
            transition: all 0.3s ease;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(14, 165, 233, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 0%, rgba(56, 189, 248, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 0% 100%, rgba(2, 132, 199, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(14, 165, 233, 0.15) 0%, transparent 50%);
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, rgba(255, 255, 255, 0.03) 0%, transparent 70%);
            z-index: -1;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
        }

        /* Dark mode card */
        :root[data-theme="dark"] .card {
            background: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Light mode card */
        :root[data-theme="light"] .card {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .card-body {
            padding: 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
            color: white !important;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), rgba(255,255,255,0));
            z-index: 1;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at top right, rgba(255,255,255,0.1), transparent 70%);
            z-index: 1;
        }

        .stat-card .card-title {
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .stat-card .display-4 {
            font-weight: 700;
            margin: 1rem 0;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-card i {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 2.5rem;
            opacity: 0.1;
            z-index: 1;
            transform: rotate(-15deg);
            transition: transform 0.3s ease;
        }

        .stat-card:hover i {
            transform: rotate(0deg) scale(1.1);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn i {
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .list-group-item {
            border: none;
            padding: 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Dark mode styles */
        :root[data-theme="dark"] .list-group-item {
            background-color: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        :root[data-theme="dark"] .list-group-item:hover {
            background-color: rgba(30, 41, 59, 0.95);
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Light mode styles */
        :root[data-theme="light"] .list-group-item {
            background-color: rgba(14, 165, 233, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        :root[data-theme="light"] .list-group-item:hover {
            background-color: rgba(2, 132, 199, 0.95);
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Low stock alert text color */
        .list-group-item i,
        .list-group-item span {
            color: #ffffff !important;
        }

        .list-group-item:hover i,
        .list-group-item:hover span {
            color: #ffffff !important;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 1rem;
            }
            
            .card {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-mobile-alt"></i>
                <span>Phone Inventory Management System</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="phones/index.php">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Phones</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories/index.php">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="transactions/index.php">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <button class="theme-toggle" id="themeToggle">
                            <i class="fas fa-moon"></i>
                            <span class="d-none d-md-inline">Theme</span>
                        </button>
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
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Low Stock Items</h5>
                        <p class="display-4"><?php echo $low_stock; ?></p>
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h5 class="card-title">Total Inventory Value</h5>
                        <p class="display-4">₱<?php echo number_format($total_value, 2); ?></p>
                        <i class="fas fa-peso-sign"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="fas fa-bolt"></i>
                            Quick Actions
                        </h5>
                        <div class="d-grid gap-3">
                            <a href="phones/create.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                <span>Add New Phone</span>
                            </a>
                            <a href="transactions/create.php" class="btn btn-primary">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Record Transaction</span>
                            </a>
                            <a href="categories/create.php" class="btn btn-primary">
                                <i class="fas fa-tag"></i>
                                <span>Add New Category</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="section-title">
                            <i class="fas fa-exclamation-circle"></i>
                            Low Stock Alert
                        </h5>
                        <?php
                        $low_stock_items = mysqli_query($conn, "SELECT brand, model, quantity FROM phones WHERE quantity < 5 ORDER BY quantity ASC LIMIT 5");
                        if (mysqli_num_rows($low_stock_items) > 0) {
                            echo '<div class="list-group">';
                            while ($item = mysqli_fetch_assoc($low_stock_items)) {
                                echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                                echo '<div>';
                                echo '<i class="fas fa-mobile-alt me-2 text-white"></i>';
                                echo '<span class="text-white">' . $item['brand'] . ' ' . $item['model'] . '</span>';
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
    <script>
        // Theme toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('themeToggle');
            const themeIcon = themeToggle.querySelector('i');
            const root = document.documentElement;
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme') || 'dark';
            root.setAttribute('data-theme', savedTheme);
            updateThemeIcon(savedTheme);

            themeToggle.addEventListener('click', function() {
                const currentTheme = root.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                root.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeIcon(newTheme);
            });

            function updateThemeIcon(theme) {
                themeIcon.className = theme === 'dark' ? 'fas fa-moon' : 'fas fa-sun';
            }
        });
    </script>
</body>
</html> 