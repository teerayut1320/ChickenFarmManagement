<?php
  require_once 'connect.php';
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลงชื่อเข้าใช้งานระบบ</title>
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
            padding: 0;
            margin: 0;
            background: #f0f2f5;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            height: 100vh;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("chicken.jpg");
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            z-index: -1;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .site-title {
            font-size: 42px;
            font-weight: 700;
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            margin-bottom: 40px;
            text-align: center;
            background: rgba(0,0,0,0.4);
            padding: 20px 40px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .site-title:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        }

        .wrapper {
            width: 420px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        .wrapper:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 30px rgba(0,0,0,0.3);
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
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 0 45px;
            font-size: 16px;
            color: #fff;
            transition: all 0.3s ease;
        }

        .input-field input:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 0 15px rgba(255,255,255,0.1);
        }

        .input-field input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .input-field i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.8);
            font-size: 20px;
            transition: all 0.3s ease;
        }

        .input-field input:focus ~ i {
            color: #fff;
        }

        button {
            width: 100%;
            height: 50px;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
            color: #333;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        button:hover {
            background: #fff;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .footer-text {
            text-align: center;
            margin-top: 25px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0,0,0,0.2);
        }

        @media (max-width: 480px) {
            .site-title {
                font-size: 32px;
                padding: 15px 25px;
            }

            .wrapper {
                width: 100%;
                margin: 0 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">

        <div class="wrapper">
            <h2>ลงชื่อเข้าใช้งานระบบ</h2>
            <form action="check_login.php" method="POST">
                <div class="input-field mb-5">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" placeholder="ชื่อผู้ใช้งาน" required>
                </div>
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="รหัสผ่าน" required>
                </div>
                <button type="submit" name="submit">
                    เข้าสู่ระบบ
                </button>
            </form>
            <div class="footer-text">
                ระบบจัดการข้อมูลฟาร์มไก่ © 2025
            </div>
        </div>
    </div>
</body>
</html>