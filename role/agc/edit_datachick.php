<?php
  require_once '../../connect.php';
  session_start();  

    if (isset($_REQUEST['edit_id'])) {
        $id = $_REQUEST['edit_id'];
        // echo "id = ".$id;
        $check_id = $db->prepare("SELECT * FROM `data_chick_detail` WHERE `dcd_id` = '$id'");
        $check_id->execute();
        $datachick = $check_id->fetch(PDO::FETCH_ASSOC);
        extract($datachick);
        // echo "agc_id = ".$agc_id;
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

    <!-- Page Wrapper -->
    <div id="wrapper">
    <?php include("../../sidebar/sb_agc.php");?> <!--  Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
            <?php include("../../topbar/tb_admin.php");?> <!-- Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">แก้ไขข้อมูลไก่</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="p-5">
                                        <form class="user" action="checkedit_datachick.php" method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">รหัสข้อมูลไก่</label>
                                                    <input type="text" class="form-control" name="dcd_id" style="border-radius: 3rem;" value="<?= $dcd_id?>" required readonly>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">วันที่รับเข้า</label>
                                                    <input type="text" class="form-control" name="dcd_date" style="border-radius: 3rem;" value="<?= $dcd_date?>" required readonly>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">จำนวน(ตัว)</label>
                                                    <input type="number" class="form-control"  name="dcd_quan" style="border-radius: 3rem;" value="<?= $dcd_quan?>" required >
                                                </div> 
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">ราคา(บาท)</label>
                                                    <input type="number" class="form-control"  name="dcd_price" style="border-radius: 3rem;" value="<?= $dcd_price?>" required >
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5"></div>
                                                <div class="col-md-3">
                                                    <a href="data_chick.php" class="btn btn-danger" style="border-radius: 3rem; font-size: 1rem;">ยกเลิก</a>
                                                    <button type="submit" class="btn btn-chick1" name="submit" style="border-radius: 3rem; font-size: 1rem;">แก้ไขข้อมูล</button>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

</body>

</html>