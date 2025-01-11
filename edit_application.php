<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'manager') {
    header('Location: index.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'planowane_urlopy');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
        // Obsługa zapisu zmian
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];
        $comment = $_POST['comment'];

        $stmt = $conn->prepare("UPDATE wnioski_urlopowe SET status = ?, komentarz_kadra = ? WHERE id = ?");
        $stmt->bind_param('ssi', $status, $comment, $request_id);
        $stmt->execute();

        header('Location: manager_view.php');
        exit();
    }

    // Pobieranie szczegółów wniosku
    $request_id = $_POST['request_id'];
    $stmt = $conn->prepare("SELECT * FROM wnioski_urlopowe WHERE id = ?");
    $stmt->bind_param('i', $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $application = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <title>Edytuj Wniosek</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Edytuj Wniosek</h1>

        <form method="POST">
            <input type="hidden" name="request_id" value="<?= $application['id'] ?>">

            <div class="form-group">
                <label>Status:</label>
                <select name="status" required>
                    <option value="zatwierdzony" <?= $application['status'] === 'zatwierdzony' ? 'selected' : '' ?>>Zatwierdzony</option>
                    <option value="odrzucony" <?= $application['status'] === 'odrzucony' ? 'selected' : '' ?>>Odrzucony</option>
                </select>
            </div>

            <div class="form-group">
                <label>Komentarz:</label>
                <textarea name="comment"><?= htmlspecialchars($application['komentarz_kadra']) ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit" name="save_changes">Zapisz zmiany</button>
                <a href="manager_view.php" class="button">Anuluj</a>
            </div>
        </form>
    </div>
</body>

</html>