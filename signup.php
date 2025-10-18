<?php
include 'config.php'; // make sure config.php defines $pdo

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "⚠️ Please fill all fields.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            // check if username already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $check->execute([$username]);

            if ($check->rowCount() > 0) {
                $error = "⚠️ Username already exists.";
            } else {
                // default role = customer
                $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
                if ($stmt->execute([$username, $passwordHash])) {
                    header("Location: login.php?signup=success");
                    exit();
                } else {
                    $error = "⚠️ Failed to create account. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Flower Management System - Sign Up</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #fbc2eb, #a6c1ee);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      width: 380px;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
      backdrop-filter: blur(10px);
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 26px;
      color: #6a0572;
      margin-bottom: 10px;
    }

    p {
      color: #555;
      font-size: 14px;
    }

    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ccc;
      outline: none;
      font-size: 15px;
      transition: border 0.3s, box-shadow 0.3s;
    }

    input:focus {
      border: 1px solid #b57edc;
      box-shadow: 0 0 6px rgba(181,126,220,0.5);
    }

    button {
      width: 100%;
      background: linear-gradient(45deg, #b57edc, #fbc2eb);
      color: white;
      padding: 12px;
      margin-top: 15px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 15px;
      transition: transform 0.2s, opacity 0.3s;
    }

    button:hover {
      transform: translateY(-2px);
      opacity: 0.9;
    }

    a {
      color: #6a0572;
      text-decoration: none;
      font-weight: bold;
    }

    a:hover {
      text-decoration: underline;
    }

    .alert {
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 14px;
      animation: fadeIn 0.5s ease-in-out;
    }

    .error {
      background: #ffe0e0;
      color: #8a1f2c;
      border: 1px solid #f5c6cb;
    }

    .footer {
      margin-top: 15px;
      font-size: 13px;
      color: #777;
    }
     /* Back to Home button */
     .home-btn {
      margin-top: 20px;
      display: inline-block;
      background: linear-gradient(45deg, #9b59b6, #ff6f91);
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 10px;
      font-weight: bold;
      transition: all 0.3s;
      box-shadow: 0 4px 10px rgba(155, 89, 182, 0.3);
    }

    .home-btn:hover {
      background: linear-gradient(45deg, #ff6f91, #9b59b6);
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(155, 89, 182, 0.4);
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h1>🌸 Create Your Account</h1>
    <p>Join our Flower Management System today!</p>

    <?php if (!empty($error)): ?>
      <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="signup.php" method="post">
      <input type="text" name="username" placeholder="Enter Username" required>
      <input type="password" name="password" placeholder="Enter Password" required>
      <button type="submit">Sign Up</button>
    </form>

    <div class="footer">
      Already have an account? <a href="login.php">Login here</a> <br>
        <!-- 🌷 Back to home button -->
    <a href="index.php" class="home-btn">🏠 Back to Home</a>
    </div>
  </div>
</body>
</html>
