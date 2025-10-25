<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // blank in XAMPP by default
$dbname = "userdb";

// 1. Connect to database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// 2. Process POST request only
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $age = trim($_POST['age'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $contact = preg_replace('/\D/', '', ($_POST['contact'] ?? ''));

    // 3. Validation
    if (empty($name) || empty($address) || empty($gender) || empty($age) || empty($email) || empty($contact)) {
        echo json_encode(["status" => "error", "message" => "Please fill all fields."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format."]);
        exit;
    }

    if (!is_numeric($age) || $age < 1) {
        echo json_encode(["status" => "error", "message" => "Invalid age value."]);
        exit;
    }

    // 4. Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO email_reg (name, address, gender, age, email, contact) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $name, $address, $gender, $age, $email, $contact);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Record inserted successfully!"]);
    } else {
        if ($conn->errno == 1062) {
            echo json_encode(["status" => "error", "message" => "Insert failed: Email already registered."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Insert failed: " . $stmt->error]);
        }
    }


    
    $stmt->close();
}

$conn->close();
?>
