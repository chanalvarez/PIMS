<?php
require_once "../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get phone data
$result = mysqli_query($conn, "SELECT * FROM phones WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$phone = mysqli_fetch_assoc($result);

// Get phone categories
$categories_query = "SELECT c.name 
                    FROM categories c 
                    JOIN phone_categories pc ON c.id = pc.category_id 
                    WHERE pc.phone_id = $id";
$categories = mysqli_query($conn, $categories_query);

// Get recent transactions
$transactions_query = "SELECT * FROM inventory_transactions 
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
    <title><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?> - Inventory System</title>
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

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .info-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .table {
            color: var(--text-primary);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .transaction-badge {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .transaction-in {
            background-color: #dcfce7;
            color: #166534;
        }

        .transaction-out {
            background-color: #fee2e2;
            color: #991b1b;
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
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-mobile-alt me-2"></i>Phones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/index.php">
                            <i class="fas fa-tags me-2"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../transactions/index.php">
                            <i class="fas fa-exchange-alt me-2"></i>Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title mb-0"><?php echo htmlspecialchars($phone['brand'] . ' ' . $phone['model']); ?></h1>
            <div class="d-flex gap-2">
                <a href="edit.php?id=<?php echo $phone['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Phone
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Basic Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-label">SKU</div>
                                <div class="info-value"><?php echo htmlspecialchars($phone['sku']); ?></div>
                                
                                <div class="info-label">Price</div>
                                <div class="info-value">$<?php echo number_format($phone['price'], 2); ?></div>
                                
                                <div class="info-label">Current Stock</div>
                                <div class="info-value">
                                    <span class="badge bg-<?php echo $phone['quantity'] < 5 ? 'danger' : 'success'; ?>">
                                        <?php echo $phone['quantity']; ?> units
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-label">Total Value</div>
                                <div class="info-value">$<?php echo number_format($phone['price'] * $phone['quantity'], 2); ?></div>
                                
                                <div class="info-label">Categories</div>
                                <div class="info-value">
                                    <?php if (mysqli_num_rows($categories) > 0): ?>
                                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                            <span class="badge bg-info me-1">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </span>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <span class="text-muted">No categories assigned</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Description</h5>
                        <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($phone['description'])); ?></p>
                    </div>
                </div>

                <?php 
                $specs = json_decode($phone['specifications'], true);
                if ($specs): 
                ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Specifications</h5>
                        <div class="row">
                            <?php foreach ($specs as $key => $value): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="info-label"><?php echo htmlspecialchars(ucfirst($key)); ?></div>
                                    <div class="info-value"><?php echo htmlspecialchars($value); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-4">Recent Transactions</h5>
                        <?php if (mysqli_num_rows($transactions) > 0): ?>
                            <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="transaction-badge <?php echo $transaction['transaction_type'] == 'in' ? 'transaction-in' : 'transaction-out'; ?>">
                                        <i class="fas fa-<?php echo $transaction['transaction_type'] == 'in' ? 'arrow-down' : 'arrow-up'; ?>"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            <?php echo $transaction['transaction_type'] == 'in' ? 'Stock In' : 'Stock Out'; ?>
                                            <span class="badge bg-<?php echo $transaction['transaction_type'] == 'in' ? 'success' : 'danger'; ?> ms-2">
                                                <?php echo $transaction['quantity']; ?> units
                                            </span>
                                        </div>
                                        <div class="text-muted small">
                                            <?php echo date('M d, Y H:i', strtotime($transaction['transaction_date'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No recent transactions</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 