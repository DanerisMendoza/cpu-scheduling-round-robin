<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form data
    $process_id_arr = $_POST['process_id'];
    $arrivalTime_arr = $_POST['arrival_time'];
    $burstTime_arr = $_POST['burst_time'];
    $time_quantum = $_POST['time_quantum']; // Extracting time quantum from POST data

    // Calculate completion time, turnaround time, and waiting time
    $completionTime_arr = array();
    $turnaroundTime_arr = array();
    $waitingTime_arr = array();

    $n = count($process_id_arr);
    $remainingTime_arr = $burstTime_arr;
    $currentTime = 0;

    // Initialize waiting time array with 0s
    for ($i = 0; $i < $n; $i++) {
        $waitingTime_arr[$i] = 0;
    }

    // Gantt chart data
    $ganttChart = array();
    $currentProcess = null;

    while (true) {
        $done = true;

        for ($i = 0; $i < $n; $i++) {
            if ($remainingTime_arr[$i] > 0) {
                $done = false;

                if ($remainingTime_arr[$i] > $time_quantum) {
                    $currentTime += $time_quantum;
                    $remainingTime_arr[$i] -= $time_quantum;
                } else {
                    $currentTime += $remainingTime_arr[$i];
                    $completionTime_arr[$i] = $currentTime;
                    $remainingTime_arr[$i] = 0;

                    // Calculate waiting time
                    $waitingTime_arr[$i] = $currentTime - $burstTime_arr[$i] - $arrivalTime_arr[$i];
                    if ($waitingTime_arr[$i] < 0) {
                        $waitingTime_arr[$i] = 0;
                    }

                    // Calculate turnaround time
                    $turnaroundTime_arr[$i] = $completionTime_arr[$i] - $arrivalTime_arr[$i];
                }

                // Gantt chart update
                if ($currentProcess !== $i) {
                    if ($currentProcess !== null) {
                        $ganttChart[] = array(
                            'process_id' => $process_id_arr[$currentProcess],
                            'start' => $ganttStart,
                            'end' => $currentTime
                        );
                    }
                    $currentProcess = $i;
                    $ganttStart = $currentTime;
                }
            }
        }

        if ($done) {
            break;
        }
    }

    // Add the last segment to the Gantt chart
    if ($currentProcess !== null) {
        $ganttChart[] = array(
            'process_id' => $process_id_arr[$currentProcess],
            'start' => $ganttStart,
            'end' => $currentTime
        );
    }

    // Construct a response
    $response = array(
        'success' => true,
        'message' => 'Form data received successfully.',
        'data' => array(
            'process_ids' => $process_id_arr,
            'arrival_times' => $arrivalTime_arr,
            'burst_times' => $burstTime_arr,
            'completion_times' => $completionTime_arr,
            'turnaround_times' => $turnaroundTime_arr,
            'waiting_times' => $waitingTime_arr,
            'time_quantum' => $time_quantum, // Include time quantum in response
            'gantt_chart' => $ganttChart // Include Gantt chart in response
        )
    );

    // Encode the response as JSON
    echo json_encode($response);
}
?>
