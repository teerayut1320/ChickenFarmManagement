<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    $agc_id = $_SESSION['agc_id'];

    try {
        
        if (isset($_POST['submit'])) {
            $date = $_POST['date'];
            $chick_lot = $_POST['chick_lot'];
            $quan = $_POST['quan'];
            $weigth = $_POST['weigth'];
            $priceKg = $_POST['priceKg'];
            $total = $weigth*$priceKg;

            // เช็คจำนวนไก่คงเหลือในล็อต
            $check_quan = $db->prepare("SELECT dcd_quan FROM data_chick_detail WHERE dcd_id = :lot_id AND agc_id = :agc_id");
            $check_quan->bindParam(':lot_id', $chick_lot);
            $check_quan->bindParam(':agc_id', $agc_id);
            $check_quan->execute();
            $current_quan = $check_quan->fetchColumn();

            // ตรวจสอบว่าจำนวนไก่ที่จะขายต้องไม่เกินจำนวนที่มีอยู่
            if ($quan > $current_quan) {
                $_SESSION['error'] = "ไม่สามารถขายไก่ได้ เนื่องจากจำนวนไก่ในล็อตมีไม่เพียงพอ";
                header("location: add_sale.php");
                exit();
            }

            // เริ่ม Transaction
            $db->beginTransaction();

            // เพิ่มข้อมูลการขาย
            $insert_sale = $db->prepare("INSERT INTO data_sale(agc_id, dcd_id, sale_date, sale_quan, sale_weigth, sale_priceKg, sale_total) 
                                       VALUES(:agc_id, :dcd_id, :sale_date, :sale_quan, :sale_weigth, :sale_priceKg, :sale_total)");
            $insert_sale->bindParam(':agc_id', $agc_id);
            $insert_sale->bindParam(':dcd_id', $chick_lot);
            $insert_sale->bindParam(':sale_date', $date);
            $insert_sale->bindParam(':sale_quan', $quan);
            $insert_sale->bindParam(':sale_weigth', $weigth);
            $insert_sale->bindParam(':sale_priceKg', $priceKg);
            $insert_sale->bindParam(':sale_total', $total);
            $insert_sale->execute();

            // อัพเดทจำนวนไก่คงเหลือในล็อต
            $new_quan = $current_quan - $quan;
            $update_quan = $db->prepare("UPDATE data_chick_detail 
                                       SET dcd_quan = :new_quan 
                                       WHERE dcd_id = :lot_id AND agc_id = :agc_id");
            $update_quan->bindParam(':new_quan', $new_quan);
            $update_quan->bindParam(':lot_id', $chick_lot);
            $update_quan->bindParam(':agc_id', $agc_id);
            $update_quan->execute();

            // Commit Transaction
            $db->commit();

            $_SESSION['success'] = "เพิ่มข้อมูลการขายสำเร็จ";
            echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'สำเร็จ',
                        text: 'เพิ่มข้อมูลเรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 5000,
                        showConfirmButton: false
                    });
                })
            </script>";
            header("refresh:1; url=data_sale.php");
        } else {
            $_SESSION['error'] = "เกิดข้อผิดพลาด";
            header("location: add_sale.php");
        }
        
    } catch(PDOException $e) {
        // หากเกิดข้อผิดพลาด ให้ Rollback
        $db->rollBack();
        $_SESSION['error'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
        header("location: add_sale.php");
    }
?>