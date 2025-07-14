<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "gourmet_delights";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST["username"];
    $email = $_POST["email"];
    $pass = password_hash($_POST["password"], PASSWORD_BCRYPT);
    $cnic = $_POST["cnic"];
    $phone = $_POST["phone"];
    $gender = $_POST["gender"];
    $address = $_POST["address"];

    $imageName = $_FILES["image"]["name"];
    $imageTmp = $_FILES["image"]["tmp_name"];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($imageName);

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (move_uploaded_file($imageTmp, $targetFile)) {
        $sql = "INSERT INTO users (username, email, password, cnic, phone, gender, address, image, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $uname, $email, $pass, $cnic, $phone, $gender, $address, $targetFile);

        if ($stmt->execute()) {
            $msg = "<p style='color:green;text-align:center;'>🎉 Signup successful! Welcome, $uname.</p>";
        } else {
            $msg = "<p style='color:red;text-align:center;'>❌ Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $msg = "<p style='color:red;text-align:center;'>❌ Image upload failed!</p>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Signup - Gourmet Delights</title>

  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", sans-serif;
      background: linear-gradient(to right, #ffe8cc, #ffcc80);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .signup-container {
      background: #fff;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      width: 95%;
      max-width: 500px;
      animation: fadeIn 1s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h2 {
      color: #d35400;
      text-align: center;
      margin-bottom: 10px;
    }

    p {
      font-size: 0.9rem;
      color: #555;
      text-align: center;
    }

    form {
      display: flex;
      flex-direction: column;
      gap: 15px;
      margin-top: 20px;
    }

    input, textarea, select {
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }

    .gender {
      display: flex;
      justify-content: space-around;
      font-size: 0.9rem;
    }

    .gender label {
      display: flex;
      align-items: center;
      gap: 5px;
    }

    button {
      padding: 12px;
      background-color: #e67e22;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #d35400;
    }

    .footer-link {
      margin-top: 15px;
      font-size: 0.85rem;
      text-align: center;
    }

    .footer-link a {
      color: #e67e22;
      text-decoration: none;
    }

    .footer-link a:hover {
      text-decoration: underline;
    }

    .message {
      font-weight: bold;
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="signup-container">
    <h2>Join Gourmet Delights 👨‍🍳</h2>
    <p>Create your account to unlock delicious experiences</p>
    <?= $msg ?>

    <form method="POST" enctype="multipart/form-data">
      <input type="text" name="username" placeholder="Username" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="text" name="cnic" placeholder="CNIC" required>
      <input type="tel" name="phone" placeholder="Phone Number" required>

      <div class="gender">
        <label><input type="radio" name="gender" value="male" required /> Male</label>
        <label><input type="radio" name="gender" value="female" required /> Female</label>
      </div>

      <textarea name="address" placeholder="Address" rows="3" required></textarea>
      <input type="file" name="image" accept="image/*" required>

      <button type="submit">Sign Up</button>
    </form>

    <div class="footer-link">
      <p>Already have an account? <a href="login.html">Login here</a></p>
    </div>
  </div>
</body>
</html>
