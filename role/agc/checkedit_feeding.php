<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    // $agc_id = $_SESSION['agc_id'];
    

    if (isset($_POST['submit'])) {
        $feed_id = $_POST['id'];
        $feed_date = $_POST['date'];
        $feed_name = $_POST['name'];
        $feed_quan = $_POST['quan'];
        $feed_price = $_POST['price'];
        $chick_lot = $_POST['chick_lot'];
    }
    try {
        $sql = $db->prepare("UPDATE `data_feeding` SET `feed_date`=:feed_date, `feed_name`=:feed_name, `feed_quan`=:feed_quan, `feed_price`=:feed_price, `dcd_id`=:dcd_id
                            WHERE `feed_id`=:feed_id");
        $sql->bindParam(':feed_date', $feed_date);
        $sql->bindParam(':feed_name', $feed_name);
        $sql->bindParam(':feed_quan', $feed_quan);
        $sql->bindParam(':feed_price', $feed_price);
        $sql->bindParam(':dcd_id', $chick_lot);
        $sql->bindParam(':feed_id', $feed_id);
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
            header("refresh:1; url=feeding.php");
        } else {
            $_SESSION['error'] = "แก้ไขข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location: feeding.php");
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>