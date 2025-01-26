<?php
header("Content-Type: application/json");
require ("../../app/db/base.php");
$dateTime = new DateTime('now', new DateTimeZone('Asia/Dhaka'));
$access_time=$dateTime->format("Y-m-d, h:i:s A");

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the input from the request body
    $data = json_decode(file_get_contents("php://input"));

    // Check if username and password are provided
    if (isset($data->mobile) && isset($data->password)) {
        $username = $data->mobile;
        $password = $data->password;

        // Prepare the SQL query
        $stmt = $conn->prepare("SELECT user_id,PBI_ID,mobile,email,fname,picture_url,passwords as password FROM users WHERE mobile = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verify the password
            if (password_verify($password, $user['password'])) {
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $domain = $_SERVER['HTTP_HOST'];
                $path = $_SERVER['REQUEST_URI'];
                $currentUrl = $protocol . "://" . $domain ;

                // Successful login
                echo json_encode([
                    "status" => 200,
                    "message" => "Login successful",
                    "user_id" => $user['user_id'],
                    "PBI_ID" => $user['PBI_ID'],
                    "mobile" => $user['mobile'],
                    "email" => $user['email'],
                    "name" => $user['fname'],
                    "profilePicture" => $currentUrl.substr($user['picture_url'], 2)
                ]);
                mysqli_query($conn, "INSERT INTO user_activity_log (user_id,access_time,access_status,platform,os) 
                VALUES ('".$user['user_id']."','".$access_time."','success','app','android')");
            } else {
                // Invalid password
                mysqli_query($conn, "INSERT INTO user_activity_log (user_id,access_time,access_status,platform,os) 
                VALUES ('".$user['user_id']."','".$access_time."','decline','app','android')");
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
