<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'manager') {
    header('Location: index.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'planowane_urlopy');
$result = $conn->query("SELECT wnioski_urlopowe.*, users.username 
                       FROM wnioski_urlopowe 
                       JOIN users ON wnioski_urlopowe.user_id = users.id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $comment = $_POST['comment'];

    $status = $action === 'approve' ? 'zatwierdzony' : 'odrzucony';
    $stmt = $conn->prepare("UPDATE wnioski_urlopowe SET status = ?, komentarz_managera = ? WHERE id = ?");
    $stmt->bind_param('ssi', $status, $comment, $request_id);
    $stmt->execute();
    header('Location: widok_manager.php');
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
    <table>
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
                <td><?= $row['username'] ?></td>
                <td><?= $row['poczatek_urlopu'] ?></td>
                <td><?= $row['koniec_urlopu'] ?></td>
                <td><?= $row['powod'] ?></td>
                <td><?= $row['status'] ?></td>
                <td>
                    <?php if ($row['status'] === 'oczekujacy'): ?>
                        <form method="POST">
                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                            <textarea name="comment" placeholder="Komentarz"></textarea>
                            <button type="submit" name="action" value="approve">Zatwierdź</button>
                            <button type="submit" name="action" value="reject">Odrzuć</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="logout.php">Wyloguj się</a>
</body>

</html>