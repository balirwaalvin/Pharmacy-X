<?php
// Database connection test for DigitalOcean deployment
// WARNING: Remove this file after testing for security reasons

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PharmacyX Database Connection Test</h1>";
echo "<p><strong>Environment:</strong> " . (isset($_ENV['DATABASE_URL']) ? 'DigitalOcean Production' : 'Local Development') . "</p>";

// Include database configuration
require_once 'db_Config/config.php';

// Use the connection variable from config.php
$conn = $Connection;

try {
    // Test database connection
    echo "<h2>Connection Test</h2>";
    
    if (!$conn || mysqli_connect_error()) {
        throw new Exception("Connection failed: " . mysqli_connect_error());
    }
    
    echo "<p style='color: green;'>✓ Database connected successfully!</p>";
    echo "<p><strong>Database:</strong> " . $Database . "</p>";
    echo "<p><strong>Host:</strong> " . $Server . "</p>";
    
    // Test table existence and count records
    echo "<h2>Table Verification</h2>";
    
    $tables = ['User_info', 'Products', 'Orders', 'Messages', 'Payment'];
    
    foreach ($tables as $table) {
        $sql = "SELECT COUNT(*) as count FROM `$table`";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            echo "<p>✓ Table <strong>$table</strong>: {$row['count']} records</p>";
        } else {
            echo "<p style='color: red;'>✗ Table <strong>$table</strong>: Error - " . mysqli_error($conn) . "</p>";
        }
    }
    
    // Test sample queries
    echo "<h2>Sample Data Test</h2>";
    
    // Check if admin user exists
    $sql = "SELECT user_name, name, user_type FROM User_info WHERE user_type = 'admin' LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        echo "<p>✓ Admin user found: <strong>{$admin['name']}</strong> ({$admin['user_name']})</p>";
    } else {
        echo "<p style='color: orange;'>⚠ No admin user found</p>";
    }
    
    // Check products
    $sql = "SELECT COUNT(*) as count FROM Products WHERE quantity_in_stock > 0";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>✓ Products in stock: <strong>{$row['count']}</strong></p>";
    }
    
    // Test foreign key relationships
    echo "<h2>Relationship Test</h2>";
    
    $sql = "SELECT o.order_id, u.name, p.product_name 
            FROM Orders o 
            JOIN User_info u ON o.user_name = u.user_name 
            JOIN Products p ON o.product_id = p.product_id 
            LIMIT 3";
    
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        echo "<p>✓ Foreign key relationships working:</p>";
        echo "<ul>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>Order #{$row['order_id']}: {$row['name']} ordered {$row['product_name']}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠ No order relationships found</p>";
    }
    
    echo "<h2>Database Schema</h2>";
    
    // Show table structures
    foreach ($tables as $table) {
        echo "<h3>$table Structure</h3>";
        $sql = "DESCRIBE `$table`";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            echo "<table border='1' style='margin-bottom: 20px; border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    }
    
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<h2 style='color: #155724; margin-top: 0;'>✅ Database Setup Complete!</h2>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Database is working correctly</li>";
    echo "<li>All tables are created with proper relationships</li>";
    echo "<li>Sample data is available for testing</li>";
    echo "<li><strong>IMPORTANT:</strong> Delete this db_test.php file for security</li>";
    echo "<li>Test your application features (registration, login, ordering)</li>";
    echo "</ol>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<h2 style='color: #721c24; margin-top: 0;'>❌ Database Error</h2>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Check if the database import was successful</li>";
    echo "<li>Verify DATABASE_URL environment variable is set correctly</li>";
    echo "<li>Check DigitalOcean database connection settings</li>";
    echo "<li>Review database logs in DigitalOcean console</li>";
    echo "</ul>";
    echo "</div>";
    
    // Show environment variables for debugging (safely)
    echo "<h3>Environment Debug Info</h3>";
    echo "<p><strong>DATABASE_URL set:</strong> " . (isset($_ENV['DATABASE_URL']) ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>DB_HOST set:</strong> " . (isset($_ENV['DB_HOST']) ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f8f9fa;
}

h1, h2, h3 {
    color: #333;
}

table {
    width: 100%;
    background: white;
    font-size: 12px;
}

th, td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    padding: 10px;
    border-radius: 5px;
    margin: 10px 0;
    color: #856404;
}
</style>

<div class="warning">
    <strong>Security Warning:</strong> This file should be deleted after testing. It exposes database information and should not be accessible in production.
</div>
