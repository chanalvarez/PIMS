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
    <title>Phones - Inventory System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title">
                <i class="fas fa-mobile-alt"></i>
                Manage Phones
            </h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Add New Phone</span>
            </a>
        </div>

        <div class="card search-form">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Search by brand, model or SKU..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                            <span>Search</span>
                        </button>
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
                                <th>Stock</th>
                                <th>Categories</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($phone['brand']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($phone['model']); ?></td>
                                    <td><?php echo htmlspecialchars($phone['sku']); ?></td>
                                    <td>₱<?php echo number_format($phone['price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $phone['quantity'] < 5 ? 'danger' : 'success'; ?>">
                                            <?php echo $phone['quantity']; ?> units
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $categories_query = "SELECT c.name 
                                                          FROM categories c 
                                                          JOIN phone_categories pc ON c.id = pc.category_id 
                                                          WHERE pc.phone_id = {$phone['id']}";
                                        $categories = mysqli_query($conn, $categories_query);
                                        if (mysqli_num_rows($categories) > 0) {
                                            while ($category = mysqli_fetch_assoc($categories)) {
                                                echo '<span class="badge bg-info me-1">' . htmlspecialchars($category['name']) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="text-muted">No categories</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <a href="view.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $phone['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this phone?')">
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