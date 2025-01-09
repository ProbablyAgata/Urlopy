<?php
session_start();
require_once 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_leave'])) {
    // Get and sanitize form data
    $employee_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
    $manager_id = 1; // Default manager ID, adjust as needed
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $status = 'oczekujacy';

    try {
        // Updated SQL to match your table structure
        $sql = "INSERT INTO wnioski_urlopowe (employee_id, manager_id, poczatek_urlopu, koniec_urlopu, powod, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param(
                'iissss',
                $employee_id,
                $manager_id,
                $start_date,
                $end_date,
                $reason,
                $status
            );

            if ($stmt->execute()) {
                header("Location: employee_view.php");
                exit();
            } else {
                echo "Execute Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Prepare Error: " . $conn->error;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn->close();
} else {
    header("Location: employee_view.php");
    exit();
}
