<?php
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form data
    $process_id_arr = $_POST['process_id'];
    $arrivalTime_arr = $_POST['arrival_time'];
    $burstTime_arr = $_POST['burst_time'];
    $time_quantum = $_POST['time_quantum']; // Extracting time quantum from POST data

    // Initialize arrays to store completion time, turnaround time, and waiting time
    $completionTime_arr = array();
    $turnaroundTime_arr = array();
    $waitingTime_arr = array();

    // Calculate completion time, turnaround time, and waiting time using Round Robin Scheduling
    $total_processes = count($process_id_arr);
    $remaining_burstTime_arr = $burstTime_arr;
    $current_time = 0;
    $completed_processes = 0;
    $ganttChart = array();
    $start_times = array_fill(0, $total_processes, 0);
    while ($completed_processes < $total_processes) {
        $process_executed = false; // Flag to track if a process was executed in the current time quantum

        for ($i = 0; $i < $total_processes; $i++) {
            if ($remaining_burstTime_arr[$i] > 0 && $arrivalTime_arr[$i] <= $current_time) {
                $start_times[$i] = max($current_time, $arrivalTime_arr[$i]); 
                // Process the current process then mark as complete and set the the 3 array
                // Process completes within time quantum
                // no burst time left
                if ($remaining_burstTime_arr[$i] <= $time_quantum) {
                    $current_time += $remaining_burstTime_arr[$i];
                    $remaining_burstTime_arr[$i] = 0;

                    $completionTime_arr[$i] = $current_time;
                    $turnaroundTime_arr[$i] = $current_time - $arrivalTime_arr[$i];
                    $waitingTime_arr[$i] = $turnaroundTime_arr[$i] - $burstTime_arr[$i];
                    $completed_processes++;
                } else {
                    // Process still needs more time
                    // still have burst time
                    $current_time += $time_quantum;
                    $remaining_burstTime_arr[$i] -= $time_quantum;
                }

                // Record the Gantt chart
                // regardless if burst time is greater than time quantum or within time quantum
                $ganttChart[] = array(
                    'process_id' => $process_id_arr[$i],
                    'start_time' => $start_times[$i],
                    'end_time' => $current_time
                );

                $process_executed = true; // Set flag to true since a process was executed
            }
        }

        // If no process was executed, increment current_time to avoid infinite loop
        if (!$process_executed) {
            $current_time++;
        }
    }

    $response = array(
        'success' => true,
        'message' => 'Form data processed successfully.',
        'data' => array(
            'process_ids' => $process_id_arr,
            'arrival_times' => $arrivalTime_arr,
            'burst_times' => $burstTime_arr,
            'completion_times' => $completionTime_arr,
            'turnaround_times' => $turnaroundTime_arr,
            'waiting_times' => $waitingTime_arr,
            'time_quantum' => $time_quantum,
            'gantt_chart' => $ganttChart,
            'completion_time_average' => getAverage($completionTime_arr),
            'turnaround_time_average' => getAverage($turnaroundTime_arr),
            'waiting_time_average' => getAverage($waitingTime_arr),
        )
    );
    echo json_encode($response);
}

function getAverage($arr)
{
    $average =  (array_sum($arr)) / (count($arr));
    return $average;
}
