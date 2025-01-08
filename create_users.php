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
            'role' => 'manager'
        ],
        [
            'username' => 'Jan.Sobieski',
            'password' => 'password456',
            'role' => 'pracownik'
        ],
        [
            'username' => 'Stanislaw.Lem',
            'password' => 'password789',
            'role' => 'pracownik'
        ],
        [
            'username' => 'Kamil.Stoch',
            'password' => 'password987',
            'role' => 'pracownik'
        ],
        [
            'username' => 'Remigiusz.Mroz',
            'password' => 'password654',
            'role' => 'manager'
        ],
        [
            'username' => 'Maciej.Musial',
            'password' => 'password321',
            'role' => 'pracownik'
        ],
        [
            'username' => 'Anita.Wlodarczyk',
            'password' => 'password123',
            'role' => 'pracownik'
        ]
    ];

    // Tworzenie zapytania SQL
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");

    // Wstawianie każdego użytkownika
    foreach ($users as $user) {
        // Haszowanie hasła
        $hashedPassword = password_hash($user['password'], PASSWORD_BCRYPT);

        // Wykonanie zapytania
        $stmt->execute([
            ':username' => $user['username'],
            ':password' => $hashedPassword,
            ':role' => $user['role']
        ]);

        echo "Dodano użytkownika: {$user['username']}\n";
    }

    echo "Wszystkie użytkowników zostały dodane pomyślnie!";
} catch (PDOException $e) {
    echo "Błąd: " . $e->getMessage();
}
