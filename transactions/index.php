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
                <img src="../assets/img/pims.png" alt="Logo" width="40" height="40" class="me-2">
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
                    <table class="table" style="border-radius: 8px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th style="color: #000000;">ID</th>
                                <th style="color: #000000;">Date</th>
                                <th style="color: #000000;">Phone</th>
                                <th style="color: #000000;">Type</th>
                                <th style="color: #000000;">Quantity</th>
                                <th style="color: #000000;">Notes</th>
                                <th style="color: #000000;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                                <tr>
                                    <td style="color: #000000;"><?php echo $transaction['id']; ?></td>
                                    <td style="color: #000000;"><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                                    <td style="color: #000000;">
                                        <?php echo htmlspecialchars($transaction['brand'] . ' ' . $transaction['model']); ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $transaction['type'] == 'in' ? 'success' : 'danger'; ?>">
                                            <?php echo $transaction['type'] == 'in' ? 'In Stock' : 'Stock Out'; ?>
                                        </span>
                                    </td>
                                    <td style="color: #000000;"><?php echo $transaction['quantity']; ?> units</td>
                                    <td style="color: #000000;"><?php echo htmlspecialchars($transaction['notes']); ?></td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="view.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="?delete=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to delete this transaction?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
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