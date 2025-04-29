<?php
require_once "../config/database.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $specifications = isset($_POST['specifications']) ? mysqli_real_escape_string($conn, $_POST['specifications']) : '';
    $category_id = !empty($_POST['category_id']) ? mysqli_real_escape_string($conn, $_POST['category_id']) : "NULL";
    
    // Check if SKU already exists
    $check_sku = mysqli_query($conn, "SELECT id FROM phones WHERE sku = '$sku'");
    if (mysqli_num_rows($check_sku) > 0) {
        $error = "SKU already exists!";
    } else {
        // Clean up the query - only one INSERT statement with category_id included
        $query = "INSERT INTO phones (brand, model, sku, price, quantity, description, specifications) 
                  VALUES ('$brand', '$model', '$sku', $price, $quantity, '$description', '$specifications')";
        
        if (mysqli_query($conn, $query)) {
            $message = "Phone added successfully!";
            // Get the new phone ID
            $phone_id = mysqli_insert_id($conn);
            
            // Save the category relationship in the junction table
            if (!empty($_POST['category_id'])) {
                $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
                mysqli_query($conn, "INSERT INTO phone_categories (phone_id, category_id) VALUES ($phone_id, $category_id)");
            }
            
            // Record the transaction
            mysqli_query($conn, "INSERT INTO inventory_transactions (phone_id, transaction_type, quantity, notes) 
                               VALUES ($phone_id, 'in', $quantity, 'Initial stock')");
            
            // Redirect to phones list after 2 seconds
            header("refresh:2;url=index.php");
        } else {
            $error = "Error adding phone: " . mysqli_error($conn);
        }
    }
}

// Get all categories for the form
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Phone - Inventory System</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h1 class="section-title">
                                <i class="fas fa-plus"></i>
                                Add New Phone
                            </h1>
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

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" required>
                            </div>

                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control" id="model" name="model" required>
                            </div>

                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="sku" name="sku" required>
                            </div>

                            <!-- In your form, make sure you have a select field for categories -->
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">-- Select Category --</option>
                                    <?php
                                    $categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
                                    while ($category = mysqli_fetch_assoc($categories)) {
                                        echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <span>Save Phone</span>
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                    <span>Cancel</span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/theme.js"></script>
</body>
</html>