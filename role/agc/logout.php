<?php
session_start();
header("Location: ../../index.php"); // กลับไปที่หน้า login 

session_destroy(); // ล้าง session ทั้งหมด
?>
