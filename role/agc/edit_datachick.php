<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php
  require_once '../../connect.php';
  session_start();  
  
  // ตรวจสอบว่ามี session agc_id หรือไม่
  if (!isset($_SESSION['agc_id'])) {
      header('Location: ../../index.php');
      exit();
  }

  $agc_id = $_SESSION['agc_id'];
  $edit_id = isset($_GET['edit_id']) ? $_GET['edit_id'] : '';

  // ดึงข้อมูลที่จะแก้ไข
  if (!empty($edit_id)) {
      $stmt = $db->prepare("SELECT * FROM data_chick_detail WHERE dcd_id = :dcd_id AND agc_id = :agc_id");
      $stmt->bindParam(':dcd_id', $edit_id);
      $stmt->bindParam(':agc_id', $agc_id);
      $stmt->execute();
      $data = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$data) {
          // ถ้าไม่พบข้อมูล หรือข้อมูลไม่ใช่ของผู้ใช้นี้
          header('Location: data_chick.php');
          exit();
      }
  } else {
      header('Location: data_chick.php');
      exit();
  }

  // เมื่อมีการส่งฟอร์มแก้ไข
  if (isset($_POST['submit'])) {
      $dcd_id = $_POST['dcd_id'];
      $dcd_date = $_POST['dcd_date'];
      $dcd_quanNew = $_POST['dcd_quan'];
      $dcd_priceNew = $_POST['dcd_price'];
      $dcd_quanOld = $_POST['dcd_quan_old'];
      $dcd_priceOld = $_POST['dcd_price_old'];

      try {
          // อัพเดตข้อมูล
          $update = $db->prepare("UPDATE data_chick_detail SET 
              dcd_date = :dcd_date, 
              dcd_quan = :dcd_quan, 
              dcd_price = :dcd_price 
              WHERE dcd_id = :dcd_id AND agc_id = :agc_id");
          $update->bindParam(':dcd_date', $dcd_date);
          $update->bindParam(':dcd_quan', $dcd_quanNew);
          $update->bindParam(':dcd_price', $dcd_priceNew);
          $update->bindParam(':dcd_id', $dcd_id);
          $update->bindParam(':agc_id', $agc_id);
          $update->execute();

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
            header("refresh:1; url=data_chick.php");
      } catch(PDOException $e) {
          echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "');</script>";
      }
  }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>แก้ไขข้อมูลไก่</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <div id="wrapper">
        <?php include("../../sidebar/sb_agc.php");?>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php 
                if (file_exists("../../topbar/tb_admin.php")) {
                    include("../../topbar/tb_admin.php");
                } else {
                    echo '<div class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                            <!-- Topbar content -->
                          </div>';
                }
                ?>
                
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center text-white">แก้ไขข้อมูลไก่</h3>
                        </div>
                        <div class="card-body">
                            <div class="container">
                                <form method="post" action="">
                                    <input type="hidden" name="dcd_id" value="<?= $data['dcd_id'] ?>">
                                    <input type="hidden" name="dcd_quan_old" value="<?= $data['dcd_quan'] ?>">
                                    <input type="hidden" name="dcd_price_old" value="<?= $data['dcd_price'] ?>">
                                    
                                    <div class="form-group">
                                        <label><strong>รหัสข้อมูลไก่</strong></label>
                                        <input type="text" class="form-control" style="border-radius: 3rem;" value="<?= $data['dcd_id'] ?>" readonly>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><strong>วันที่รับเข้า</strong></label>
                                        <input type="date" class="form-control"  style="border-radius: 3rem;" name="dcd_date" value="<?= $data['dcd_date'] ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><strong>จำนวน(ตัว)</strong></label>
                                        <input type="number" step="0.01" class="form-control"  style="border-radius: 3rem;" name="dcd_quan" value="<?= $data['dcd_quan'] ?>" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label><strong>ราคา(บาท)</strong></label>
                                        <input type="number" step="0.01" class="form-control"  style="border-radius: 3rem;"  name="dcd_price" value="<?= $data['dcd_price'] ?>" required>
                                    </div>
                                    
                                    <div class="text-center mt-4">
                                        <a href="data_chick.php" class="btn btn-danger" style="padding: 10px 30px; border-radius: 50px;">ยกเลิก</a>
                                        <button type="submit" name="submit" class="btn btn-success" style="padding: 10px 30px; border-radius: 50px;">แก้ไขข้อมูล</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php include("../../footer/footer.php");?>
        </div>
    </div>

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
</body>
</html>