<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    $agc_id = $_SESSION['agc_id'];

    try {
        
        if (isset($_POST['submit'])) {

            $date = $_POST['date'];
            $quan = $_POST['quan'];
            $weigth = $_POST['weigth'];
            $priceKg = $_POST['priceKg'];
            $total = $weigth*$priceKg;
        

            $data_sale = $db->prepare("INSERT INTO `data_sale`( `sale_date`, `sale_quan`, `sale_weigth`, `sale_priceKg`, `sale_total`, `agc_id`) 
                                 VALUES ('$date',$quan,$weigth, $priceKg, $total ,'$agc_id')");
            $data_sale->execute();

            $data_chick = $db->prepare("SELECT `dc_quan` FROM `data_chick` WHERE `agc_id`= '$agc_id'");
            $data_chick->execute();
            $dc_data = $data_chick->fetch(PDO::FETCH_ASSOC);
            extract($dc_data);

            $dc_quanNew = $dc_quan - $quan;

            $data_chick1 = $db->prepare("UPDATE `data_chick` SET `dc_quan`='$dc_quanNew' WHERE `agc_id`='$agc_id'");
            $data_chick1->execute();

            $data_inex = $db->prepare("INSERT INTO `data_inex`(`inex_date`, `inex_type`, `inex_name`, `inex_price`, `agc_id`) VALUES ('$date','รายรับ','ค่าขายไก่', $total, '$agc_id')");
            $data_inex->execute();

            

        }

        if ($data_sale && $data_chick && $data_chick1 && $data_inex) {
            $_SESSION['success'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
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
            $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location:data_sale.php");
        }
        
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>