<?php
session_start();
$conn = new mysqli(
    'localhost',
    'root',
    '',
    'planowane_urlopy'
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $sql = '';
    // Dodanie debugowania

    error_log("Próba logowania dla użytkownika: " . $username);

    // Pobieranie danych użytkownika

    $stmt = $conn->prepare('SELECT id, password, role FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        error_log("Użytkownik znaleziony w bazie danych");
        error_log("Niezaszyfrowane hasło: " . $password);
        error_log("Zaszyfrowane hasło z bazy danych: " . $user['password']);
        error_log("Zaszyfrowane hasło z wejścia: " . password_hash($password, PASSWORD_DEFAULT));

        if (password_verify($password, $user['password'])) {
            error_log("Hasło zostało zweryfikowane poprawnie");
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['rola'] = $user['role'];

            // Przekierowanie na podstawie roli użytkownika

            if ($user['role'] === 'manager') {
                header('Location: manager_view.php');
            } else if ($user['role'] === 'pracownik') {
                header('Location: employee_view.php');
            }
            exit();
        } else {
            error_log("Weryfikacja hasła nie powiodła się");
            echo 'Nieprawidłowe hasło'; // Wiadomość o błędzie
        }
    } else {
        error_log("No user found with username: " . $username);
        echo 'Użytkownik nie istnieje'; // Wiadomość o błędzie
    }
}
