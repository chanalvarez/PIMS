<?php
require_once "../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get phone data with category name
$result = mysqli_query($conn, "SELECT p.*, GROUP_CONCAT(c.name) as category_names 
                             FROM phones p 
                             LEFT JOIN phone_categories pc ON p.id = pc.phone_id 
                             LEFT JOIN categories c ON pc.category_id = c.id 
                             WHERE p.id = $id 
                             GROUP BY p.id");
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$phone = mysqli_fetch_assoc($result);

// Get recent transactions
$transactions_query = "SELECT *, 
                      transaction_type as type,
                      transaction_date as created_at 
                      FROM inventory_transactions 
                      WHERE phone_id = $id 
                      ORDER BY transaction_date DESC 
                      LIMIT 5";
$transactions = mysqli_query($conn, $transactions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Phone - Inventory System</title>
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
                        <a class="nav-link active" href="index.php">
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
                        <a class="nav-link" href="../transactions/index.php">
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
                            <i class="fas fa-mobile-alt"></i>
                            Phone Details
                        </h1>

                        <div class="mb-3">
                            <label class="form-label">Brand</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($phone['brand']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Model</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($phone['model']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">SKU</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($phone['sku']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <p class="form-control-static">
                                <?php 
                                if (!empty($phone['category_names'])) {
                                    $categories = explode(',', $phone['category_names']);
                                    foreach ($categories as $category) {
                                        echo '<span class="badge bg-info me-1">' . htmlspecialchars($category) . '</span>';
                                    }
                                } else {
                                    echo '<span class="text-muted">No categories</span>';
                                }
                                ?>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <p class="form-control-static">$<?php echo number_format($phone['price'], 2); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <p class="form-control-static">
                                <span class="badge <?php echo $phone['quantity'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo $phone['quantity']; ?>
                                </span>
                            </p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p class="form-control-static"><?php echo nl2br(htmlspecialchars($phone['description'])); ?></p>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="edit.php?id=<?php echo $phone['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                                <span>Edit Phone</span>
                            </a>
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
                            <i class="fas fa-exchange-alt"></i>
                            Transaction History
                        </h2>

                        <?php if (empty($transactions)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No transactions found for this phone.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($transactions as $transaction): ?>
                                            <tr>
                                                <td><?php echo date('F j, Y g:i A', strtotime($transaction['created_at'])); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $transaction['type'] === 'in' ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo ucfirst($transaction['type']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $transaction['quantity']; ?></td>
                                                <td><?php echo htmlspecialchars($transaction['notes']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html> 