<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form data
    $process_id_arr = $_POST['process_id'];
    $arrivalTime_arr = $_POST['arrival_time'];
    $burstTime_arr = $_POST['burst_time'];
    $time_quantum = $_POST['time_quantum'];
    
    // // Construct a response
    $response = array(
        'success' => true,
        'message' => 'Form data received successfully.',
        'data' => array(
            'process_ids' => $process_id_arr,
            'arrival_times' => $arrivalTime_arr,
            'burst_times' => $burstTime_arr
        )
    );
    // $response['data'] = implode(', ', $process_id_arr);
    // $response['data'] = print_r($process_id_arr);
    $response['data'] = $time_quantum;

    // Encode the response as JSON
    echo json_encode($response);
} 

?>
