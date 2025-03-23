<?php
  require_once 'connect.php';
  session_start();
?>
<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการข้อมูลฟาร์มไก่ จังหวัดสุราษฎร์ธานี</title>
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
            background-image: url('https://images.unsplash.com/photo-1548550023-2bdb3c5beed7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }
        
        .login-container {
            background-color: rgba(255, 255, 255, 0.9);
            width: 400px;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            z-index: 2;
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
        }
        
        .logo {
            width: 80px;
            height: 80px;
            margin-bottom: 15px;
        }
        
        h1 {
            color: #00703c;
            margin-bottom: 20px;
            font-size: 24px;
            position: relative;
            padding-bottom: 10px;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(to right, #ffc107, #00703c);
            border-radius: 10px;
        }
        
        .sub-title {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 18px;
        }
        
        input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: none;
            border-radius: 50px;
            background-color: #f8f9fa;
            font-size: 16px;
            color: #333;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 112, 60, 0.3);
        }
        
        button {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(to right, #00703c, #45a049);
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        button:hover {
            background: linear-gradient(to right, #045c31, #3d8b40);
            box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }
        
        .footer {
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }
        
        .chicken-icon {
            position: absolute;
            width: 60px;
            height: 60px;
            z-index: 2;
            pointer-events: none;
        }
        
        .chicken-icon-1 {
            top: 20%;
            left: 20%;
            animation: float 6s ease-in-out infinite;
        }
        
        .chicken-icon-2 {
            top: 70%;
            right: 20%;
            animation: float 7s ease-in-out infinite;
        }
        
        @keyframes float {
            0% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-15px) rotate(5deg);
            }
            100% {
                transform: translateY(0px) rotate(0deg);
            }
        }
        
        @media (max-width: 500px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
            
            .chicken-icon {
                display: none;
            }
        }
    </style>
</head>
<body>
    <img src="https://cdn-icons-png.flaticon.com/512/7251/7251199.png" alt="Chicken" class="chicken-icon chicken-icon-1">
    <img src="https://cdn-icons-png.flaticon.com/512/6553/6553124.png" alt="Chicken" class="chicken-icon chicken-icon-2">
    
    <div class="login-container">
        <img src="https://cdn-icons-png.flaticon.com/512/2776/2776067.png" alt="Logo" class="logo">
        <h1>ระบบจัดการข้อมูลฟาร์มไก่</h1>
        <p class="sub-title">จังหวัดสุราษฎร์ธานี</p>
        
    <form action="check_login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user input-icon"></i>
                <input type="text" name="username" placeholder="ชื่อผู้ใช้งาน" required>
      </div>
            
            <div class="input-group">
                <i class="fas fa-lock input-icon"></i>
                <input type="password" name="password" placeholder="รหัสผ่าน" required>
      </div>

            <button type="submit" name="submit">เข้าสู่ระบบ</button>
        </form>
      
        <div class="footer">
            ระบบจัดการข้อมูลฟาร์มไก่ © <?php echo date('Y'); ?>
        </div>
  </div>
</body>
</html>