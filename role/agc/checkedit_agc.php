<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    

    if (isset($_POST['submit'])) {
        $agc_id = $_POST['agc_id'];
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $fname = $_POST['fname'];
        $user = $_POST['user'];
        $password = $_POST['password'];
        // echo "name = ".$name  ;
        // echo "phone = ".$phone ;
        // echo "fname = ".$fname ;
        // echo "user = ".$user ;
        // echo "password = ".$password ;
    }
    try {
        $sql = $db->prepare("UPDATE `agriculturist` SET `agc_name`='$name',`agc_Fname`='$fname',`agc_phone`='$phone' 
                             WHERE `agc_id`='$agc_id'");
        $sql->execute();


        $sql2 = $db->prepare("UPDATE `user_login` SET `us_name`='$user',`us_pass`='$password'
                             WHERE `agc_id`='$agc_id'");
        $sql2->execute();

        if ($sql && $sql2) {
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
            header("refresh:1; url=data_agc.php");
        } else {
            $_SESSION['error'] = "แก้ไขข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location: data_agc.php");
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>