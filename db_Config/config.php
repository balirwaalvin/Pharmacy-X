<?php 
// Database configuration with environment variable support

// Check if we're in production (DigitalOcean App Platform sets DATABASE_URL)
if (isset($_ENV['DATABASE_URL'])) {
    // Parse DigitalOcean database URL
    $url = parse_url($_ENV['DATABASE_URL']);
    $Server = $url['host'];
    $Username = $url['user'];
    $Password = $url['pass'];
    $Database = ltrim($url['path'], '/');
    $Port = $url['port'] ?? 3306;
} else {
    // Use individual environment variables or fallback to defaults
    $Server = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost';
    $Username = $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'root';
    $Password = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: '';
    $Database = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'PharmacyX_DB';
    $Port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: 3306;
}

// Create connection with port support
$Connection = mysqli_connect($Server, $Username, $Password, $Database, $Port);

// Check connection
if (!$Connection) {
    // In production, don't expose database errors
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
        die("Database connection failed. Please try again later.");
    } else {
        die("Connection Failed: " . mysqli_connect_error());
    }
}

// Set charset to UTF8
mysqli_set_charset($Connection, "utf8");

?>