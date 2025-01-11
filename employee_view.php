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
    <title>Panel Pracownika - System Urlopowy</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <nav class="top-nav">
        <a href="employee_view.php" class="home-link">
            <span class="material-icons">home</span>
            <span>Panel Pracownika</span>
        </a>
        <a href="logout.php" class="button button-danger">Wyloguj się</a>
    </nav>

    <div class="container">
        <div class="content-container">
            <h1>Moje wnioski urlopowe</h1>

            <div class="table-container">
                <table>
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
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['poczatek_urlopu']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['koniec_urlopu']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['powod']) . "</td>";
                            echo "<td><span class='status-badge status-" . $row['status'] . "'>" .
                                htmlspecialchars($row['status']) . "</span></td>";
                            echo "<td>" . htmlspecialchars($row['komentarz_kadra'] ?? '-') . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="actions-container">
                <a href="create_leave_application.php" class="button button-success">Nowy wniosek</a>
            </div>
        </div>
    </div>
</body>

</html>