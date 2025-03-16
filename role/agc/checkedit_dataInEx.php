<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    // $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $date = $_POST['date'];
        $type = $_POST['type'];
        $name = $_POST['name'];
        $price = $_POST['price'];
    }
    try {
        $sql = $db->prepare("UPDATE `data_inex` SET `inex_date`='$date',`inex_type`='$type',`inex_name`='$name',`inex_price`='$price'
                            WHERE `inex_id`='$id'");
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
            header("refresh:1; url=data_inex.php");
        } else {
            $_SESSION['error'] = "แก้ไขข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location: data_inex.php");
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>