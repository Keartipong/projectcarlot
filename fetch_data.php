<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Database configuration
$host = '151.106.124.154'; // Change this to your database host
$dbname = 'u583789277_wag7'; // Change this to your database name
$username = 'u583789277_wag7'; // Change this to your database username
$password = '2567Concept'; // Change this to your database password

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Retrieve the latest card ID where status_id is 6
$sql = "
 SELECT card.user_license_plate, lot.parked_zone, lot.number, lot.bay_id, bay.bay_name, card.card_id
        FROM card 
        INNER JOIN lot ON card.lot_id = lot.lot_id 
        INNER JOIN bay ON lot.bay_id = bay.bay_id  -- JOIN ตาราง bay
        WHERE lot.status_id = '6'  
        ORDER BY card.time DESC
        LIMIT 1
";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute();
    $carData = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
}

// If no data is found, set default values
if (!$carData) {
    $carData = [
        'card_id' => 'Not Found',
        'user_license_plate' => 'Not Found',
        'bay_name' => 'Not Found',
        'parking_slot' => 'Not Found',
        'zone' => 'Not Found'
    ];
} else {
    // Set the parking_slot and zone values from the fetched data
    $carData['parking_slot'] = $carData['number']; // Use the 'number' from the lot table
    $carData['zone'] = $carData['parked_zone'];   // Use the 'parked_zone' from the lot table
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($carData);
?>
