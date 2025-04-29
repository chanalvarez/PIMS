<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Phone Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <img src="../assets/img/pims.png" alt="Logo" width="40" height="40" class="me-2">Phone Inventory Management System
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'phones' ? 'active' : ''; ?>" href="../phones/index.php">
                            <i class="fas fa-phone me-1"></i>Phones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'categories' ? 'active' : ''; ?>" href="../categories/index.php">
                            <i class="fas fa-tags me-1"></i>Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page == 'transactions' ? 'active' : ''; ?>" href="../transactions/index.php">
                            <i class="fas fa-exchange-alt me-1"></i>Transactions
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="page-header d-flex justify-content-between align-items-center">
            <div>
                <h1><?php echo isset($page_title) ? $page_title : 'Dashboard'; ?></h1>
                <p class="text-muted mb-0"><?php echo isset($page_description) ? $page_description : 'Manage your inventory system'; ?></p>
            </div>
            <?php if (isset($action_button)): ?>
                <?php echo $action_button; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>