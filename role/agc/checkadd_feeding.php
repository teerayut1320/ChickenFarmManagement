<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    session_start();
    require_once '../../connect.php';

    if (isset($_POST['submit'])) {
        $date = $_POST['date'];
        $chick_lot = $_POST['chick_lot'];
        $name = $_POST['name'];
        $quan = $_POST['quan'];
        $price = $_POST['price']; // ราคาที่คำนวณจาก JavaScript
        $id = $_SESSION['agc_id'];

        // ตรวจสอบข้อมูลว่าถูกต้องหรือไม่
        if (empty($date) || empty($name) || empty($quan) || $quan <= 0) {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน';
            header("location: add_feeding.php");
            exit();
        }

        // ตรวจสอบจำนวนอาหารที่มีอยู่
        $check_food = $db->prepare("SELECT `df_quantity` FROM `data_food` WHERE `df_name` = ? AND `agc_id` = ?");
        $check_food->execute([$name, $id]);
        $food_data = $check_food->fetch(PDO::FETCH_ASSOC);
        
        if (!$food_data) {
            $_SESSION['error'] = 'ไม่พบข้อมูลอาหาร';
            header("location: add_feeding.php");
            exit();
        }
        
        $available_quantity = $food_data['df_quantity'];
        
        if ($quan > $available_quantity) {
            $_SESSION['error'] = 'ไม่สามารถให้อาหารได้ เนื่องจากจำนวนที่ให้เกินกว่าจำนวนที่มีอยู่';
            header("location: add_feeding.php");
            exit();
        }

        // เริ่ม transaction
        $db->beginTransaction();

        // บันทึกข้อมูลการให้อาหาร
        $insert_sql = "INSERT INTO data_feeding(feed_date, feed_name, feed_quan, feed_price, dcd_id, agc_id) 
                      VALUES(?, ?, ?, ?, ?, ?)";
        $insert_stmt = $db->prepare($insert_sql);
        $insert_result = $insert_stmt->execute([$date, $name, $quan, $price, $chick_lot, $id]);
        
        if (!$insert_result) {
            $db->rollBack();
            $_SESSION['error'] = 'ไม่สามารถบันทึกข้อมูลการให้อาหารได้';
            header("location: add_feeding.php");
            exit();
        }
        
        // อัพเดตจำนวนอาหารคงเหลือ
        $new_quantity = $available_quantity - $quan;
        $update_sql = "UPDATE `data_food` SET `df_quantity` = ? WHERE `df_name` = ? AND `agc_id` = ?";
        $update_stmt = $db->prepare($update_sql);
        $update_result = $update_stmt->execute([$new_quantity, $name, $id]);
        
        if (!$update_result) {
            $db->rollBack();
            $_SESSION['error'] = 'ไม่สามารถอัพเดตจำนวนอาหารคงเหลือได้';
            header("location: add_feeding.php");
            exit();
        }
        
        // ยืนยัน transaction
        $db->commit();
        
        $_SESSION['success'] = "เพิ่มข้อมูลสำเร็จ";
        header("location: feeding.php");
        exit();
    } else {
        $_SESSION['error'] = 'ไม่ได้รับข้อมูลจากฟอร์ม';
        header("location: add_feeding.php");
        exit();
    }
?>