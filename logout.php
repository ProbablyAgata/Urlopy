<?php
session_start();
require_once 'connect.php';

// Przekierowanie do index.html, jeśli nie ma sesji
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

// Tylko przetwarzaj akcje, gdy formularz jest przesyłany ze strony potwierdzenia
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'confirm_logout') {
            session_destroy();
            header("Location: index.html");
            exit();
        } elseif ($_POST['action'] === 'cancel') {
            $role = isset($_SESSION['rola']) && in_array($_SESSION['rola'], ['pracownik', 'manager'])
                ? $_SESSION['rola']
                : 'pracownik';
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

?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wylogowanie - System Urlopowy</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="card form-container fade-in">
            <div class="header-container">
                <h1>Wylogowanie</h1>
            </div>

            <div class="text-center" style="margin-bottom: 2rem;">
                <p>Czy na pewno chcesz się wylogować?</p>
            </div>

            <form method="POST" action="logout.php">
                <div class="form-group">
                    <button type="submit" name="action" value="confirm_logout" class="button button-danger">Tak, wyloguj</button>
                    <button type="submit" name="action" value="cancel" class="button">Anuluj</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>