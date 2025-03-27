<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    // $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $df_id = $_POST['df_id'];
        $df_name = $_POST['df_name'];
        $df_quantity = $_POST['df_quantity'];
        $df_price_per_kg = $_POST['df_price_per_kg'];
    }
    try {
        if (!$df_name) {
            $_SESSION['error'] = 'กรุณากรอกชื่ออาหาร';
            header("location: edit_datafood.php?edit_id=$df_id");
            return;
        }
        
        if (!$df_quantity || $df_quantity < 0) {
            $_SESSION['error'] = 'กรุณากรอกปริมาณอาหารที่ถูกต้อง';
            header("location: edit_datafood.php?edit_id=$df_id");
            return;
        }
        
        if (!$df_price_per_kg || $df_price_per_kg < 0) {
            $_SESSION['error'] = 'กรุณากรอกราคาต่อกิโลกรัมที่ถูกต้อง';
            header("location: edit_datafood.php?edit_id=$df_id");
            return;
        }

        $update_stmt = $db->prepare("UPDATE data_food SET 
                                     df_name = :df_name,
                                     df_quantity = :df_quantity,
                                     df_price_per_kg = :df_price_per_kg
                                     WHERE df_id = :df_id");
        $update_stmt->bindParam(':df_name', $df_name);
        $update_stmt->bindParam(':df_quantity', $df_quantity);
        $update_stmt->bindParam(':df_price_per_kg', $df_price_per_kg);
        $update_stmt->bindParam(':df_id', $df_id);
        $update_stmt->execute();

        $_SESSION['success'] = "แก้ไขข้อมูลสำเร็จ";
        header("location: data_food.php");

    } catch(PDOException $e) {
        $_SESSION['error'] = $e->getMessage();
        header("location: edit_datafood.php?edit_id=$df_id");
    }

?>