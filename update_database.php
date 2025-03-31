<?php
require_once "config/database.php";

// Create phone_categories table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS phone_categories (
    phone_id INT NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (phone_id, category_id),
    FOREIGN KEY (phone_id) REFERENCES phones(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $query)) {
    echo "Created phone_categories table successfully.<br>";
} else {
    echo "Error creating phone_categories table: " . mysqli_error($conn) . "<br>";
}

// Migrate existing category relationships
$phones = mysqli_query($conn, "SELECT id, category_id FROM phones WHERE category_id IS NOT NULL");
while ($phone = mysqli_fetch_assoc($phones)) {
    $phone_id = $phone['id'];
    $category_id = $phone['category_id'];
    mysqli_query($conn, "INSERT IGNORE INTO phone_categories (phone_id, category_id) VALUES ($phone_id, $category_id)");
}

// Add SKU column to phones table if it doesn't exist
$result = mysqli_query($conn, "SHOW COLUMNS FROM phones LIKE 'sku'");
if (mysqli_num_rows($result) == 0) {
    $query = "ALTER TABLE phones ADD COLUMN sku VARCHAR(50) AFTER model";
    if (mysqli_query($conn, $query)) {
        echo "Added SKU column successfully.<br>";
    } else {
        echo "Error adding SKU column: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "SKU column already exists in phones table.<br>";
}

// Rename created_at to transaction_date in inventory_transactions table
$result = mysqli_query($conn, "SHOW COLUMNS FROM inventory_transactions LIKE 'transaction_date'");
if (mysqli_num_rows($result) == 0) {
    $query = "ALTER TABLE inventory_transactions CHANGE created_at transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if (mysqli_query($conn, $query)) {
        echo "Renamed created_at to transaction_date successfully.<br>";
    } else {
        echo "Error renaming created_at column: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "transaction_date column already exists in inventory_transactions table.<br>";
}

// Add some sample data if the phones table is empty
$result = mysqli_query($conn, "SELECT COUNT(*) as count FROM phones");
$count = $result->fetch_assoc()['count'];

if ($count == 0) {
    // Add sample categories
    $categories = [
        ['name' => 'Smartphones', 'description' => 'Mobile phones with advanced features'],
        ['name' => 'Feature Phones', 'description' => 'Basic mobile phones'],
        ['name' => 'Accessories', 'description' => 'Phone accessories and parts']
    ];
    
    foreach ($categories as $category) {
        mysqli_query($conn, "INSERT INTO categories (name, description) VALUES ('{$category['name']}', '{$category['description']}')");
    }
    
    // Add sample phones
    $phones = [
        ['brand' => 'Apple', 'model' => 'iPhone 13', 'sku' => 'IP13-128', 'price' => 999.99, 'quantity' => 10, 'category_id' => 1],
        ['brand' => 'Samsung', 'model' => 'Galaxy S21', 'sku' => 'GS21-256', 'price' => 899.99, 'quantity' => 15, 'category_id' => 1],
        ['brand' => 'Nokia', 'model' => '3310', 'sku' => 'NK3310', 'price' => 49.99, 'quantity' => 20, 'category_id' => 2]
    ];
    
    foreach ($phones as $phone) {
        mysqli_query($conn, "INSERT INTO phones (brand, model, sku, price, quantity, category_id) 
                            VALUES ('{$phone['brand']}', '{$phone['model']}', '{$phone['sku']}', 
                            {$phone['price']}, {$phone['quantity']}, {$phone['category_id']})");
    }
    
    echo "Added sample data successfully.<br>";
} else {
    echo "Phones table already contains data.<br>";
}

mysqli_close($conn);
?> 