<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $compression = $_POST['compression'];
    $recoil = $_POST['recoil'];
    $rate = $_POST['rate'];
    $hand_position = $_POST['hand_position'];
    $machine_id = $_POST['machine_id'];

    // Encode the metrics into a URL-safe string
    $data = [
        'compression' => $compression,
        'recoil' => $recoil,
        'rate' => $rate,
        'hand_position' => $hand_position,
        'machine_id' => $machine_id,

    ];
    $encodedData = base64_encode(json_encode($data));
    $url = "https://hearhub-fkgmazbcagh0aecv.uksouth-01.azurewebsites.net/metrics/{$encodedData}";

    // Return the encoded URL to Unity
    echo json_encode([
        'encoded_url' => $url,
    ]);
} else {
    echo "Invalid request";
}
?>
