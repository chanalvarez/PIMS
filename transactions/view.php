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
    <title>Transaction Details - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Phone Inventory Management System</a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../phones/index.php">Phones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/index.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Transactions</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="card-title mb-0">Transaction Details</h2>
                        <div>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="card-subtitle mb-2 text-muted">Transaction Information</h5>
                                <table class="table">
                                    <tr>
                                        <th>Transaction ID:</th>
                                        <td><?php echo $transaction['id']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date:</th>
                                        <td><?php echo date('Y-m-d H:i:s', strtotime($transaction['transaction_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Type:</th>
                                        <td>
                                            <span class="badge bg-<?php echo $transaction['transaction_type'] == 'in' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($transaction['transaction_type']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Quantity:</th>
                                        <td><?php echo $transaction['quantity']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Notes:</th>
                                        <td><?php echo htmlspecialchars($transaction['notes']); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h5 class="card-subtitle mb-2 text-muted">Phone Information</h5>
                                <table class="table">
                                    <tr>
                                        <th>Phone:</th>
                                        <td><?php echo htmlspecialchars($transaction['brand'] . ' ' . $transaction['model']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>SKU:</th>
                                        <td><?php echo htmlspecialchars($transaction['sku']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Price:</th>
                                        <td>$<?php echo number_format($transaction['price'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Transaction Value:</th>
                                        <td>$<?php echo number_format($transaction['price'] * $transaction['quantity'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <?php if ($prev_id): ?>
                                <a href="view.php?id=<?php echo $prev_id; ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-chevron-left"></i> Previous Transaction
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>

                            <?php if ($next_id): ?>
                                <a href="view.php?id=<?php echo $next_id; ?>" class="btn btn-outline-primary">
                                    Next Transaction <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <div></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 