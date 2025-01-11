<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

function logToFile($message)
{
    $logFile = 'logs/manager_selection.log';
    $logDir = dirname($logFile);

    if (!file_exists($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] " . $message . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nowy wniosek urlopowy - System Urlopowy</title>
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
            <h1>Nowy wniosek urlopowy</h1>

            <form method="POST" action="submit_leave.php" onsubmit="return validateForm()" class="leave-form">
                <input type="hidden" name="employee_id" value="<?php echo $_SESSION['user_id']; ?>">

                <div class="form-group">
                    <label for="manager_id">Kierownik:</label>
                    <select name="manager_id" id="manager_id" class="form-control" required>
                        <option disabled selected value="">Wybierz kierownika</option>
                        <?php
                        require_once 'connect.php';

                        $query = "SELECT id, imie, nazwisko FROM users WHERE role = 'manager' ORDER BY nazwisko, imie";
                        logToFile("Wykonanie zapytania: " . $query);

                        try {
                            $result = $conn->query($query);

                            if ($result && $result->num_rows > 0) {
                                logToFile("Found " . $result->num_rows . " managers");
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . htmlspecialchars($row['id'], ENT_QUOTES) . "'>"
                                        . htmlspecialchars($row['imie'] . " " . $row['nazwisko'], ENT_QUOTES)
                                        . "</option>";
                                }
                            } else {
                                logToFile("Brak kierowników w bazie danych");
                                echo "<option disabled value=''>Brak dostępnych kierowników</option>";
                            }
                        } catch (Exception $e) {
                            logToFile("Błąd podczas pobierania listy kierowników: " . $e->getMessage());
                            echo "<option disabled value=''>Błąd podczas pobierania listy kierowników</option>";
                        }

                        $conn->close();
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_date">Data rozpoczęcia:</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" placeholder="Wybierz datę rozpoczęcia" required>
                </div>

                <div class="form-group">
                    <label for="end_date">Data zakończenia:</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" placeholder="Wybierz datę zakończenia" required>
                </div>

                <div class="form-group">
                    <label for="reason">Powód:</label>
                    <textarea id="reason" name="reason" class="form-control" placeholder="Opisz powód urlopu" required></textarea>
                </div>

                <div class="actions-container">
                    <a href="employee_view.php" class="button button-danger">Anuluj</a>
                    <button type="submit" name="submit_leave" class="button button-success">Złóż wniosek</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            var startDate = new Date(document.getElementById('start_date').value);
            var endDate = new Date(document.getElementById('end_date').value);

            if (endDate < startDate) {
                alert('Data zakończenia nie może być wcześniejsza niż data rozpoczęcia!');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>