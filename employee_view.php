<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'pracownik') {
    header('Location: index.html');
    exit();
}

$conn = new mysqli(
    'localhost',
    'root',
    '',
    'planowane_urlopy'
);

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM wnioski_urlopowe WHERE user_id = $user_id");
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Pracownika</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Twoje wnioski urlopowe</h1>
    <table>
        <tr>
            <th>Data rozpoczęcia</th>
            <th>Data zakończenia</th>
            <th>Powód</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['poczatek_urlopu'] ?></td>
                <td><?= $row['koniec_urlopu'] ?></td>
                <td><?= $row['powod'] ?></td>
                <td><?= $row['status'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="logout.php" class="button">Wyloguj się</a>
</body>

</html>