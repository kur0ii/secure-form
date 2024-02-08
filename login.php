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

                // Check connection with db
                if ($conn->connect_error) {
                    die("Connexion échouée: " . $conn->connect_error);
                }

                // Preparing SQL request
                $stmt = $conn->prepare("SELECT id, username, pwd FROM users WHERE username = ?");
                $stmt->bind_param("s", $username);

                if (!$stmt) {
                    echo "<p style='color:red;'>Error with the prepared statement, contact the developer.</p>";
                } else {
                    // Values entered by the users
                    $username = $_POST["username"];
                    $password = $_POST["password"];

                    $stmt->execute();
                    $stmt->store_result();

                    // Check if the user exists in the db
                    if ($stmt->num_rows > 0) {
                        $stmt->bind_result($id, $db_username, $db_hashpwd);
                        $stmt->fetch();

                        if (password_verify($password, $db_hashpwd)) {
                            echo "<p style='color:green;'>You are successfully logged in $db_username.</p>";
                        } else {
                            echo "<p style='color:red;'>Username or password is incorrect</p>"; // password incorrect
                        }
                    } else {
                        echo "<p style='color:red;'>Username or password is incorrect</p>"; // username inexistant
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
