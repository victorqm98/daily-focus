<?php

require_once __DIR__ . '/vendor/autoload.php';

use Ramsey\Uuid\Uuid;

// Test database connection
try {
    $pdo = new PDO(
        'mysql:host=daily-focus-mysql;port=3306;dbname=daily-focus;charset=utf8mb4',
        'challenge',
        'challenge',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    
    echo "Database connection successful\n";
    
    // Test UUID generation
    $uuid = Uuid::uuid4();
    echo "Generated UUID: " . $uuid->toString() . "\n";
    
    // Test inserting a user
    $stmt = $pdo->prepare("INSERT INTO users (id, username, email, hashed_password, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
    $result = $stmt->execute([
        $uuid->toString(),
        'test_user_direct',
        'direct@example.com',
        password_hash('test_password', PASSWORD_DEFAULT)
    ]);
    
    if ($result) {
        echo "User inserted successfully\n";
        
        // Verify the user was saved
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$uuid->toString()]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "User verified in database: " . $user['username'] . "\n";
        } else {
            echo "User not found in database\n";
        }
    } else {
        echo "Failed to insert user\n";
    }

    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
