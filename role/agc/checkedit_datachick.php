<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();
    $agc_id = $_SESSION['agc_id'];
    echo "agc_id = ".$agc_id."<br>";
    echo "--------------------------------- "."<br>" ;
    

    if (isset($_POST['submit'])) {
        $dcd_id = $_POST['dcd_id'];
        $dcd_date = $_POST['dcd_date'];
        $dcd_quanNew = $_POST['dcd_quan'];
        $dcd_priceNew = $_POST['dcd_price'];
        echo "dcd_id = ".$dcd_id."<br>";
        echo "dcd_date = ".$dcd_date."<br>" ;
        echo "dcd_quanNew = ".$dcd_quanNew."<br>" ;
        echo "dcd_priceNew = ".$dcd_priceNew."<br>" ;
        echo "--------------------------------- "."<br>" ;
    }
    try {


        $check_dcd = $db->prepare("SELECT  `dcd_quan` as \"dcd_quanOld\", `dcd_price` as \"dcd_priceOld\" FROM `data_chick_detail` WHERE `dcd_id` = '$dcd_id'");
        $check_dcd->execute();
        $dcd_data = $check_dcd->fetch(PDO::FETCH_ASSOC);
        extract($dcd_data);


        echo "dcd_quanOld = ".$dcd_quanOld."<br>" ;
        echo "dcd_priceOld = ".$dcd_priceOld."<br>" ;
        echo "--------------------------------- "."<br>" ;


        $check_dc = $db->prepare("SELECT * FROM `data_chick` WHERE `agc_id` = '$agc_id'");
        $check_dc->execute();
        $dc_data = $check_dc->fetch(PDO::FETCH_ASSOC);
        extract($dc_data);

        // <------------------------กรณีที่ dcd_quanNew มากกว่า---------------------->
        $Difference_quan = $dcd_quanNew - $dcd_quanOld ; 
        $dc_quanNEW = $Difference_quan + $dc_quan;
        // <------------------------กรณีที่ dcd_priceNew มากกว่า---------------------->
        $Difference_price = $dcd_priceNew - $dcd_priceOld ; 
        $dc_priceNEW = $Difference_price + $dc_price;
        
        if ($dcd_quanNew == $dcd_quanOld and $dcd_priceNew == $dcd_priceOld) {
            header("location: data_chick.php");
        }elseif($dcd_quanNew == $dcd_quanOld and $dcd_priceNew != $dcd_priceOld){
            echo "dcd_quanNew == dcd_quanOld แต่ dcd_priceNew != dcd_priceOld";
            if ($dcd_priceNew > $dcd_priceOld) {
                echo "dcd_priceNew > dcd_priceOld";
            }else {
                echo "dcd_priceNew < dcd_priceOld";
            }

        }elseif($dcd_quanNew != $dcd_quanOld and $dcd_priceNew == $dcd_priceOld){
            echo "dcd_quanNew != dcd_quanOld แต่ dcd_priceNew == dcd_priceOld";

            if ($dcd_quanNew > $dcd_quanOld) {
                echo "dcd_quanNew > dcd_quanOld";
            }else {
                echo "dcd_quanNew < dcd_quanOld";
            }

        }else{
            echo "dcd_quanNew != dcd_quanOld แต่ dcd_priceNew != dcd_priceOld";
            
            if ($dcd_priceNew > $dcd_priceOld) {
                echo "dcd_priceNew > dcd_priceOld";
            }else {
                echo "dcd_priceNew < dcd_priceOld";
            }

            if ($dcd_quanNew > $dcd_quanOld) {
                echo "dcd_quanNew > dcd_quanOld";
            }else {
                echo "dcd_quanNew < dcd_quanOld";
            }
        }
        

    
        // if ($dcd_quanNew > $dcd_quanOld and $dcd_priceNew > $dcd_priceOld) {
        //     echo "มากกว่าเก่าทั้งคู่";



        //     $Difference_quan = $dcd_quanNew - $dcd_quanOld ; 
        //     $dc_quanNEW = $Difference_quan + $dc_quan;
        //     $Difference_price = $dcd_priceNew - $dcd_priceOld ; 
        //     $dc_priceNEW = $Difference_price + $dc_price;

        //     $dc_data = $db->prepare("UPDATE `data_chick` SET `dc_quan`=$dc_quanNEW,`dc_price`=$dc_priceNEW WHERE `agc_id`='$agc_id'");
        //     $dc_data->execute();

        //     $dcd_data = $db->prepare("UPDATE `data_chick_detail` SET `dcd_quan`=$dcd_quanNew,`dcd_price`= $dcd_priceNew WHERE `dcd_id`='$dcd_id'");
        //     $dcd_data->execute();

        //     if ($dc_data && $dcd_data) {
        //     $_SESSION['success'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
        //         echo "<script>
        //             $(document).ready(function() {
        //                 Swal.fire({
        //                     title: 'สำเร็จ',
        //                     text: 'แก้ไขข้อมูลเรียบร้อยแล้ว',
        //                     icon: 'success',
        //                     timer: 5000,
        //                     showConfirmButton: false
        //                 });
        //             })
        //         </script>";
        //         header("refresh:1; url=data_chick.php");
        //     } else {
        //         $_SESSION['error'] = "แก้ไขข้อมูลเรียบร้อยไม่สำเร็จ";
        //         header("location: data_chick.php");
        //     }


        // }elseif ($dcd_quanNew > $dcd_quanOld or $dcd_priceNew > $dcd_priceOld) {
        //     echo "มากกว่าเก่า 1 อย่าง";


        // }elseif ($dcd_quanNew < $dcd_quanOld and $dcd_priceNew < $dcd_priceOld) {
        //     echo "น้อยกว่าเก่าทั้งคู่";
            


        // }else {
        //     echo "น้อยกว่าเก่า 1 อย่าง";


        // }

    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>