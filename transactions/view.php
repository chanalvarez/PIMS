<?php
require_once "../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get transaction data with phone details
$query = "SELECT t.*, p.brand, p.model, p.sku, p.price 
          FROM inventory_transactions t 
          JOIN phones p ON t.phone_id = p.id 
          WHERE t.id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$transaction = mysqli_fetch_assoc($result);

// Get previous and next transactions
$prev_query = "SELECT id FROM inventory_transactions WHERE id < $id ORDER BY id DESC LIMIT 1";
$next_query = "SELECT id FROM inventory_transactions WHERE id > $id ORDER BY id ASC LIMIT 1";
$prev_id = mysqli_query($conn, $prev_query)->fetch_assoc()['id'] ?? null;
$next_id = mysqli_query($conn, $next_query)->fetch_assoc()['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Transaction - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/theme.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-mobile-alt"></i>
                <span>Phone Inventory Management System</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../phones/index.php">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Phones</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/index.php">
                            <i class="fas fa-tags"></i>
                            <span>Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
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
        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h1 class="section-title">
                            <i class="fas fa-exchange-alt"></i>
                            Transaction Details
                        </h1>

                        <div class="mb-3">
                            <label class="form-label">Transaction ID</label>
                            <p class="form-control-static">#<?php echo $transaction['id']; ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <p class="form-control-static"><?php echo date('F j, Y g:i A', strtotime($transaction['transaction_date'])); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <p class="form-control-static">
                                <span class="badge <?php echo $transaction['transaction_type'] === 'in' ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $transaction['transaction_type'] === 'in' ? 'In Stock' : 'Stock Out'; ?>
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <p class="form-control-static"><?php echo $transaction['quantity']; ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <p class="form-control-static"><?php echo nl2br(htmlspecialchars($transaction['notes'])); ?></p>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i>
                                <span>Back to List</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h2 class="section-title">
                            <i class="fas fa-mobile-alt"></i>
                            Phone Information
                        </h2>

                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($transaction['brand']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($transaction['model']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($transaction['sku']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <p class="form-control-static">₱<?php echo number_format($transaction['price'], 2); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Value</label>
                            <p class="form-control-static">₱<?php echo number_format($transaction['price'] * $transaction['quantity'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>