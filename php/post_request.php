<?php
// Set the content-type to JSON
header('Content-Type: application/json');

// Include the database connection
include 'dbconn.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve data from POST request
    
    //$timestamp = $_POST['timestamp'];
    $machine_id = $_POST['machine_id'];
    $time_engaged = $_POST['time_engaged'];
    $distance_while_active = $_POST['distance_while_active'];
    $hand_position = $_POST['hand_position'];
    $rate = $_POST['rate'];
    $compression = $_POST['compression'];
    $recoil = $_POST['recoil'];
    $watched_animation = $_POST['watched_animation'];
    $sessions_played = $_POST['sessions_played'];
    $question_1_response = $_POST['question_1_response'];
    $question_2_response = $_POST['question_2_response'];
    $session_status = $_POST['session_status'];


    // Map session_status to session_id
    $session_id = null;
    if ($session_status === 'footfall') {
        $session_id = 1;
    } elseif ($session_status === 'loitering') {
        $session_id = 2;
    } elseif ($session_status === 'partially_engaged') {
        $session_id = 3;
    } elseif ($session_status === 'fully_engaged') {
        $session_id = 4;
    } 
    elseif ($session_status === 'fully_engaged_with_survey') {
        $session_id = 5;
    }
    elseif ($session_status === 'repeated_sessions') {
        $session_id = 6;
    }
    else {
        // Default or unrecognized session_status
        echo json_encode(["message" => "Invalid session_status value"]);
        http_response_code(400); // Set the HTTP status code to 400 (Bad Request)
        exit;
      
    }


    // Map survey questions to survey_id
    $survey_id = null;
    if ($question_1_response === "0" && $question_2_response === "0") {
        $survey_id = 1;

    } elseif ($question_1_response === "1" && $question_2_response === "0") {
        $survey_id = 2;
    } 

    elseif ($question_1_response === "1" && $question_2_response === "null") {
        $survey_id = 3;
    }

    elseif ($question_1_response === "1" && $question_2_response === "1") {
        $survey_id = 4;
    }

    elseif ($question_1_response === "null" && $question_2_response === "null") {
        $survey_id = 5;
    }

    elseif ($question_1_response === "0" && $question_2_response === "null") {
        $survey_id = 6;
    }
    

    else {
        // Default or unrecognized session_status
        echo json_encode(["message" => "Invalid survey value"]);
        http_response_code(400); // Set the HTTP status code to 400 (Bad Request)
        exit;
    }


    // Insert the data into a table called 'user_data'
    $sql = "INSERT INTO remote_monitoring_usermetrics (machine_id, time_engaged, distance_while_active, hand_position, rate, compression, recoil, watched_animation, sessions_played, session_id, survey_id)
            VALUES ('$machine_id', '$time_engaged', '$distance_while_active', '$hand_position', '$rate','$compression',
            '$recoil','$watched_animation','$sessions_played','$session_id','$survey_id')";

    if ($conn->query($sql) === TRUE) {
        // Respond back with success
        echo "Data inserted successfully";
    } else {
        // Respond with error if insertion failed
        echo json_encode(["message" => "Invalid data"]);
        
    }

    $conn->close();

} else {
    echo json_encode(["message" => "Invalid request"]);
}
?>
