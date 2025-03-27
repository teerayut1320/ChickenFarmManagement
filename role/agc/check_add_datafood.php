<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();   
    $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $id = $_SESSION['agc_id'];
        $foodname = $_POST['foodname'];
        $quantity = $_POST['quantity'];
        $price_per_kg = $_POST['price_per_kg'];

        try {
            if (!$foodname) {
                $_SESSION['error'] = 'กรุณากรอกชื่ออาหาร';
                header("location: add_datafood.php");
                return;
            }
            
            if (!$quantity || $quantity <= 0) {
                $_SESSION['error'] = 'กรุณากรอกปริมาณอาหารที่ถูกต้อง';
                header("location: add_datafood.php");
                return;
            }
            
            if (!$price_per_kg || $price_per_kg <= 0) {
                $_SESSION['error'] = 'กรุณากรอกราคาต่อกิโลกรัมที่ถูกต้อง';
                header("location: add_datafood.php");
                return;
            }

            $check_data = $db->prepare("INSERT INTO data_food(df_name, df_quantity, df_price_per_kg, agc_id) 
                                         VALUES(:foodname, :quantity, :price_per_kg, :id)");
            $check_data->bindParam(":foodname", $foodname);
            $check_data->bindParam(":quantity", $quantity);
            $check_data->bindParam(":price_per_kg", $price_per_kg);
            $check_data->bindParam(":id", $id);
            $check_data->execute();

            if ($check_data) {
                $_SESSION['success'] = "เพิ่มข้อมูลอาหารไก่สำเร็จ";
                header("location: data_food.php");
            } else {
                $_SESSION['error'] = "เพิ่มข้อมูลอาหารไก่ไม่สำเร็จ";
                header("location: add_datafood.php");
            }

        } catch(PDOException $e) {
            $_SESSION['error'] = $e->getMessage();
            header("location: add_datafood.php");
        }
    }
?>