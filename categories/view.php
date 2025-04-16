<?php
require_once "../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get category data
$query = "SELECT * FROM categories WHERE id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$category = mysqli_fetch_assoc($result);

// Get phones in this category
$phones_query = "SELECT p.* FROM phones p 
                 INNER JOIN phone_categories pc ON p.id = pc.phone_id 
                 WHERE pc.category_id = $id";
$phones = mysqli_query($conn, $phones_query);

// Get previous and next categories
$prev_query = "SELECT id FROM categories WHERE id < $id ORDER BY id DESC LIMIT 1";
$next_query = "SELECT id FROM categories WHERE id > $id ORDER BY id ASC LIMIT 1";
$prev_id = mysqli_query($conn, $prev_query)->fetch_assoc()['id'] ?? null;
$next_id = mysqli_query($conn, $next_query)->fetch_assoc()['id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Category - Inventory System</title>
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
                        <a class="nav-link active" href="index.php">
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
                            <i class="fas fa-tag"></i>
                            Category Details
                        </h1>

                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($category['name']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <p class="form-control-static"><?php echo htmlspecialchars($category['description']); ?></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Created At</label>
                            <p class="form-control-static"><?php echo date('F j, Y g:i A', strtotime($category['created_at'])); ?></p>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-edit"></i>
                                <span>Edit Category</span>
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
                            <i class="fas fa-mobile-alt"></i>
                            Phones in Category
                        </h2>

                        <?php if (empty($phones)): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                No phones found in this category.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive mt-4">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th style="color: #000000;">Brand</th>
                                            <th style="color: #000000;">Model</th>
                                            <th style="color: #000000;">SKU</th>
                                            <th style="color: #000000;">Price</th>
                                            <th style="color: #000000;">Stock</th>
                                            <th style="color: #000000;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                            <tr>
                                                <td style="color: #000000;"><strong><?php echo htmlspecialchars($phone['brand']); ?></strong></td>
                                                <td style="color: #000000;"><?php echo htmlspecialchars($phone['model']); ?></td>
                                                <td style="color: #000000;"><?php echo htmlspecialchars($phone['sku']); ?></td>
                                                <td style="color: #000000;">₱<?php echo number_format($phone['price'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $phone['quantity'] < 5 ? 'danger' : 'success'; ?>">
                                                        <?php echo $phone['quantity']; ?> units
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="../phones/view.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <?php if ($prev_id): ?>
            <a href="view.php?id=<?php echo $prev_id; ?>" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> Previous Category
            </a>
        <?php else: ?>
            <div></div>
        <?php endif; ?>

        <?php if ($next_id): ?>
            <a href="view.php?id=<?php echo $next_id; ?>" class="btn btn-outline-primary">
                Next Category <i class="fas fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <div></div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>