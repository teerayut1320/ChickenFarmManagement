<?php
    session_start(); 
    require_once '../../connect.php';

    if (isset($_REQUEST['edit_id'])) {
        $id = $_REQUEST['edit_id'];

        $check_sale = $db->prepare("SELECT * FROM `data_sale` WHERE `sale_id` = '$id'");
        $check_sale->execute();
        $datasale = $check_sale->fetch(PDO::FETCH_ASSOC);
        extract($datasale);   
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

    <title>แก้ไขข้อมูลการขาย</title>

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
                    <div class="card shadow mb-2">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">แก้ไขข้อมูลการขาย</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="p-5">
                                        <form class="user" action="checkedit_datasale.php" method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-2 mb-2"></div>
                                                <div class="col-md-3 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">รหัสการขาย</label>
                                                    <input type="text" class="form-control" name="id" style="border-radius: 3rem;" value="<?= $sale_id ?>" required readonly>
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">วันที่ทำรายการขาย</label>
                                                    <input type="date" class="form-control" name="date" style="border-radius: 3rem;" value="<?= $sale_date ?>" required >
                                                </div>
                                                <div class="col-md-3 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">รหัสล็อตไก่</label>
                                                    <select class="form-control" name="chick_lot" style="border-radius: 3rem;" required>
                                                        <option disabled>กรุณาเลือกล็อตไก่....</option>
                                                        <?php
                                                            $id = $_SESSION['agc_id'];
                                                            $check_lots = $db->prepare("SELECT `dcd_id`, `dcd_date`, `dcd_quan` FROM `data_chick_detail` WHERE `agc_id` = :id ORDER BY `dcd_id` DESC");
                                                            $check_lots->bindParam(':id', $id);
                                                            $check_lots->execute();
                                                            $chick_lots = $check_lots->fetchAll();
                                                            foreach($chick_lots as $lot) {
                                                        ?>
                                                            <option value="<?= $lot['dcd_id']; ?>" <?= ($lot['dcd_id'] == $datasale['dcd_id']) ? 'selected' : ''; ?>>
                                                                รหัสล็อต <?= $lot['dcd_id']; ?> (<?= $lot['dcd_quan']; ?> ตัว)
                                                            </option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">จำนวนรวม(ตัว)</label>
                                                    <input type="text" class="form-control" name="quan" style="border-radius: 3rem;" value="<?= $sale_quan ?>" required >
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">น้ำหนักรวม(กิโลกรัม)</label>
                                                    <input type="text" class="form-control"  name="weigth" style="border-radius: 3rem;" value="<?= $sale_weigth ?>" required >
                                                </div> 
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">ราคาต่อกิโลกรัม</label>
                                                    <input type="text" class="form-control"  name="priceKg" style="border-radius: 3rem;" value="<?= $sale_priceKg ?>" required >
                                                </div> 
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">ราคาสุทธิ</label>
                                                    <input type="text" class="form-control"  name="total" style="border-radius: 3rem;" value="<?= $sale_total ?>" required >
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5"></div>
                                                <div class="col-md-3">
                                                    <a href="data_sale.php" class="btn btn-danger" style="border-radius: 3rem; font-size: 1rem;">ยกเลิก</a>
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