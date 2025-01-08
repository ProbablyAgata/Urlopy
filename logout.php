<?php
session_start();
require_once 'connect.php';

// Only redirect to index if there's no session at all
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Only process actions when form is submitted from the confirmation page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'confirm_logout') {
            session_destroy();
            header("Location: index.html");
            exit();
        } elseif ($_POST['action'] === 'cancel') {
            $role = $_SESSION['role'] ?? 'pracownik'; // Default to pracownik if role not set
            switch ($role) {
                case 'pracownik':
                    header("Location: employee_view.php");
                    exit();
                case 'manager':
                    header("Location: manager_view.php");
                    exit();
                default:
                    header("Location: index.html");
                    exit();
            }
        }
    }
}

// If no POST action, display the confirmation page
?>
<!DOCTYPE html>
<!-- Rest of the HTML remains the same -->
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Potwierdzenie wylogowania</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="logout-confirmation">
        <h2>Czy na pewno chcesz się wylogować?</h2>
        <form method="POST" action="logout.php" style="gap: 10px;">
            <button type="submit" name="action" value="confirm_logout" class="logout-button">Tak, wyloguj</button>
            <button type="submit" name="action" value="cancel" class="cancel-button">Anuluj</button>
        </form>
    </div>
</body>

</html>