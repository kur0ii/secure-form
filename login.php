<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>Demo secure-form</title>
</head>
<body>
    <form action="" method="POST">
        <div class="logo-container">
            <img src="./img/logo.png" alt="Logo" class="logo">
            <h1>Login</h1>
        </div>
        <div class="input-box">
            <input type="text" id="username" name="username" minlength="5" placeholder="Enter username" required><br>
        </div>
        <div class="input-box">
            <!--Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character: -->
            <input type="password" id="password" name="password" pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" placeholder="Enter password" required><br><br>
        </div>
        <div class="register">
            <p>Don't have an account ?<a href="register.php">Sign Up</a></p>
        </div>
        <?php
            require_once "config.php";

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {
                $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

                if ($conn->connect_error) {
                    error_log("Error connection failed: " . $conn->connect_error);
                    echo "<p style='color:red;'>An error occurred. Please try again later.</p>";
                } else {
                    $username = $_POST["username"];
                    $password = $_POST["password"];

                    $stmt = $conn->prepare("SELECT id, username, pwd, attempts, last_attempt, status_lock FROM users WHERE username = ?");
                    $stmt->bind_param("s", $username);

                    if (!$stmt) {
                        error_log("Error with the prepared statement: " . $conn->error);
                        echo "<p style='color:red;'>An error occurred. Please try again later.</p>";
                    } else {
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $user = $result->fetch_assoc();
                            if ($user['status_lock'] == 1) {
                                // Calculate the time difference
                                $current_time = time();
                                $last_attempt_time = strtotime($user['last_attempt']);
                                $time_difference = $current_time - $last_attempt_time;
                                
                                if ($time_difference < LOCK_DURATION) {
                                    echo "<p style='color:red;'>Your account is temporarily locked. Please try again later.</p>";
                                    exit; 
                                } else {
                                    // Free the account if time passed
                                    $stmt = $conn->prepare("UPDATE users SET attempts = 0, last_attempt = NULL, status_lock = 0 WHERE id = ?");
                                    $stmt->bind_param("i", $user['id']);
                                    $stmt->execute();
                                }
                            }
                            // Check password
                            if (password_verify($password, $user['pwd'])) {
                                echo "<p style='color:green;'>You are successfully logged in " . htmlspecialchars($user['username']) . ".</p>";
                                // Reset the number of failed login attempts
                                $stmt = $conn->prepare("UPDATE users SET attempts = 0 WHERE id = ?");
                                $stmt->bind_param("i", $user['id']);
                                $stmt->execute();
                            } else {
                                // Increment failed attempts
                                $attempts = $user['attempts'] + 1;
                                if ($attempts >= MAX_ATTEMPTS) {
                                    // Lock the account
                                    $stmt = $conn->prepare("UPDATE users SET attempts = ?, last_attempt = CURRENT_TIMESTAMP, status_lock = 1 WHERE id = ?");
                                    $stmt->bind_param("ii", $attempts, $user['id']);
                                    $stmt->execute();
                                    echo "<p style='color:red;'>Your account is temporarily locked. Please try again later.</p>";
                                } else {
                                    // Update attempts count and last_attempt time
                                    $stmt = $conn->prepare("UPDATE users SET attempts = ?, last_attempt = CURRENT_TIMESTAMP WHERE id = ?");
                                    $stmt->bind_param("ii", $attempts, $user['id']);
                                    $stmt->execute();
                                    echo "<p style='color:red;'>Username or password is incorrect. You have " . (MAX_ATTEMPTS - $attempts) . " attempt(s) left.</p>";
                                }
                            }
                        } else {
                            echo "<p style='color:red;'>Username or password is incorrect</p>";
                        }
                    }
                    $stmt->close();
                    $conn->close();
                }
            }
        ?>

        <button type="reset" class="btn">Reset</button>
        <button type="submit" class="btn">Login</button>
    </form>
</body>
</html>
