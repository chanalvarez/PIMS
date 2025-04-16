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
                            <i class="fas fa-exclamation-triangle"></i>
                            Low Stock Alert
                        </h5>
                        <?php 
                        // Get low stock phones for the list
                        $low_stock_phones = mysqli_query($conn, "SELECT * FROM phones WHERE quantity < 5 ORDER BY quantity ASC");
                        if (mysqli_num_rows($low_stock_phones) > 0): ?>
                            <div class="list-group">
                                <?php while ($phone = mysqli_fetch_assoc($low_stock_phones)): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></span>
                                        <span class="badge bg-<?php echo $phone['quantity'] < 3 ? 'danger' : 'warning'; ?>">
                                            <?php echo $phone['quantity']; ?> units
                                        </span>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No low stock items.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>
</html>