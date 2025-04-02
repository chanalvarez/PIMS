<?php
require_once "../config/database.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_id = mysqli_real_escape_string($conn, $_POST['phone_id']);
    $transaction_type = mysqli_real_escape_string($conn, $_POST['transaction_type']);
    $quantity = (int)$_POST['quantity'];
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    // Get current quantity
    $current_quantity = mysqli_query($conn, "SELECT quantity FROM phones WHERE id = $phone_id")->fetch_assoc()['quantity'];
    
    // Calculate new quantity
    $new_quantity = $transaction_type == 'in' ? $current_quantity + $quantity : $current_quantity - $quantity;
    
    // Validate new quantity
    if ($new_quantity < 0) {
        $error = "Error: Not enough stock available for this transaction.";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Update phone quantity
            mysqli_query($conn, "UPDATE phones SET quantity = $new_quantity WHERE id = $phone_id");
            
            // Record transaction
            mysqli_query($conn, "INSERT INTO inventory_transactions (phone_id, transaction_type, quantity, notes) 
                               VALUES ($phone_id, '$transaction_type', $quantity, '$notes')");
            
            mysqli_commit($conn);
            $message = "Transaction recorded successfully!";
            // Redirect to transactions list after 2 seconds
            header("refresh:2;url=index.php");
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error recording transaction: " . $e->getMessage();
        }
    }
}

// Get all phones for the form
$phones = mysqli_query($conn, "SELECT id, brand, model, sku, quantity FROM phones ORDER BY brand, model");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Transaction - Inventory System</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="section-title">
                            <i class="fas fa-plus"></i>
                            Record New Transaction
                        </h1>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($message)): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="phone_id" class="form-label">Phone</label>
                                <select class="form-select" id="phone_id" name="phone_id" required>
                                    <option value="">Select a phone</option>
                                    <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                        <option value="<?php echo $phone['id']; ?>">
                                            <?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model'] . ' (' . $phone['sku'] . ')'); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Transaction Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="in">Stock In</option>
                                    <option value="out">Stock Out</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <span>Record Transaction</span>
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html> 