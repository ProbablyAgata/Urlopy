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
<html>

<head>
    <title>Wniosek urlopowy</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <div class="header-container">
        <h1>Wniosek urlopowy</h1>
        <a href="logout.php" class="button">Wyloguj się</a>
    </div>

    <div class="leave-application-section">
        <h2>Potwierdzenie wniosku urlopowego</h2>

        <form method="POST" action="submit_leave.php" onsubmit="return validateForm()" class="leave-form">
            <input type="hidden" name="employee_id" value="<?php echo $_SESSION['user_id']; ?>">

            <div class="form-group">
                <label>Kierownik:</label>
                <select name="manager_id" id="manager_id" required>
                    <option disabled selected value="">Wybierz kierownika</option>
                    <?php
                    require_once 'connect.php';

                    $query = "SELECT id, imie, nazwisko FROM users WHERE role = 'manager' ORDER BY nazwisko, imie";
                    logToFile("Executing query: " . $query);

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
                            logToFile("No managers found in the database");
                            echo "<option disabled value=''>Brak dostępnych kierowników</option>";
                        }
                    } catch (Exception $e) {
                        logToFile("Error executing query: " . $e->getMessage());
                        echo "<option disabled value=''>Błąd podczas pobierania listy kierowników</option>";
                    }

                    $conn->close();
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>Data rozpoczęcia:</label>
                <input type="date" name="start_date" required>
            </div>

            <div class="form-group">
                <label>Data zakończenia:</label>
                <input type="date" name="end_date" required>
            </div>

            <div class="form-group">
                <label>Powód:</label>
                <textarea name="reason" required></textarea>
            </div>
            <div class="form-group">
                <button type="submit" name="submit_leave" value="1">Wyślij</button>
                <button type="reset" class="clear-button">Wyczyść</button>
            </div>
        </form>
    </div>

    <script>
        function validateForm() {
            var startDate = document.getElementsByName('start_date')[0].value;
            var endDate = document.getElementsByName('end_date')[0].value;

            if (startDate > endDate) {
                alert('Data zakończenia nie może być wcześniejsza niż data rozpoczęcia!');
                return false;
            }
            return true;
        }
    </script>
</body>

</html>