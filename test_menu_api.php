<?php
/**
 * Menu API Diagnostic Tool
 * Upload this to your InfinityFree hosting and visit it in your browser
 * to diagnose menu loading issues
 */

// Enable error display
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu API Diagnostics</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #2563eb; }
        h2 { color: #334155; margin-top: 0; }
        .success { color: #16a34a; font-weight: bold; }
        .error { color: #dc2626; font-weight: bold; }
        .warning { color: #ea580c; font-weight: bold; }
        .info { background: #eff6ff; padding: 15px; border-left: 4px solid #2563eb; margin: 10px 0; }
        .code { background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 4px; overflow-x: auto; }
        pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { background: #f8fafc; font-weight: 600; }
    </style>
</head>
<body>
    <h1>🔍 Menu API Diagnostics</h1>
    
    <?php
    // Test 1: Check if config file exists
    echo '<div class="test-section">';
    echo '<h2>Test 1: Configuration File</h2>';
    $configPath = __DIR__ . '/api/config.php';
    if (file_exists($configPath)) {
        echo '<p class="success">✓ config.php found</p>';
        require_once $configPath;
        
        echo '<table>';
        echo '<tr><th>Setting</th><th>Value</th></tr>';
        echo '<tr><td>DB_HOST</td><td>' . DB_HOST . '</td></tr>';
        echo '<tr><td>DB_NAME</td><td>' . DB_NAME . '</td></tr>';
        echo '<tr><td>DB_USER</td><td>' . DB_USER . '</td></tr>';
        echo '<tr><td>DB_PASS</td><td>' . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : '<span class="error">EMPTY</span>') . '</td></tr>';
        echo '</table>';
    } else {
        echo '<p class="error">✗ config.php NOT found at: ' . $configPath . '</p>';
        echo '<div class="info">Create api/config.php with your InfinityFree database credentials</div>';
    }
    echo '</div>';
    
    // Test 2: Database Connection
    echo '<div class="test-section">';
    echo '<h2>Test 2: Database Connection</h2>';
    
    if (file_exists($configPath)) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
            echo '<p class="success">✓ Database connection successful!</p>';
            
            // Test 3: Check if menu_items table exists
            echo '</div><div class="test-section">';
            echo '<h2>Test 3: Menu Items Table</h2>';
            
            try {
                $stmt = $pdo->query("SHOW TABLES LIKE 'menu_items'");
                $tableExists = $stmt->fetch();
                
                if ($tableExists) {
                    echo '<p class="success">✓ menu_items table exists</p>';
                    
                    // Test 4: Count menu items
                    echo '</div><div class="test-section">';
                    echo '<h2>Test 4: Menu Data</h2>';
                    
                    $stmt = $pdo->query("SELECT COUNT(*) as total FROM menu_items");
                    $count = $stmt->fetch();
                    
                    if ($count['total'] > 0) {
                        echo '<p class="success">✓ Found ' . $count['total'] . ' menu items</p>';
                        
                        // Show sample data
                        $stmt = $pdo->query("SELECT category, COUNT(*) as count FROM menu_items GROUP BY category");
                        $categories = $stmt->fetchAll();
                        
                        echo '<table>';
                        echo '<tr><th>Category</th><th>Item Count</th></tr>';
                        foreach ($categories as $cat) {
                            echo '<tr><td>' . htmlspecialchars($cat['category']) . '</td><td>' . $cat['count'] . '</td></tr>';
                        }
                        echo '</table>';
                        
                        // Show sample items
                        echo '<h3>Sample Items (First 5):</h3>';
                        $stmt = $pdo->query("SELECT name, category, price, lang FROM menu_items LIMIT 5");
                        $samples = $stmt->fetchAll();
                        
                        echo '<table>';
                        echo '<tr><th>Name</th><th>Category</th><th>Price</th><th>Language</th></tr>';
                        foreach ($samples as $item) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($item['name']) . '</td>';
                            echo '<td>' . htmlspecialchars($item['category']) . '</td>';
                            echo '<td>₹' . $item['price'] . '</td>';
                            echo '<td>' . $item['lang'] . '</td>';
                            echo '</tr>';
                        }
                        echo '</table>';
                        
                    } else {
                        echo '<p class="error">✗ No menu items found in database</p>';
                        echo '<div class="info">You need to import database_setup.sql to populate the menu items</div>';
                    }
                    
                } else {
                    echo '<p class="error">✗ menu_items table does NOT exist</p>';
                    echo '<div class="info">You need to import database_setup.sql to create the table</div>';
                }
                
            } catch (PDOException $e) {
                echo '<p class="error">✗ Error checking table: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            
        } catch (PDOException $e) {
            echo '<p class="error">✗ Database connection failed</p>';
            echo '<div class="code"><pre>' . htmlspecialchars($e->getMessage()) . '</pre></div>';
            
            echo '<div class="info">';
            echo '<strong>Common InfinityFree Issues:</strong><br>';
            echo '1. DB_HOST should be something like "sqlXXX.infinityfreeapp.com"<br>';
            echo '2. DB_NAME usually has a prefix like "epiz_XXXXX_qrify_db"<br>';
            echo '3. DB_USER usually has a prefix like "epiz_XXXXX"<br>';
            echo '4. Make sure you created the database in cPanel first';
            echo '</div>';
        }
    }
    echo '</div>';
    
    // Test 5: API Endpoint
    echo '<div class="test-section">';
    echo '<h2>Test 5: API Endpoint Test</h2>';
    
    $apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . 
              "://" . $_SERVER['HTTP_HOST'] . 
              dirname($_SERVER['REQUEST_URI']) . '/api/get_menu.php?lang=en';
    
    echo '<p>Testing: <code>' . htmlspecialchars($apiUrl) . '</code></p>';
    
    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        $data = json_decode($response, true);
        if ($data && !isset($data['error'])) {
            echo '<p class="success">✓ API endpoint working correctly!</p>';
            echo '<p>Categories returned: ' . implode(', ', array_keys($data)) . '</p>';
        } else {
            echo '<p class="error">✗ API returned error</p>';
            echo '<div class="code"><pre>' . htmlspecialchars($response) . '</pre></div>';
        }
    } else {
        echo '<p class="error">✗ API request failed (HTTP ' . $httpCode . ')</p>';
        echo '<div class="code"><pre>' . htmlspecialchars($response) . '</pre></div>';
    }
    
    echo '</div>';
    
    // Summary
    echo '<div class="test-section">';
    echo '<h2>📋 Next Steps</h2>';
    
    if (file_exists($configPath) && isset($pdo) && isset($count) && $count['total'] > 0) {
        echo '<p class="success">✓ All tests passed! Your menu should be working.</p>';
        echo '<p>If the menu still doesn\'t show on home.php:</p>';
        echo '<ol>';
        echo '<li>Check browser console (F12) for JavaScript errors</li>';
        echo '<li>Check Network tab to see if api/get_menu.php is being called</li>';
        echo '<li>Clear browser cache and refresh</li>';
        echo '</ol>';
    } else {
        echo '<p class="warning">⚠ Issues detected. Follow these steps:</p>';
        echo '<ol>';
        if (!file_exists($configPath)) {
            echo '<li class="error">Create api/config.php with your InfinityFree database credentials</li>';
        }
        if (!isset($pdo)) {
            echo '<li class="error">Fix database connection (check credentials)</li>';
        }
        if (isset($tableExists) && !$tableExists) {
            echo '<li class="error">Import database_setup.sql via phpMyAdmin</li>';
        }
        if (isset($count) && $count['total'] == 0) {
            echo '<li class="error">Database is empty - import database_setup.sql</li>';
        }
        echo '</ol>';
    }
    
    echo '</div>';
    ?>
    
    <div class="test-section">
        <h2>🔄 Refresh Tests</h2>
        <button onclick="location.reload()" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
            Run Tests Again
        </button>
    </div>
</body>
</html>
