<?php
// Set the content-type to JSON
header('Content-Type: application/json');

// Include the database connection
include 'dbconn.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from POST request
    
    $timestamp = $_POST['timestamp'];
    $compression = $_POST['compression'];
    $recoil = $_POST['recoil'];
    $handposition = $_POST['handposition'];
    $overall_score = $_POST['overall_score'];
    $feedback = $_POST['feedback'];



    // Insert the data into a table called 'user_data'
    $sql = "INSERT INTO user_data (timestamp, compression, recoil, hand_position, overall_score, feedback)
            VALUES ('$timestamp', '$compression', '$recoil', '$handposition', '$overall_score', '$feedback')";

    if ($conn->query($sql) === TRUE) {
        // Respond back with success
        echo json_encode(["message" => "Data inserted successfully"]);
    } else {
        // Respond with error if insertion failed
        echo json_encode(["message" => "Error: " . $conn->error]);
    }

    $conn->close();
} else {
    echo json_encode(["message" => "Invalid request method"]);
}
?>
