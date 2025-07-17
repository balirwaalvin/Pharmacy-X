<?php
// Database connection test for DigitalOcean App Platform
echo "<h1>Pharmacy-X Database Connection Test</h1>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "DATABASE_URL: " . (isset($_ENV['DATABASE_URL']) ? "SET (***hidden***)" : "NOT SET") . "\n";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NOT SET') . "\n";
echo "DB_USER: " . ($_ENV['DB_USER'] ?? 'NOT SET') . "\n";
echo "DB_PASS: " . (isset($_ENV['DB_PASS']) ? "SET (***hidden***)" : "NOT SET") . "\n";
echo "DB_NAME: " . ($_ENV['DB_NAME'] ?? 'NOT SET') . "\n";
echo "APP_ENV: " . ($_ENV['APP_ENV'] ?? 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>Connection Test:</h2>";
try {
    require_once './db_Config/config.php';
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test a simple query
    $result = mysqli_query($Connection, "SHOW TABLES");
    if ($result) {
        $tables = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo "<h3>Database Tables:</h3>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . array_values($table)[0] . "</li>";
        }
        echo "</ul>";
        
        if (count($tables) == 0) {
            echo "<p style='color: orange;'>⚠️ No tables found. Please import PharmacyX_DB.sql</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Connected but cannot query tables. Check permissions.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If connection failed, check that database is attached to your app</li>";
echo "<li>If no tables shown, import Database/PharmacyX_DB.sql to your database</li>";
echo "<li>Once working, delete this file for security</li>";
echo "</ol>";
?>
