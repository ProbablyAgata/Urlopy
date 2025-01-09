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
$stmt = $conn->prepare("SELECT * FROM wnioski_urlopowe WHERE employee_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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
    <div class="header-container">
        <h1>Moje wnioski</h1>
        <a href="logout.php" class="button">Wyloguj się</a>
    </div>
    <table border="1">
        <thead>
            <tr>
                <th>Data rozpoczęcia</th>
                <th>Data zakończenia</th>
                <th>Powód</th>
                <th>Status</th>
                <th>Komentarz</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch leave applications for the current user
            $user_id = $_SESSION['user_id'];
            $sql = "SELECT * FROM wnioski_urlopowe WHERE employee_id = ? ORDER BY poczatek_urlopu DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['poczatek_urlopu']) . "</td>";
                echo "<td>" . htmlspecialchars($row['koniec_urlopu']) . "</td>";
                echo "<td>" . htmlspecialchars($row['powod']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "<td>" . htmlspecialchars($row['komentarz_kadra'] ?? '-') . "</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="button-container">
        <a href="create_leave_application.php" class="add-button">Dodaj nowy wniosek</a>
    </div>
    </div>

</body>

</html>