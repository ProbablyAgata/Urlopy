<?php
session_start();
require_once 'connect.php';

error_log("Submitted manager_id: " . $_POST['manager_id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_leave'])) {
    // Formularz został wysłany
    $employee_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
    $manager_id = (int)$_POST['manager_id']; // Korzystanie z wartości z formularza
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    $status = 'oczekujacy';

    try {
        // Aktualizowane zapytanie SQL do pasowania do struktury tabeli
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
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }

    $conn->close();
} else {
    header("Location: employee_view.php");
    exit();
}
