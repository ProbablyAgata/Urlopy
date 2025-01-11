<?php
// Parametry połączenia z bazą danych
$host = 'localhost';
$dbname = 'planowane_urlopy';
$username = 'root';
$password = '';

try {
    // Tworzenie połączenia z bazą danych
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Przykładowe dane użytkowników
    $users = [
        [
            'username' => 'Agata.Ochocinska',
            'password' => 'password123',
            'role' => 'manager',
            'imie' => 'Agata',
            'nazwisko' => 'Ochocinska'
        ],
        [
            'username' => 'Jan.Sobieski',
            'password' => 'password456',
            'role' => 'pracownik',
            'imie' => 'Jan',
            'nazwisko' => 'Sobieski'
        ],

        [
            'username' => 'Kamil.Stoch',
            'password' => 'password987',
            'role' => 'pracownik',
            'imie' => 'Kamil',
            'nazwisko' => 'Stoch'
        ],
        [
            'username' => 'Remigiusz.Mroz',
            'password' => 'password654',
            'role' => 'manager',
            'imie' => 'Remigiusz',
            'nazwisko' => 'Mroz'
        ],
    ];

    // Tworzenie zapytania SQL
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, imie, nazwisko) VALUES (:username, :password, :role, :imie, :nazwisko)");

    // Wstawianie każdego użytkownika
    foreach ($users as $user) {
        // Haszowanie hasła
        $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);

        // Wykonanie zapytania
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => $hashedPassword,
            ':role' => $user['role'],
            ':imie' => $user['imie'],
            ':nazwisko' => $user['nazwisko']
        ]);

        echo "Dodano użytkownika: {$user['username']}\n";
    }

    echo "Wszystkie użytkowników zostały dodane pomyślnie!";
} catch (PDOException $e) {
    echo "Błąd: " . $e->getMessage();
}
