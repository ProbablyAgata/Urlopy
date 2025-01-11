<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['rola'] !== 'manager') {
    header('Location: index.html');
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'planowane_urlopy');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_changes'])) {
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];
        $comment = $_POST['comment'];

        $stmt = $conn->prepare("UPDATE wnioski_urlopowe SET status = ?, komentarz_kadra = ? WHERE id = ?");
        $stmt->bind_param('ssi', $status, $comment, $request_id);
        $stmt->execute();

        header('Location: manager_view.php');
        exit();
    }

    $request_id = $_POST['request_id'];
    $stmt = $conn->prepare("SELECT * FROM wnioski_urlopowe WHERE id = ? AND manager_id = ?");
    $stmt->bind_param('ii', $request_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header('Location: manager_view.php');
        exit();
    }

    $application = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj Wniosek - System Urlopowy</title>
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
            <h1>Edytuj Wniosek</h1>

            <form method="POST" class="leave-form">
                <input type="hidden" name="request_id" value="<?= $application['id'] ?>">

                <div class="form-group">
                    <label for="status">Status:</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="zatwierdzony" <?= $application['status'] === 'zatwierdzony' ? 'selected' : '' ?>>Zatwierdzony</option>
                        <option value="odrzucony" <?= $application['status'] === 'odrzucony' ? 'selected' : '' ?>>Odrzucony</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Komentarz:</label>
                    <textarea name="comment" id="comment" class="form-control"><?= htmlspecialchars($application['komentarz_kadra']) ?></textarea>
                </div>

                <div class="actions-container">
                    <a href="manager_view.php" class="button button-danger">Anuluj</a>
                    <button type="submit" name="save_changes" class="button button-success">Zapisz zmiany</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>