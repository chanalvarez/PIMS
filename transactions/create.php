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
    <title>Record New Transaction - Inventory System</title>
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

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .alert {
            border: none;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
        }

        .form-select {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-mobile-alt me-2"></i>Phone Inventory Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../phones/index.php">
                            <i class="fas fa-mobile-alt me-2"></i>Phones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/index.php">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-exchange-alt me-2"></i>Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="section-title mb-0">Record New Transaction</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="phone_id" class="form-label">Phone</label>
                                    <select class="form-select" id="phone_id" name="phone_id" required>
                                        <option value="">Select a phone...</option>
                                        <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                            <option value="<?php echo $phone['id']; ?>">
                                                <?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?>
                                                (SKU: <?php echo htmlspecialchars($phone['sku']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Transaction Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="in">Stock In</option>
                                        <option value="out">Stock Out</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="transaction_date" class="form-label">Transaction Date</label>
                                    <input type="datetime-local" class="form-control" id="transaction_date" name="transaction_date" required>
                                </div>
                                <div class="col-12">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Record Transaction
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Set default datetime to current time
        document.getElementById('transaction_date').value = new Date().toISOString().slice(0, 16);
    </script>
</body>
</html> 