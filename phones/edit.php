<?php
require_once "../config/database.php";

$message = '';
$error = '';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $model = mysqli_real_escape_string($conn, $_POST['model']);
    $sku = mysqli_real_escape_string($conn, $_POST['sku']);
    $price = (float)$_POST['price'];
    $quantity = (int)$_POST['quantity'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $specifications = isset($_POST['specifications']) ? mysqli_real_escape_string($conn, $_POST['specifications']) : '';
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    // Check if SKU exists for other phones
    $check_sku = mysqli_query($conn, "SELECT id FROM phones WHERE sku = '$sku' AND id != $id");
    if (mysqli_num_rows($check_sku) > 0) {
        $error = "SKU already exists!";
    } else {
        // Start transaction
        mysqli_begin_transaction($conn);
        try {
            // Get current quantity to check if it changed
            $current_quantity = mysqli_query($conn, "SELECT quantity FROM phones WHERE id = $id")->fetch_assoc()['quantity'];
            
            // Update phone
            $query = "UPDATE phones SET 
                        brand = '$brand',
                        model = '$model',
                        sku = '$sku',
                        price = $price,
                        quantity = $quantity,
                        description = '$description',
                        specifications = '$specifications'
                     WHERE id = $id";
            
            if (mysqli_query($conn, $query)) {
                // Update category
                mysqli_query($conn, "DELETE FROM phone_categories WHERE phone_id = $id");
                if ($category_id) {
                    mysqli_query($conn, "INSERT INTO phone_categories (phone_id, category_id) VALUES ($id, $category_id)");
                }

                // Record transaction if quantity changed
                if ($quantity != $current_quantity) {
                    $quantity_diff = $quantity - $current_quantity;
                    $transaction_type = $quantity_diff > 0 ? 'in' : 'out';
                    $transaction_quantity = abs($quantity_diff);
                    
                    mysqli_query($conn, "INSERT INTO inventory_transactions (phone_id, transaction_type, quantity, notes) 
                                       VALUES ($id, '$transaction_type', $transaction_quantity, 'Quantity adjusted through edit')");
                }

                mysqli_commit($conn);
                $message = "Phone updated successfully!";
                // Redirect to phones list after 2 seconds
                header("refresh:2;url=index.php");
            } else {
                throw new Exception(mysqli_error($conn));
            }
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Error updating phone: " . $e->getMessage();
        }
    }
}

// Get phone data
$result = mysqli_query($conn, "SELECT * FROM phones WHERE id = $id");
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}
$phone = mysqli_fetch_assoc($result);

// Get all categories
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// Get phone's categories
$phone_categories = mysqli_query($conn, "SELECT category_id FROM phone_categories WHERE phone_id = $id");
$selected_categories = array();
while ($cat = mysqli_fetch_assoc($phone_categories)) {
    $selected_categories[] = $cat['category_id'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Phone - Inventory System</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="section-title">
                            <i class="fas fa-edit"></i>
                            Edit Phone
                        </h1>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($message)): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo htmlspecialchars($phone['brand']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="model" class="form-label">Model</label>
                                <input type="text" class="form-control" id="model" name="model" value="<?php echo htmlspecialchars($phone['model']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($phone['sku']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Select a category</option>
                                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $phone['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $phone['price']; ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $phone['quantity']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($phone['description']); ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    <span>Save Changes</span>
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