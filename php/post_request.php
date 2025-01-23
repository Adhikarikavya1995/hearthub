<?php
// Set the content-type to JSON
header('Content-Type: application/json');

// Include the database connection
include 'dbconn.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Retrieve data from POST request
        $machine_id = $_POST['machine_id'] ?? null;
        $time_engaged = $_POST['time_engaged'] ?? null;
        $distance_while_active = $_POST['distance_while_active'] ?? null;
        $hand_position = $_POST['hand_position'] ?? null;
        $rate = $_POST['rate'] ?? null;
        $compression = $_POST['compression'] ?? null;
        $recoil = $_POST['recoil'] ?? null;
        $watched_animation = $_POST['watched_animation'] ?? null;
        $sessions_played = $_POST['sessions_played'] ?? null;
        $question_1_response = $_POST['question_1_response'] ?? null;
        $question_2_response = $_POST['question_2_response'] ?? null;
        $session_status = $_POST['session_status'] ?? null;
        $updated_at = $_POST['updated_at'] ?? null;

        // Validate required inputs
        if (!$machine_id || !$session_status) {
            throw new Exception("Missing required fields: machine_id or session_status.");
        }

        // Map session_status to session_id
        $session_id_map = [
            'footfall' => 1,
            'loitering' => 2,
            'partially_engaged' => 3,
            'fully_engaged' => 4,
            'fully_engaged_with_survey' => 5,
            'repeated_sessions' => 6
        ];
        $session_id = $session_id_map[$session_status] ?? null;

        if (!$session_id) {
            throw new Exception("Invalid session_status value.");
        }

        // Map survey questions to survey_id
        $survey_map = [
            "0|0" => 1,
            "1|0" => 2,
            "1|null" => 3,
            "1|1" => 4,
            "null|null" => 5,
            "0|null" => 6
        ];
        $survey_key = "{$question_1_response}|{$question_2_response}";
        $survey_id = $survey_map[$survey_key] ?? null;

        if (!$survey_id) {
            throw new Exception("Invalid survey values.");
        }

        // Insert the data into the database
        $sql = "INSERT INTO remote_monitoring_usermetrics (machine_id, time_engaged, distance_while_active, hand_position, rate, compression, recoil, watched_animation, sessions_played, session_id, survey_id, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssssss",
            $machine_id,
            $time_engaged,
            $distance_while_active,
            $hand_position,
            $rate,
            $compression,
            $recoil,
            $watched_animation,
            $sessions_played,
            $session_id,
            $survey_id,
            $updated_at
        );

        if ($stmt->execute()) {
            // Respond with the machine_id on successful insertion
            echo $machine_id;
        } else {
            throw new Exception("Database insertion failed: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        // Catch and send the error message back to Unity
        http_response_code(400); // Set the HTTP status code to 400 (Bad Request)
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    // Handle invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Invalid request method. Only POST requests are allowed."]);
}
?>
