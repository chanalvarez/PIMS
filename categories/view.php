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

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }

        .info-value {
            color: var(--text-primary);
            margin-bottom: 1rem;
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
                        <a class="nav-link active" href="index.php">
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
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2 class="section-title mb-0">Category Details</h2>
                            <div>
                                <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-2"></i>Edit Category
                                </a>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Back to List
                                </a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="info-label">Category Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($category['name']); ?></div>
                        </div>

                        <div class="mb-4">
                            <div class="info-label">Description</div>
                            <div class="info-value"><?php echo nl2br(htmlspecialchars($category['description'])); ?></div>
                        </div>

                        <div class="mb-4">
                            <div class="info-label">Created At</div>
                            <div class="info-value"><?php echo date('F j, Y g:i A', strtotime($category['created_at'])); ?></div>
                        </div>

                        <h3 class="section-title">Phones in this Category</h3>
                        <?php if (mysqli_num_rows($phones) > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Brand</th>
                                            <th>Model</th>
                                            <th>SKU</th>
                                            <th>Price</th>
                                            <th>Quantity</th>
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
                                                    <span class="badge bg-<?php echo $phone['quantity'] > 5 ? 'success' : 'warning'; ?>">
                                                        <?php echo $phone['quantity']; ?> units
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>No phones found in this category.
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
</body>
</html> 