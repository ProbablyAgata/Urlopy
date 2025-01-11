<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'manager') {
    header('Lokalizacja: index.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'planowane_urlopy');
$result = $conn->query("SELECT wnioski_urlopowe.*, users.imie, users.nazwisko 
                       FROM wnioski_urlopowe 
                       JOIN users ON wnioski_urlopowe.employee_id = users.id
                       ORDER BY wnioski_urlopowe.id DESC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $comment = $_POST['comment'];

    $status = $action === 'Opinia' ? 'zatwierdzony' : 'odrzucony';
    $stmt = $conn->prepare("UPDATE wnioski_urlopowe SET status = ?, komentarz_kadra = ? WHERE id = ?");
    $stmt->bind_param('ssi', $status, $comment, $request_id);
    $stmt->execute();
    header('Lokalizacja: widok_manager.php');
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Menedżera</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Panel Menedżera</h1>
    <table class="manager-table">
        <tr>
            <th>Pracownik</th>
            <th>Data rozpoczęcia</th>
            <th>Data zakończenia</th>
            <th>Powód</th>
            <th>Status</th>
            <th>Akcja</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td data-label="Pracownik"><?= $row['imie'] . ' ' . $row['nazwisko'] ?></td>
                <td data-label="Data rozpoczęcia"><?= $row['poczatek_urlopu'] ?></td>
                <td data-label="Data zakończenia"><?= $row['koniec_urlopu'] ?></td>
                <td data-label="Powód"><?= $row['powod'] ?></td>
                <td data-label="Status" class="status-<?= $row['status'] ?>"><?= $row['status'] ?></td>
                <td data-label="Akcja">
                    <?php if ($row['status'] === 'oczekujacy'): ?>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <textarea name="comment" placeholder="Komentarz"></textarea>
                            <button type="submit" name="action" value="approve">Zatwierdź</button>
                            <button type="submit" name="action" value="reject">Odrzuć</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="edit_application.php">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="action" value="edit">Edytuj</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="logout.php" class="logout-button">Wyloguj się</a>
    <div class="container">
    </div>
</body>

</html>