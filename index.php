<?php
  require_once 'connect.php';
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการฟาร์มไก่</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            min-height: 100vh;
            width: 100%;
            padding: 0 10px;
            background: #f0f2f5;
        }

        body::before {
            content: "";
            position: fixed;
            width: 100%;
            height: 100%;
            background: url("chicken.jpg"), #000;
            background-position: center;
            background-size: cover;
            filter: brightness(0.8);
            z-index: -1;
        }

        .container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }

        .wrapper {
            width: 420px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo img {
            width: 80px;
            height: 80px;
            object-fit: contain;
            margin-bottom: 15px;
            filter: brightness(0) invert(1);
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .input-field {
            position: relative;
            height: 55px;
            margin-bottom: 25px;
        }

        .input-field input {
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.15);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 0 45px;
            font-size: 16px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .input-field i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 18px;
        }

        .input-field label {
            position: absolute;
            top: 50%;
            left: 45px;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .input-field input:focus ~ label,
        .input-field input:valid ~ label {
            top: 0;
            left: 15px;
            font-size: 12px;
            padding: 0 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }

        button {
            width: 100%;
            height: 50px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 10px;
            color: #333;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        button:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="wrapper">
            <h2>ลงชื่อเข้าใช้งานระบบ</h2>
            <form action="check_login.php" method="POST">
                <div class="input-field">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" required>
                    <label>ชื่อผู้ใช้งาน</label>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" required>
                    <label>รหัสผ่าน</label>
                </div>
                <button type="submit" name="submit">
                    เข้าสู่ระบบ
                </button>
            </form>
            <div class="footer-text">
                ระบบจัดการข้อมูลฟาร์มไก่ © 2024
            </div>
        </div>
    </div>
</body>
</html>