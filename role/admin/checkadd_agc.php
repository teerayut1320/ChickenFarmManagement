<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    

    if (isset($_POST['submit'])) {
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
        $sql = $db->prepare("INSERT INTO `agriculturist`(`agc_name`, `agc_Fname`, `agc_phone`) VALUES 
                                                        ('$name','$fname','$phone')");
        $sql->execute();

        $check_id = $db->query("SELECT `agc_id` FROM `agriculturist` WHERE `agc_name`= '$name' AND `agc_Fname`= '$fname' AND `agc_phone`= '$phone'");
        $check_id->execute();
        $agc_id = $check_id->fetch(PDO::FETCH_ASSOC);
        extract($agc_id);

        $sql2 = $db->prepare("INSERT INTO `user_login`(`us_name`, `us_pass`, `us_role`, `agc_id`) VALUES 
                                                      ('$user','$password','2','$agc_id')");
        $sql2->execute();

        if ($sql && $sql2) {
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
            header("refresh:1; url=agriculturist.php");
        } else {
            $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location: agriculturist.php");
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>