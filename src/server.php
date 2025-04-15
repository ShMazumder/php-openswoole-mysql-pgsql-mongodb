<?php
echo "Starting OpenSwoole server...\n";

// Get database configuration from environment variables
$serverName = getenv('DB_HOST');
$databaseName = getenv('DB_DATABASE');
$dbUsername = getenv('DB_USERNAME');
$dbPassword = getenv('DB_PASSWORD');

// Define PDO options
$pdoOpts = [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
$serverUri = "mysql:host={$serverName};dbname={$databaseName};charset=utf8mb4"; // Default to MySQL

// Create connection to the database
try {
    $pdo = new PDO($serverUri, $dbUsername, $dbPassword, $pdoOpts);
    echo "Database connection successful!\n";
} catch (Exception $ex) {
    echo "Could not connect to the database: " . $ex->getMessage() . "\n";
    die();
}

// You can switch to PostgreSQL or MongoDB by changing DB_CONNECTION in the .env file
// Example for PostgreSQL:
// $serverUri = "pgsql:host={$serverName};dbname={$databaseName}";