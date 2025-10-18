<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Flower Management System</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: linear-gradient(135deg, #ffdde1, #ee9ca7, #d8bfd8, #dda0dd);
      background-size: 400% 400%;
      animation: gradientShift 12s ease infinite;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .signin-container {
      background: rgba(255, 255, 255, 0.85);
      padding: 40px 35px;
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      backdrop-filter: blur(12px);
      text-align: center;
      width: 420px;
      animation: fadeIn 1s ease-in-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      margin-bottom: 10px;
      font-size: 28px;
      color: #7b2cbf;
      font-weight: bold;
    }

    p {
      color: #444;
      font-size: 16px;
      margin-bottom: 25px;
    }

    .btn {
      display: inline-block;
      padding: 14px 22px;
      border-radius: 10px;
      text-decoration: none;
      font-weight: bold;
      font-size: 15px;
      transition: transform 0.2s, box-shadow 0.3s;
      background: linear-gradient(45deg, #ff6f91, #c77dff);
      color: white;
      box-shadow: 0 4px 10px rgba(199, 125, 255, 0.3);
    }

    .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 14px rgba(199, 125, 255, 0.5);
    }

    .button-container {
      display: flex;
      gap: 15px;
      margin-top: 25px;
    }
  </style>
</head>
<body>
  <div class="signin-container">
    <h2>🌸 Flower Management System</h2>
    <p>Welcome 💐<br><strong>Please sign up or login</strong></p>
    <div class="button-container">
      <a href="signup.php" class="btn" style="flex:1; text-align:center;">Sign Up</a>
      <a href="login.php" class="btn" style="flex:1; text-align:center;">Login</a>
    </div>
  </div>
</body>
</html>
