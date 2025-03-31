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
    SELECT t.*, p.brand, p.model, p.sku 
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
    <title>Manage Transactions - Inventory System</title>
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

        .table {
            color: var(--text-primary);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .search-form {
            background: var(--card-background);
            padding: 1.5rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .transaction-date {
            color: var(--text-secondary);
            font-size: 0.875rem;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title mb-0">Manage Transactions</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Record New Transaction
            </a>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search by phone brand, model or notes..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                        <?php if ($search): ?>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Clear
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
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
                                    <td class="align-middle">
                                        <div class="transaction-date">
                                            <?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?>
                                            <br>
                                            <?php echo date('h:i A', strtotime($transaction['transaction_date'])); ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <strong><?php echo htmlspecialchars($transaction['brand']); ?></strong>
                                        <br>
                                        <span class="text-secondary"><?php echo htmlspecialchars($transaction['model']); ?></span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $transaction['type'] == 'in' ? 'success' : 'danger'; ?>">
                                            <i class="fas fa-<?php echo $transaction['type'] == 'in' ? 'arrow-down' : 'arrow-up'; ?> me-1"></i>
                                            <?php echo ucfirst($transaction['type']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $transaction['type'] == 'in' ? 'success' : 'danger'; ?>">
                                            <?php echo $transaction['quantity']; ?> units
                                        </span>
                                    </td>
                                    <td class="align-middle text-secondary">
                                        <?php echo htmlspecialchars($transaction['notes']); ?>
                                    </td>
                                    <td class="align-middle">
                                        <a href="view.php?id=<?php echo $transaction['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($transactions) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">No transactions found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
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
</body>
</html> 