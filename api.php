<?php
// Database connection details
$host = 'localhost';
$dbname = 'rifatosan';
$username = 'root';
$password = 'Sowhatseven7*';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create the table if it doesn't exist
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS sold_numbers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            number_sold INT NOT NULL,
            sold_date DATE NOT NULL,
            custom_name VARCHAR(255) NOT NULL,
            phone_number VARCHAR(20) NOT NULL
        )
    ";
    $pdo->exec($createTableQuery);

    // Check if the request is a POST request
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the data from the POST request
        $numberSold = isset($_POST['number_sold']) ? (int)$_POST['number_sold'] : null;
        $customName = isset($_POST['custom_name']) ? trim($_POST['custom_name']) : null;
        $phoneNumber = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;

        if ($numberSold !== null && $customName && $phoneNumber) {
            // Insert the data into the table
            $insertQuery = "
                INSERT INTO sold_numbers (number_sold, sold_date, custom_name, phone_number) 
                VALUES (:number_sold, :sold_date, :custom_name, :phone_number)
            ";
            $stmt = $pdo->prepare($insertQuery);
            $stmt->execute([
                ':number_sold' => $numberSold,
                ':sold_date' => date('Y-m-d'),
                ':custom_name' => $customName,
                ':phone_number' => $phoneNumber
            ]);

            echo json_encode(['success' => true, 'message' => 'Data saved successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
        }
    }

    // Check if the request is a GET request
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if(isset($_GET['number_sold'])){
            $numberSold = (int)$_GET['number_sold'];
            // Retrieve the specific number's information from the table
            $selectQuery = "SELECT * FROM sold_numbers WHERE number_sold = :number_sold";
            $stmt = $pdo->prepare($selectQuery);
            $stmt->execute([':number_sold' => $numberSold]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo json_encode(['success' => true, 'data' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Number not found.']);
            }
            exit;
        }
        // Retrieve all the numbers from the table
        $selectQuery = "SELECT * FROM sold_numbers";
        $stmt = $pdo->query($selectQuery);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $results]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}