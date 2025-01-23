<?php
// Set the content-type to JSON
header('Content-Type: application/json');

// Include the database connection
include 'dbconn.php';

// Function to handle and log errors, then send the error to Unity
function handleError($message) {
    echo json_encode(["status" => "error", "message" => $message]);
    exit;
}

try {
    // Check if the POST request contains the necessary data
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        handleError("Invalid request method");
    }

    // Retrieve data from POST request
    $machine_id = isset($_POST['machine_id']) ? (int)$_POST['machine_id'] : null;
    $timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : null;

    // Validate input
    if (!$machine_id || !$timestamp) {
        handleError("Invalid input: Machine ID and timestamp are required");
    }

    // Prepare SQL statement to update the timestamp for the specific machine ID
    $stmt = $conn->prepare("UPDATE remote_monitoring_machine SET timestamp = ? WHERE id = ?");
    if (!$stmt) {
        handleError("Failed to prepare statement: " . $conn->error);
    }

    // Bind parameters
    if (!$stmt->bind_param('si', $timestamp, $machine_id)) {
        handleError("Failed to bind parameters: " . $stmt->error);
    }

    // Execute the update query
    if (!$stmt->execute()) {
        handleError("Failed to execute query: " . $stmt->error);
    }

    // Check if the query affected any rows
    if ($stmt->affected_rows > 0) {
        echo json_encode(["status" => "success", "message" => "Timestamp updated successfully"]);
    } else {
        handleError("No rows updated. Check if the Machine ID exists");
    }

    // Close the prepared statement
    $stmt->close();
} catch (Exception $e) {
    // Handle unexpected exceptions
    handleError("Unexpected error: " . $e->getMessage());
} finally {
    // Close database connection
    if (isset($conn)) {
        $conn->close();
    }
}
?>