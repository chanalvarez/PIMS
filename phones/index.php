<?php
require_once "../config/database.php";

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM phones WHERE id = $id");
    header("Location: index.php");
    exit();
}

// Get all phones with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$where_clause = $search ? "WHERE brand LIKE '%$search%' OR model LIKE '%$search%' OR sku LIKE '%$search%'" : "";

$total_records = mysqli_query($conn, "SELECT COUNT(*) as total FROM phones $where_clause")->fetch_assoc()['total'];
$total_pages = ceil($total_records / $per_page);

$phones = mysqli_query($conn, "SELECT * FROM phones $where_clause ORDER BY brand, model LIMIT $offset, $per_page");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Phones - Inventory System</title>
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
            <h1 class="section-title mb-0">Manage Phones</h1>
            <div class="d-flex gap-2">
                <a href="create.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add New Phone
                </a>
                <a href="../transactions/create.php" class="btn btn-success">
                    <i class="fas fa-exchange-alt me-2"></i>Record Transaction
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-mobile-alt text-primary fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Add New Phone</h5>
                                <p class="text-muted mb-0">Add a new phone to your inventory</p>
                            </div>
                        </div>
                        <a href="create.php" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus me-2"></i>Add Phone
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-exchange-alt text-success fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Record Transaction</h5>
                                <p class="text-muted mb-0">Record stock in/out transactions</p>
                            </div>
                        </div>
                        <a href="../transactions/create.php" class="btn btn-outline-success w-100">
                            <i class="fas fa-exchange-alt me-2"></i>Record Transaction
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="fas fa-tags text-info fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Manage Categories</h5>
                                <p class="text-muted mb-0">Organize phones by categories</p>
                            </div>
                        </div>
                        <a href="../categories/index.php" class="btn btn-outline-info w-100">
                            <i class="fas fa-tags me-2"></i>Manage Categories
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search by brand, model or SKU..." value="<?php echo htmlspecialchars($search); ?>">
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
                                <th>Brand</th>
                                <th>Model</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                <tr>
                                    <td class="align-middle"><?php echo htmlspecialchars($phone['brand']); ?></td>
                                    <td class="align-middle"><?php echo htmlspecialchars($phone['model']); ?></td>
                                    <td class="align-middle"><?php echo htmlspecialchars($phone['sku']); ?></td>
                                    <td class="align-middle">$<?php echo number_format($phone['price'], 2); ?></td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $phone['quantity'] < 5 ? 'danger' : 'success'; ?>">
                                            <?php echo $phone['quantity']; ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="edit.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="deletePhone(<?php echo $phone['id']; ?>)" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($phones) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-secondary">No phones found.</td>
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
    <script>
        function deletePhone(id) {
            if (confirm('Are you sure you want to delete this phone?')) {
                window.location.href = 'index.php?delete=' + id;
            }
        }
    </script>
</body>
</html> 