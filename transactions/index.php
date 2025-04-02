<?php
require_once "../config/database.php";

// Get all transactions with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE p.brand LIKE '%$search%' OR p.model LIKE '%$search%' OR t.notes LIKE '%$search%'" : "";

$total_records = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM inventory_transactions t 
    JOIN phones p ON t.phone_id = p.id 
    $where_clause
")->fetch_assoc()['total'];

$total_pages = ceil($total_records / $per_page);

$transactions = mysqli_query($conn, "
    SELECT 
        t.id,
        t.transaction_date,
        t.transaction_type as type,
        t.quantity,
        t.notes,
        p.brand,
        p.model,
        p.sku 
    FROM inventory_transactions t 
    JOIN phones p ON t.phone_id = p.id 
    $where_clause 
    ORDER BY t.transaction_date DESC 
    LIMIT $offset, $per_page
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions - Inventory System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title">
                <i class="fas fa-exchange-alt"></i>
                Transactions
            </h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Record Transaction</span>
            </a>
        </div>

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

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Quantity</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                                <tr>
                                    <td>#<?php echo $transaction['id']; ?></td>
                                    <td><?php echo date('F j, Y g:i A', strtotime($transaction['transaction_date'])); ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($transaction['brand']); ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($transaction['model']); ?></small>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $transaction['type'] === 'in' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo ucfirst($transaction['type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php echo $transaction['quantity']; ?></strong>
                                        <span class="text-muted">units</span>
                                    </td>
                                    <td><?php echo htmlspecialchars($transaction['notes']); ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="view.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            
                            <?php if (mysqli_num_rows($transactions) == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x mb-3 d-block text-muted"></i>
                                        <p class="text-muted">No transactions found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html> 