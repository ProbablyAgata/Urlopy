<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'manager') {
    header('Location: index.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'planowane_urlopy');

$stmt = $conn->prepare("SELECT wnioski_urlopowe.*, users.imie, users.nazwisko 
                       FROM wnioski_urlopowe 
                       JOIN users ON wnioski_urlopowe.employee_id = users.id
                       WHERE wnioski_urlopowe.manager_id = ?
                       ORDER BY wnioski_urlopowe.id DESC");

if (!$stmt) {
    error_log("Błąd podczas przygotowania zapytania: " . $conn->error);
    die("Błąd podczas przygotowania zapytania");
}

$stmt->bind_param('i', $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $comment = $_POST['comment'];

    $status = $action === 'Decyzja' ? 'zatwierdzony' : 'odrzucony';
    $stmt = $conn->prepare("UPDATE wnioski_urlopowe SET status = ?, komentarz_kadra = ? WHERE id = ?");
    $stmt->bind_param('ssi', $status, $comment, $request_id);
    $stmt->execute();
    header('Location: manager_view.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Menedżera - System Urlopowy</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body>
    <nav class="top-nav">
        <a href="manager_view.php" class="home-link">
            <span class="material-icons">home</span>
            <span>Panel Menedżera</span>
        </a>
        <a href="logout.php" class="button button-danger">Wyloguj się</a>
    </nav>

    <div class="container">
        <div class="content-container">
            <h1>Wnioski urlopowe pracowników</h1>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Pracownik</th>
                            <th>Data rozpoczęcia</th>
                            <th>Data zakończenia</th>
                            <th>Powód</th>
                            <th>Status</th>
                            <th>Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['imie'] . ' ' . $row['nazwisko']) ?></td>
                                <td><?= htmlspecialchars($row['poczatek_urlopu']) ?></td>
                                <td><?= htmlspecialchars($row['koniec_urlopu']) ?></td>
                                <td><?= htmlspecialchars($row['powod']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $row['status'] ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'oczekujacy'): ?>
                                        <form method="POST" class="form-inline">
                                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                            <textarea name="comment" class="form-control" placeholder="Komentarz"></textarea>
                                            <div class="button-group">
                                                <button type="submit" name="action" value="approve" class="button button-success">Zatwierdź</button>
                                                <button type="submit" name="action" value="reject" class="button button-danger">Odrzuć</button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <form method="POST" action="edit_application.php" class="form-inline">
                                            <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                            <button type="submit" name="action" value="edit" class="button">Edytuj</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .form-inline {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-inline textarea {
            min-height: 60px;
        }

        .button-group {
            display: flex;
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</body>

</html>