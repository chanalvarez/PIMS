<?php
require_once "config/database.php";

echo "<h2>Database Setup Process</h2>";

// Create categories table
echo "<h3>Creating categories table...</h3>";
$query = "CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $query)) {
    echo "Categories table created successfully.<br>";
} else {
    echo "Error creating categories table: " . mysqli_error($conn) . "<br>";
}

// Check if category_id column exists
$result = mysqli_query($conn, "SHOW COLUMNS FROM phones LIKE 'category_id'");
if (mysqli_num_rows($result) == 0) {
    // Add category_id to phones table
    echo "<h3>Adding category_id to phones table...</h3>";
    $query = "ALTER TABLE phones ADD COLUMN category_id INT";
    if (mysqli_query($conn, $query)) {
        echo "Added category_id column successfully.<br>";
    } else {
        echo "Error adding category_id column: " . mysqli_error($conn) . "<br>";
    }

    // Add foreign key constraint
    echo "<h3>Adding foreign key constraint...</h3>";
    $query = "ALTER TABLE phones ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL";
    if (mysqli_query($conn, $query)) {
        echo "Added foreign key constraint successfully.<br>";
    } else {
        echo "Error adding foreign key constraint: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "category_id column already exists in phones table.<br>";
}

// Create transactions table
echo "<h3>Creating transactions table...</h3>";
$query = "CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone_id INT NOT NULL,
    transaction_type ENUM('in', 'out') NOT NULL,
    quantity INT NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (phone_id) REFERENCES phones(id) ON DELETE CASCADE
)";
if (mysqli_query($conn, $query)) {
    echo "Transactions table created successfully.<br>";
} else {
    echo "Error creating transactions table: " . mysqli_error($conn) . "<br>";
}

// Verify tables
echo "<h3>Verifying table structure...</h3>";
$tables = ['categories', 'phones', 'inventory_transactions'];
foreach ($tables as $table) {
    $result = mysqli_query($conn, "SHOW TABLES LIKE '$table'");
    if (mysqli_num_rows($result) > 0) {
        echo "Table '$table' exists.<br>";
        
        // Show table structure
        $columns = mysqli_query($conn, "SHOW COLUMNS FROM $table");
        echo "Columns in $table:<br>";
        while ($column = mysqli_fetch_assoc($columns)) {
            echo "- {$column['Field']}: {$column['Type']}<br>";
        }
    } else {
        echo "Table '$table' does not exist!<br>";
    }
}

mysqli_close($conn);
?> 