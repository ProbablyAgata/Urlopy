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
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Sprawdzenie czy nazwa użytkownika jest już zajęta
    $check = $conn->prepare('SELECT id FROM users WHERE username = ?');
    $check->bind_param('s', $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo 'Nazwa użytkownika jest już zajęta';
    } else {
        // Haszowanie hasła (zwiększenie bezpieczeństwa)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Dodanie nowego użytkownika
        $stmt = $conn->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $hashed_password, $role);

        if ($stmt->execute()) {
            header('Location: index.html');
            exit();
        } else {
            echo 'Błąd podczas rejestracji: ' . $conn->error;
        }
    }
}
