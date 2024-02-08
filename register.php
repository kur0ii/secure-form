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
                <h1>Register</h1>
            </div>
            <div class="input-box">
            <input type="text" id="username" name="username" minlength="5" placeholder="Enter username" required><br>
            </div>
            <div class="input-box">
                <!--Minimum eight characters, at least one uppercase letter, one lowercase letter, one number and one special character: -->
            <input type="password" id="password" name="password"  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" placeholder="Enter password" required><br>
            </div>
            <div class="input-box">
                <input type="password" id="confirm-password" name="confirm-password"  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$" placeholder="Confirm password" required><br><br>
            <div class="register">
                <p>You have already an account ?<a href="login.php">Login</a></p>
            </div>
            <?php
                require_once "config.php"; // Connection variables 

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["confirm-password"])) {
                    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

                    // Check connection with the db
                    if ($conn->connect_error) {
                        echo "<p style='color:red;'>Error connection failed, contact the developer.</p>";
                    } else {
                        // Check if the passwords are matching
                        if ($_POST["password"] !== $_POST["confirm-password"]) {
                            echo "<p style='color:red;'>Please make sure your passwords match.</p>";
                        } else {
                            // Preparing the SQL request
                            $stmt = $conn->prepare("INSERT INTO users (username, pwd, perm, creation) VALUES (?, ?, ?, ?)");

                            if (!$stmt) {
                                echo "<p style='color:red;'>Error with the prepared statement, contact the developer.</p>";
                            } else {
                                // hash password
                                $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

                                $username = $_POST["username"];
                                $perm = 0; // Client(0) Admin(1)
                                $creation = date('Y-m-d H:i:s'); // Current datetime

                                // Matching and execution
                                $stmt->bind_param("ssis", $username, $hashed_password, $perm, $creation);
                                $result = $stmt->execute();

                                // Check the result
                                if ($result) {
                                    echo "<p style='color:green;'>Congratulations, your account $username has been successfully created</p>";
                                } else {
                                    echo "<p style='color:red;'>This username is already taken.</p>";
                                }
                            }
                            $stmt->close();
                        }
                        $conn->close();                        
                    }
                }
            ?>
            <button type="reset" class="btn">Reset</button>
            <button type="submit" class="btn">Sign Up</button>
        </form>
    </body>
    </html>
