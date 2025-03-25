<?php
  require_once '../../connect.php';
  session_start();
  
  // ตรวจสอบว่ามี session agc_id หรือไม่
  if (!isset($_SESSION['agc_id'])) {
      // ถ้าไม่มี session ให้ redirect ไปหน้า login
      header('Location: ../../index.php');
      exit();
  }

  $id = $_SESSION['agc_id'];

  // Query to get lots that are not sold out
  $query = $db->prepare("
    SELECT * FROM `data_chick_detail`
    WHERE `agc_id` = :agc_id AND `dcd_quan` > 0
  ");
  $query->bindParam(':agc_id', $id, PDO::PARAM_INT);
  $query->execute();
  $chick_details = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ข้อมูลไก่</title>

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
        <?php include("../../sidebar/sb_agc.php");?> <!--  Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
            <?php include("../../topbar/tb_admin.php");?> <!-- Topbar -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-lg font-weight-bold text-primary text-uppercase mb-1">
                                                จำนวนไก่ทั้งหมด</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php
                                                $id = $_SESSION['agc_id'];
                                                $check_data = $db->prepare("SELECT SUM(dcd_quan) as total_quan FROM data_chick_detail WHERE agc_id = :agc_id");
                                                $check_data->bindParam(':agc_id', $id);
                                                $check_data->execute();
                                                $result = $check_data->fetch(PDO::FETCH_ASSOC);
                                                echo number_format($result['total_quan'] ?? 0, 0);
                                                ?>
                                                ตัว</div>
                                        </div>
                                        <div class="col-auto">
                                            <!-- <i class="fas fa-calendar fa-2x text-gray-300"></i> -->
                                            <i class="fas fa-kiwi-bird fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-lg font-weight-bold text-success text-uppercase mb-1">
                                                จำนวนเงินที่ซื้อไก่</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php
                                                $id = $_SESSION['agc_id'];
                                                $check_data = $db->prepare("SELECT SUM(dcd_price) as total_price FROM data_chick_detail WHERE agc_id = :agc_id");
                                                $check_data->bindParam(':agc_id', $id);
                                                $check_data->execute();
                                                $result = $check_data->fetch(PDO::FETCH_ASSOC);
                                                echo number_format($result['total_price'] ?? 0, 2);
                                                ?>
                                                บาท</div>
                                        </div>
                                        <div class="col-auto">

                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h3 class="m-0 font-weight-bold text-center">ข้อมูลไก่</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr align="center">
                                                    <th>รหัสล็อตไก่</th>
                                                    <th>วันที่รับเข้า</th>
                                                    <th>จำนวน (ตัว)</th>
                                                    <!-- <th>ราคา (บาท)</th> -->
                                                    <!-- <th>เกษตรกร</th> -->
                                                    <th></th>  
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if (!$chick_details) {
                                                        echo "<p><td colspan='6' class='text-center'>ไม่พบข้อมูล</td></p>";
                                                    } else {
                                                        foreach($chick_details as $data_chick)  {
                                                ?>
                                                <tr align="center">
                                                    <td><?= htmlspecialchars($data_chick['dcd_id']) ?></td>
                                                    <td><?= htmlspecialchars($data_chick['dcd_date']) ?></td>
                                                    <td><?= htmlspecialchars($data_chick['dcd_quan']) ?></td>
                                                    <!-- <td><?= $data_chick['dcd_price'];?></td> -->
                                                    <!-- <td><?= $agc_name;?></td> -->
                                                    <td><a href="edit_datachick.php?edit_id=<?= htmlspecialchars($data_chick['dcd_id']) ?>" class="btn btn-warning " style = "border-radius: 3rem; font-size: .9rem;">แก้ไขข้อมูลไก่</a></td>
                                                </tr>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                            </tbody>
                                            
                                        </table>
                                        <!-- <a href="add_datachick.php" class="btn btn-chick1">แก้ไขข้อมูลไก่ทั้งหมด</a> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="add_datachick.php" class="btn btn-chick1">เพิ่มข้อมูลไก่</a>
                    <!-- <buttona type="button" class="btn btn-chick1" style="border-redies" >เพิ่มข้อมูลไก่</buttona> -->
                    
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include("../../footer/footer.php");?> <!-- footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

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
    
    <script>
        $.extend(true, $.fn.dataTable.defaults, {
            "language": {
                    "sProcessing": "กำลังดำเนินการ...",
                    "sLengthMenu": "แสดง _MENU_ รายการ",
                    "sZeroRecords": "ไม่พบข้อมูล",
                    "sInfo": "แสดงรายการ _START_ ถึง _END_ จาก _TOTAL_ รายการ",
                    "sInfoEmpty": "แสดงรายการ 0 ถึง 0 จาก 0 รายการ",
                    "sInfoFiltered": "(กรองข้อมูล _MAX_ ทุกรายการ)",
                    "sInfoPostFix": "",
                    "sSearch": "ค้นหา:",
                    "sUrl": "",
                    "oPaginate": {
                                    "sFirst": "เริ่มต้น",
                                    "sPrevious": "ก่อนหน้า",
                                    "sNext": "ถัดไป",
                                    "sLast": "สุดท้าย"
                    }
            }
        });
        $('.table').DataTable();
    </script>

</body>

</html>