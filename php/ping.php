<?php
// Set the content-type to JSON
header('Content-Type: application/json');

// Include the database connection
include 'dbconn.php';

// Check if the POST request contains the necessary data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve data from POST request
  $machine_id = isset($_POST['machine_id']) ? (int)$_POST['machine_id'] : null;
  $timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : null;

  // Check if machine_id and timestamp are not null
  if ($machine_id && $timestamp) {
      // Prepare SQL statement to update the timestamp for the specific machine ID
      $stmt = $conn->prepare("UPDATE remote_monitoring_machine SET timestamp = ? WHERE id = ?");
      $stmt->bind_param('si', $timestamp, $machine_id);

      // Execute the update query
      if ($stmt->execute()) {
          echo json_encode(["status" => "success", "message" => "Timestamp updated successfully"]);
      } else {
          echo json_encode(["status" => "error", "message" => "Failed to update timestamp"]);
      }

      // Close the prepared statement
      $stmt->close();
  } else {
      echo json_encode(["status" => "error", "message" => "Invalid input"]);
  }
} else {
  echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}

// Close database connection
$conn->close();

?>


