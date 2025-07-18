<?php
// DigitalOcean Database Configuration
// This file handles both local development and DigitalOcean production environments

error_reporting(E_ALL);
ini_set('log_errors', 1);

// Default values (for local development)
$Server = "localhost";
$Username = "root";
$Password = "";
$Database = "PharmacyX_DB";
$Port = 3306;

// Check if we're running on DigitalOcean (DATABASE_URL environment variable exists)
if (isset($_ENV['DATABASE_URL']) && !empty($_ENV['DATABASE_URL'])) {
    // Parse DigitalOcean DATABASE_URL
    $databaseUrl = $_ENV['DATABASE_URL'];
    
    // Parse the URL - format: mysql://username:password@host:port/database?ssl-mode=REQUIRED
    $urlParts = parse_url($databaseUrl);
    
    if ($urlParts === false) {
        error_log("Failed to parse DATABASE_URL: " . $databaseUrl);
        die("Database configuration error: Invalid DATABASE_URL format");
    }
    
    // Extract connection parameters
    $Server = $urlParts['host'] ?? '';
    $Username = $urlParts['user'] ?? '';
    $Password = $urlParts['pass'] ?? '';
    $Port = $urlParts['port'] ?? 25060;
    
    // Extract database name from path (remove leading slash)
    if (isset($urlParts['path'])) {
        $Database = ltrim($urlParts['path'], '/');
    }
    
    // Parse query parameters for SSL settings
    $sslMode = '';
    if (isset($urlParts['query'])) {
        parse_str($urlParts['query'], $queryParams);
        if (isset($queryParams['ssl-mode'])) {
            $sslMode = $queryParams['ssl-mode'];
        }
    }
    
    error_log("DigitalOcean DB Config - Host: $Server, Port: $Port, User: $Username, DB: $Database, SSL: $sslMode");
    
} else {
    // Check for individual environment variables (fallback)
    $Server = $_ENV['DB_HOST'] ?? $Server;
    $Username = $_ENV['DB_USER'] ?? $Username;
    $Password = $_ENV['DB_PASS'] ?? $Password;
    $Database = $_ENV['DB_NAME'] ?? $Database;
    $Port = $_ENV['DB_PORT'] ?? $Port;
    
    error_log("Using individual DB environment variables or defaults");
}

// Validate required parameters
if (empty($Server) || empty($Username) || empty($Database)) {
    error_log("Missing required database parameters - Server: $Server, Username: $Username, Database: $Database");
    die("Database configuration error: Missing required connection parameters");
}

// Create connection with proper error handling
try {
    // For DigitalOcean, we need to handle SSL and port properly
    if (isset($_ENV['DATABASE_URL'])) {
        // DigitalOcean connection with SSL
        $Connection = mysqli_init();
        
        if (!$Connection) {
            throw new Exception("mysqli_init failed");
        }
        
        // Set SSL options for DigitalOcean
        mysqli_ssl_set($Connection, NULL, NULL, NULL, NULL, NULL);
        
        // Connect with SSL
        $connected = mysqli_real_connect(
            $Connection,
            $Server,
            $Username,
            $Password,
            $Database,
            $Port,
            NULL,
            MYSQLI_CLIENT_SSL
        );
        
        if (!$connected) {
            throw new Exception("DigitalOcean connection failed: " . mysqli_connect_error());
        }
        
    } else {
        // Local development connection
        $Connection = mysqli_connect($Server, $Username, $Password, $Database, $Port);
        
        if (!$Connection) {
            throw new Exception("Local connection failed: " . mysqli_connect_error());
        }
    }
    
    // Set charset
    mysqli_set_charset($Connection, "utf8mb4");
    
    // Test the connection
    $testQuery = "SELECT 1";
    $testResult = mysqli_query($Connection, $testQuery);
    
    if (!$testResult) {
        throw new Exception("Database connection test failed: " . mysqli_error($Connection));
    }
    
    error_log("Database connection successful!");
    
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
    
    // Show user-friendly error in development, generic in production
    if (isset($_ENV['DATABASE_URL'])) {
        // Production - generic error
        die("Database connection error. Please check your database configuration and try again.");
    } else {
        // Development - detailed error
        die("Database connection failed: " . $e->getMessage());
    }
}

// Legacy variable names for compatibility
$servername = $Server;
$username = $Username;
$password = $Password;
$database = $Database;
$conn = $Connection;

?>