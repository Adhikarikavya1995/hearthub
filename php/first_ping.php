<?php

include 'dbconn.php';

// Check if the request is a POST request

    // Retrieve data from POST request
    
    //$timestamp = $_POST['timestamp'];
$machine_name = "Kavya";
$location = "Sheffield";
$current_timestamp = date("Y-m-d H:i:s");
$status = "healthy";
   
    
    // Check if the request method is POST

  // Query to check if the machine name exists
  $sql_check = "SELECT COUNT(*) AS count FROM remote_monitoring_machine WHERE machine_name = ?";
  $stmt_check = $conn->prepare($sql_check);
  $stmt_check->bind_param("s", $machine_name);
  $stmt_check->execute();
  $result_check = $stmt_check->get_result();
  $row_check = $result_check->fetch_assoc();

  if ($row_check['count'] > 0) {
      // Machine name already exists
      echo json_encode(["error" => "Machine name already exists. Please use a different name."]);
  } else {
      // Machine name does not exist, save it to the database
      $sql_insert = "INSERT INTO remote_monitoring_machine (machine_name, location, timestamp, status) VALUES (?, ?, ?,?)";
      $stmt_insert = $conn->prepare($sql_insert);
      $stmt_insert->bind_param("ssss", $machine_name, $location,$current_timestamp,$status);

      if ($stmt_insert->execute()) {
          echo json_encode(["message" => "Machine data saved successfully."]);
      } else {
          echo json_encode(["error" => "Failed to save machine data: " . $conn->error]);
      }

      $stmt_insert->close();
  }

  // Close the statement and connection
  $stmt_check->close();
  $conn->close();

?>