<?php
require_once "../config/database.php";

// Handle delete action
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    
    // Check if category has phones
    $phones_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM phone_categories WHERE category_id = $id")->fetch_assoc()['count'];
    
    if ($phones_count > 0) {
        $error = "Cannot delete category: It contains phones. Please remove or reassign phones first.";
    } else {
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
        header("Location: index.php");
        exit();
    }
}

// Handle create/edit action
$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    if (isset($_POST['id'])) {
        // Update existing category
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        if (mysqli_query($conn, "UPDATE categories SET name = '$name', description = '$description' WHERE id = $id")) {
            $message = "Category updated successfully!";
        } else {
            $error = "Error updating category: " . mysqli_error($conn);
        }
    } else {
        // Create new category
        if (mysqli_query($conn, "INSERT INTO categories (name, description) VALUES ('$name', '$description')")) {
            $message = "Category created successfully!";
        } else {
            $error = "Error creating category: " . mysqli_error($conn);
        }
    }
}

// Get all categories with phone counts
$categories = mysqli_query($conn, "
    SELECT c.*, COUNT(pc.phone_id) as phone_count 
    FROM categories c 
    LEFT JOIN phone_categories pc ON c.id = pc.category_id 
    GROUP BY c.id 
    ORDER BY c.name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Inventory System</title>
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

        .alert {
            border: none;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title mb-0">Manage Categories</h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Category
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($message)): ?>
            <div class="alert alert-success mb-4">
                <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Phones</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                <tr>
                                    <td class="align-middle"><?php echo htmlspecialchars($category['name']); ?></td>
                                    <td class="align-middle"><?php echo htmlspecialchars($category['description']); ?></td>
                                    <td class="align-middle">
                                        <span class="badge bg-<?php echo $category['phone_count'] > 0 ? 'success' : 'secondary'; ?>">
                                            <?php echo $category['phone_count']; ?> phones
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary me-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="view.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="deleteCategory(<?php echo $category['id']; ?>)" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if (mysqli_num_rows($categories) == 0): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-secondary">No categories found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function deleteCategory(id) {
            if (confirm('Are you sure you want to delete this category?')) {
                window.location.href = 'index.php?delete=' + id;
            }
        }
    </script>
</body>
</html> 