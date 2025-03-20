<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    // $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $date = $_POST['date'];
        $chick_lot = $_POST['chick_lot'];
        $quan = $_POST['quan'];
        $weigth = $_POST['weigth'];
        $priceKg = $_POST['priceKg'];
        $total = $weigth*$priceKg;
    }
    try {
        $sql = $db->prepare("UPDATE `data_sale` SET 
            `sale_date` = :date,
            `dcd_id` = :dcd_id,
            `sale_quan` = :quan,
            `sale_weigth` = :weigth,
            `sale_priceKg` = :priceKg,
            `sale_total` = :total
            WHERE `sale_id` = :id");
        
        $sql->bindParam(':date', $date);
        $sql->bindParam(':dcd_id', $chick_lot);
        $sql->bindParam(':quan', $quan);
        $sql->bindParam(':weigth', $weigth);
        $sql->bindParam(':priceKg', $priceKg);
        $sql->bindParam(':total', $total);
        $sql->bindParam(':id', $id);
        
        $sql->execute();


        if ($sql) {
            $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
            echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'สำเร็จ',
                        text: 'แก้ไขข้อมูลเรียบร้อยแล้ว',
                        icon: 'success',
                        timer: 5000,
                        showConfirmButton: false
                    });
                })
            </script>";
            header("refresh:1; url=data_sale.php");
        } else {
            $_SESSION['error'] = "แก้ไขข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location: data_sale.php");
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>