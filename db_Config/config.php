<?php 
// Database configuration with environment variable support for DigitalOcean App Platform

// Debug: Show what environment variables are available (remove in production)
error_log("Available environment variables: " . print_r($_ENV, true));

// Check if we're in production (DigitalOcean App Platform sets DATABASE_URL)
if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
    // Parse DigitalOcean database URL
    $url = parse_url($_ENV['DATABASE_URL']);
    $Server = $url['host'];
    $Username = $url['user'];
    $Password = $url['pass'] ?? '';
    $Database = ltrim($url['path'], '/');
    $Port = $url['port'] ?? 25060; // DigitalOcean MySQL default port
    
    error_log("Using DATABASE_URL: Host=$Server, User=$Username, DB=$Database, Port=$Port");
} else {
    // Use individual environment variables with better fallback handling
    $Server = $_ENV['DB_HOST'] ?? getenv('DB_HOST');
    $Username = $_ENV['DB_USER'] ?? getenv('DB_USER');
    $Password = $_ENV['DB_PASS'] ?? getenv('DB_PASS');
    $Database = $_ENV['DB_NAME'] ?? getenv('DB_NAME');
    $Port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? 25060;
    
    // If no environment variables are set, we're likely in development
    if (!$Server) {
        error_log("No database environment variables found. Using development defaults.");
        $Server = 'localhost';
        $Username = 'root';
        $Password = '';
        $Database = 'PharmacyX_DB';
        $Port = 3306;
    }
    
    error_log("Using individual env vars: Host=$Server, User=$Username, DB=$Database, Port=$Port");
}

// Create connection with port support
$Connection = mysqli_connect($Server, $Username, $Password, $Database, $Port);

// Check connection
if (!$Connection) {
    $error_msg = mysqli_connect_error();
    error_log("Database connection failed: $error_msg");
    error_log("Connection details: Host=$Server, User=$Username, DB=$Database, Port=$Port");
    
    // In production, don't expose database details
    if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
        die("Database connection failed. Please check your database configuration. Error: Connection refused.");
    } else {
        die("Connection Failed: " . $error_msg . " (Host: $Server, Port: $Port)");
    }
}

// Set charset to UTF8
mysqli_set_charset($Connection, "utf8");

error_log("Database connection successful!");

?>