<?php
header("Content-Type: application/json");

// Database configuration
$host = "localhost";
$db_name = "icp_distribution";
$db_username = "icp_distribution";
$db_password = "Allahis1!!@@##";

// Create a MySQLi connection
$conn = new mysqli($host, $db_username, $db_password, $db_name);

// Check if the connection was successful
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input from the request body
    $data = json_decode(file_get_contents("php://input"));

    // Check if username and password are provided
    if (isset($data->username) && isset($data->password)) {
        $username = $data->username;
        $password = $data->password;

        // Prepare the SQL query
        $stmt = $conn->prepare("SELECT user_id as id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Successful login
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful",
                    "user_id" => $user['id']
                ]);
            } else {
                // Invalid password
                echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
            }
        } else {
            // Invalid username
            echo json_encode(["status" => "error", "message" => "User not found"]);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Missing username or password
        echo json_encode(["status" => "error", "message" => "Username and password are required"]);
    }
} else {
    // Invalid request method
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

// Close the connection
$conn->close();
?>
