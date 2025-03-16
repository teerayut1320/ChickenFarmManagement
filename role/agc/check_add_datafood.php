<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();   
    $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $foodname = $_POST['foodname'];
    }
    
    try {

        $check_df_name = $db->prepare("SELECT `df_name` FROM `data_food` WHERE `agc_id` = '$agc_id'");
        $check_df_name->execute();

        $check_name = array();
        while ($row = $check_df_name->fetch(PDO::FETCH_ASSOC)) {
            $name = $row["df_name"];
            array_push($check_name, $name);
        }
 
        if (!in_array($foodname, $check_name)) {
            // echo "1";
            $sql = $db->prepare("INSERT INTO `data_food`(`df_name`, `agc_id`) VALUES ('$foodname','$agc_id')");
            $sql->execute();

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
                header("refresh:1; url=data_food.php");
            } else {
                $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
                header("location: data_food.php");
            }

        }else {
            // echo "2";
            $_SESSION['warning'] = "มีข้อมูลอาหารนี้แล้ว";
            echo "<script>
                $(document).ready(function() {
                    Swal.fire({
                        title: 'ไม่สำเร็จ',
                        text: 'มีข้อมูลอาหารนี้แล้ว',
                        icon: 'warning',
                        timer: 5000,
                        showConfirmButton: false
                    });
                })
            </script>";
            header("refresh:1; url=data_food.php");
        }
        
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>