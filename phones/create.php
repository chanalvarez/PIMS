<?php
require_once "../config/database.php";

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $specifications = mysqli_real_escape_string($conn, $_POST['specifications']);

    // Check if SKU already exists
    $check_sku = mysqli_query($conn, "SELECT id FROM phones WHERE sku = '$sku'");
    if (mysqli_num_rows($check_sku) > 0) {
        $error = "SKU already exists!";
    } else {
        // Insert new phone
        $query = "INSERT INTO phones (brand, model, sku, price, quantity, description, specifications) 
                  VALUES ('$brand', '$model', '$sku', $price, $quantity, '$description', '$specifications')";
        
        if (mysqli_query($conn, $query)) {
            $message = "Phone added successfully!";
            // Record the transaction
            $phone_id = mysqli_insert_id($conn);
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
    <title>Add New Phone - Inventory System</title>
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

        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        .alert {
            border: none;
            border-radius: 1rem;
            padding: 1rem 1.5rem;
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="section-title mb-0">Add New Phone</h1>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="brand" class="form-label">Brand</label>
                                    <input type="text" class="form-control" id="brand" name="brand" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="model" name="model" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="sku" name="sku" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="price" class="form-label">Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" class="form-control" id="quantity" name="quantity" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="categories" class="form-label">Categories</label>
                                    <select class="form-select" id="categories" name="categories[]" multiple>
                                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                            <option value="<?php echo $category['id']; ?>">
                                                <?php echo htmlspecialchars($category['name']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="col-12">
                                    <label for="specifications" class="form-label">Specifications</label>
                                    <textarea class="form-control" id="specifications" name="specifications" rows="3" placeholder="Enter phone specifications (e.g., Screen size, RAM, Storage, etc.)"></textarea>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Phone
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 