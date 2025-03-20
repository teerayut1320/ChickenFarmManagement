<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php

    require_once '../../connect.php';
    session_start();    
    

    
    try {
        
        if (isset($_POST['submit'])) {
            $date = $_POST['date'];
            $name = $_POST['name'];
            $quan = $_POST['quan'];
            $price = $_POST['price'];
            $agc_id = $_SESSION['agc_id'];
            $chick_lot = $_POST['chick_lot'];
        

            $sql = $db->prepare("INSERT INTO `data_feeding`(`feed_date`, `feed_name`, `feed_quan`, `feed_price`, `agc_id`, `dcd_id`) 
                                 VALUES (:date, :name, :quan, :price, :agc_id, :dcd_id)");
            
            $sql->bindParam(':date', $date);
            $sql->bindParam(':name', $name);
            $sql->bindParam(':quan', $quan);
            $sql->bindParam(':price', $price);
            $sql->bindParam(':agc_id', $agc_id);
            $sql->bindParam(':dcd_id', $chick_lot);
            
            $sql->execute();


            $sql2 = $db->prepare("INSERT INTO `data_inex`(`inex_date`, `inex_type`, `inex_name`, `inex_price`, `agc_id`) VALUES ('$date','รายจ่าย','ค่าอาหาร', $price, '$agc_id')");
            $sql2->execute();

        }

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
            header("refresh:1; url=feeding.php");
        } else {
            $_SESSION['error'] = "เพิ่มข้อมูลเรียบร้อยไม่สำเร็จ";
            header("location:feeding.php");
        }
        
    } catch(PDOException $e) {
        echo $e->getMessage();
    }
?>