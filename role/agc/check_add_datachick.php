<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    

    if (isset($_POST['submit'])) {
        $date = $_POST['date'];
        $quan = $_POST['quan'];
        $price = $_POST['price'];
        $agc_id = $_SESSION['agc_id'];
    }
    try {

        $check_agc_id = $db->prepare("SELECT `agc_id` FROM `data_chick`");
        $check_agc_id->execute();

        $check_id = array();
        while ($row = $check_agc_id->fetch(PDO::FETCH_ASSOC)) {
            $id = $row["agc_id"];
            array_push($check_id, $id);
        }

        if (!in_array($id,$check_id)) {


            $sql = $db->prepare("INSERT INTO `data_chick_detail`(`dcd_date`, `dcd_quan`, `dcd_price`, `agc_id`) VALUES ('$date',$quan, $price, '$agc_id')");
            $sql->execute();

            $sql2 = $db->prepare("INSERT INTO `data_chick`(`dc_quan`, `dc_price`, `agc_id`) VALUES ($quan, $price, '$agc_id')");
            $sql2->execute();

            if ($sql) {
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
                header("refresh:1; url=data_chick.php");
            } else {
                $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
                header("location: data_chick.php");
            }
        }else {

            $sql = $db->prepare("INSERT INTO `data_chick_detail`(`dcd_date`, `dcd_quan`, `dcd_price`, `agc_id`) VALUES ('$date',$quan, $price, '$agc_id')");
            $sql->execute();

            $check_data = $db->prepare("SELECT * FROM `data_chick` WHERE `agc_id`= '$agc_id'");
            $check_data->execute();
            $dcd_data = $check_data->fetch(PDO::FETCH_ASSOC);
            extract($dcd_data);

            $quanNew = $dc_quan + $quan;
            $priceNew = $dc_price + $price;

            

            $sql2 = $db->prepare("UPDATE `data_chick` SET `dc_quan`=$quanNew , `dc_price`= $priceNew WHERE `agc_id`='$agc_id'");
            $sql2->execute();

            
            if ($sql) {
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
                header("refresh:1; url=data_chick.php");
            } else {
                $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
                header("location: data_chick.php");
            }
        }
    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>