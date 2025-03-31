<?php
require_once "../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// Get category details
$category = mysqli_query($conn, "SELECT * FROM categories WHERE id = $id")->fetch_assoc();

if (!$category) {
    header("Location: index.php");
    exit();
}

// Get phones in this category using the junction table
$phones = mysqli_query($conn, "
    SELECT p.* 
    FROM phones p 
    INNER JOIN phone_categories pc ON p.id = pc.phone_id 
    WHERE pc.category_id = $id
");

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
                        <a class="nav-link active" href="index.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../transactions/index.php">Transactions</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>View Category</h1>
            <div>
                <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Category
                </a>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Categories
                </a>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Category Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($category['name']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($category['description']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Phones in this Category</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($phone = mysqli_fetch_assoc($phones)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($phone['brand']); ?></td>
                                    <td><?php echo htmlspecialchars($phone['model']); ?></td>
                                    <td><?php echo htmlspecialchars($phone['sku']); ?></td>
                                    <td>$<?php echo number_format($phone['price'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $phone['stock'] > 0 ? 'success' : 'danger'; ?>">
                                            <?php echo $phone['stock']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="../phones/view.php?id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($phones) == 0): ?>
                                <tr>
                                    <td colspan="6" class="text-center">No phones found in this category.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 