<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // already logged in → redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin.php');
    } else {
        header('Location: customer.php');
    }
    exit;
}
$error = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Login - Flower Management System</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #fcb6d8, #c084f5, #9b59b6);
      background-size: 400% 400%;
      animation: gradientMove 10s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      overflow: hidden;
    }

    @keyframes gradientMove {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 40px 35px;
      border-radius: 18px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      backdrop-filter: blur(12px);
      width: 380px;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      margin-bottom: 20px;
      font-size: 26px;
      color: #6a0dad;
      letter-spacing: 1px;
    }

    form {
      text-align: left;
    }

    label {
      display: block;
      margin: 10px 0 5px;
      font-weight: bold;
      font-size: 14px;
      color: #444;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 18px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 14px;
      transition: all 0.3s;
    }

    input:focus {
      border: 1px solid #a36df0;
      box-shadow: 0 0 8px rgba(163, 109, 240, 0.5);
      outline: none;
    }

    button {
      width: 100%;
      background: linear-gradient(45deg, #c084f5, #ff85c1);
      color: white;
      padding: 12px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      font-size: 15px;
      transition: transform 0.2s, opacity 0.3s, box-shadow 0.3s;
    }

    button:hover {
      transform: translateY(-2px);
      opacity: 0.95;
      box-shadow: 0 6px 15px rgba(155, 89, 182, 0.4);
    }

    .alert {
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
      font-size: 14px;
      animation: fadeIn 0.5s ease-in-out;
    }

    .error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    p {
      margin-top: 15px;
      font-size: 14px;
      color: #444;
    }

    p a {
      color: #9b59b6;
      font-weight: bold;
      text-decoration: none;
      transition: 0.3s;
    }

    p a:hover {
      color: #ff69b4;
      text-decoration: underline;
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
    <h2>🌸 Welcome Back</h2>

    <?php if ($error): ?>
      <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form action="auth.php" method="POST">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter Username" required>

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter Password" required>

      <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="signup.php">Sign up</a></p>

    <!-- 🌷 Back to home button -->
    <a href="index.php" class="home-btn">🏠 Back to Home</a>
  </div>
</body>
</html>
