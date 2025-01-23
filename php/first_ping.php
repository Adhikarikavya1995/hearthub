<?php 

include 'dbconn.php';

try {
    

    // $machine_name = "Kavya5";
    // $location = "Sheffield";

    // $machine_name = strtolower(str_replace(' ', '', $machine_name));

     // Retrieve POST parameters
     $machine_name = strtolower(str_replace(' ', '', $_POST['machine_name'] ?? ''));
     $location = $_POST['location'] ?? '';

    if (empty($machine_name) || empty($location)) {
        throw new Exception("Machine name and location cannot be empty.");
    }

    $current_timestamp = date("Y-m-d H:i:s");
    $status = "healthy";
    $email1_sent = 0; // Default value for the email1_sent field (False)
    $email2_sent = 0;
    $email3_sent = 0;

    // Query to check if the machine name exists
    $sql_check = "SELECT COUNT(*) AS count FROM remote_monitoring_machine WHERE machine_name = ?";
    $stmt_check = $conn->prepare($sql_check);

    if (!$stmt_check) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    $stmt_check->bind_param("s", $machine_name);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if (!$result_check) {
        throw new Exception("Failed to execute SQL query: " . $stmt_check->error);
    }

    $row_check = $result_check->fetch_assoc();

    if ($row_check['count'] > 0) {
        // Machine name already exists
        echo json_encode(["error" => "Machine name already exists. Please use a different name."]);
    } else {
        // Machine name does not exist, save it to the database
        $sql_insert = "INSERT INTO remote_monitoring_machine (machine_name, location, timestamp, status, email1_sent, email2_sent, email3_sent) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);

        if (!$stmt_insert) {
            throw new Exception("Failed to prepare SQL statement for insert: " . $conn->error);
        }

        $stmt_insert->bind_param("ssssiii", $machine_name, $location, $current_timestamp, $status, $email1_sent, $email2_sent, $email3_sent );

        if ($stmt_insert->execute()) {
            // Fetch the machine ID of the newly inserted record
            $machine_id = $conn->insert_id; // Get the last inserted ID
            //echo "Machine ID: " . $machine_id;
            echo json_encode(["machine_id" => $machine_id]);
        } else {
            throw new Exception("Failed to save machine data: " . $stmt_insert->error);
        }

        $stmt_insert->close();
    }

    // Close the statement and connection
    $stmt_check->close();
    $conn->close();
} catch (Exception $e) {
    // Handle errors and send a JSON response
    echo json_encode(["error" => $e->getMessage()]);
} catch (Error $e) {
    // Handle PHP errors and send a JSON response
    echo json_encode(["error" => "A system error occurred: " . $e->getMessage()]);
}

?>
