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
    <title>Categories - Inventory System</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="section-title">
                <i class="fas fa-tags"></i>
                Manage Categories
            </h1>
            <a href="create.php" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Add New Category</span>
            </a>
        </div>

        <?php if (isset($error) && !empty($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($message) && !empty($message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" style="border-radius: 8px; overflow: hidden;">
                        <thead>
                            <tr>
                                <th style="color: #000000;">Name</th>
                                <th style="color: #000000;">Description</th>
                                <th style="color: #000000;">Phones Count</th>
                                <th style="color: #000000;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                <tr>
                                    <td style="color: #000000;">
                                        <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                    </td>
                                    <td style="color: #000000;"><?php echo htmlspecialchars($category['description']); ?></td>
                                    <td style="color: #000000;"><?php echo $category['phone_count']; ?> phones</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="view.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="?delete=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.75rem;" onclick="return confirm('Are you sure you want to delete this category?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>