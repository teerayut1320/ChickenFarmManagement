<?php
  require_once 'connect.php';
  session_start();
?>
<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ลงชื่อเข้าใช้งานระบบ</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="wrapper">
    <form action="check_login.php" method="POST">
      <h2>ลงชื่อเข้าใช้งานระบบ</h2>
        <div class="input-field">
        <input name="username" type="text" required>
        <label>ชื้อผู้ใช้งาน</label>
      </div>
      <div class="input-field">
        <input name="password" type="password" required>
        <label>รหัสผ่าน
        </label>
      </div>

      <button name="submit" type="submit">เข้าสู้ระบบ</button>
      
    </form>
  </div>
</body>
</html>