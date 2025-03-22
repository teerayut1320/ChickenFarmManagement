<?php
  require_once '../../connect.php';
  session_start();  
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>รายละเอียดรายรับ-รายจ่าย ล็อตไก่</title>

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
            <?php include("../../topbar/tb_admin.php");?>
                <div class="container-fluid">
                    <?php
                        $agc_id = $_SESSION['agc_id'];
                        $lot_id = isset($_GET['lot_id']) ? $_GET['lot_id'] : '';

                        if (!$lot_id) {
                            echo '<div class="alert alert-danger">ไม่พบข้อมูลล็อตไก่</div>';
                            exit;
                        }

                        // ข้อมูลล็อตไก่
                        $lot_query = $db->prepare("
                            SELECT * FROM data_chick_detail 
                            WHERE dcd_id = :lot_id AND agc_id = :agc_id
                        ");
                        $lot_query->bindParam(':lot_id', $lot_id);
                        $lot_query->bindParam(':agc_id', $agc_id);
                        $lot_query->execute();
                        $lot_data = $lot_query->fetch(PDO::FETCH_ASSOC);

                        if (!$lot_data) {
                            echo '<div class="alert alert-danger">ไม่พบข้อมูลล็อตไก่</div>';
                            exit;
                        }
                    ?>

                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">รายละเอียดล็อตไก่ #รหัสล็อต <?= $lot_id ?></h1>
                        <a href="inex_lot.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> กลับ
                        </a>
                    </div>

                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                วันที่รับไก่</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $lot_data['dcd_date'] ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                จำนวนไก่</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $lot_data['dcd_quan'] ?> ตัว</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-feather fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                ราคาไก่ล็อตนี้</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($lot_data['dcd_price'], 2) ?> บาท</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                            // รายรับจากการขาย
                            $sales_query = $db->prepare("
                                SELECT SUM(sale_total) as total_income 
                                FROM data_sale 
                                WHERE dcd_id = :lot_id AND agc_id = :agc_id
                            ");
                            $sales_query->bindParam(':lot_id', $lot_id);
                            $sales_query->bindParam(':agc_id', $agc_id);
                            $sales_query->execute();
                            $sales_data = $sales_query->fetch(PDO::FETCH_ASSOC);
                            $total_income = $sales_data['total_income'] ?? 0;

                            // รายจ่ายค่าอาหาร
                            $feed_cost_query = $db->prepare("
                                SELECT SUM(feed_price) as feed_cost 
                                FROM data_feeding 
                                WHERE dcd_id = :lot_id AND agc_id = :agc_id
                            ");
                            $feed_cost_query->bindParam(':lot_id', $lot_id);
                            $feed_cost_query->bindParam(':agc_id', $agc_id);
                            $feed_cost_query->execute();
                            $feed_data = $feed_cost_query->fetch(PDO::FETCH_ASSOC);
                            $feed_cost = $feed_data['feed_cost'] ?? 0;

                            // รวมรายจ่ายทั้งหมด
                            $total_expense = $lot_data['dcd_price'] + $feed_cost;

                            // คำนวณกำไร/ขาดทุน
                            $profit = $total_income - $total_expense;
                            
                            // กำหนดสถานะและสี
                            $status = ($profit > 0) ? 'กำไร' : (($profit < 0) ? 'ขาดทุน' : 'เท่าทุน');
                            $status_color = ($profit > 0) ? 'success' : (($profit < 0) ? 'danger' : 'warning');
                        ?>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-<?= $status_color ?> shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-<?= $status_color ?> text-uppercase mb-1">
                                                <?= $status ?></div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format(abs($profit), 2) ?> บาท</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-<?= ($profit >= 0) ? 'chart-line' : 'chart-line-down' ?> fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- รายรับ -->
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">รายรับจากการขาย</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr align="center">
                                                    <th>วันที่</th>
                                                    <th>จำนวนไก่</th>
                                                    <th>น้ำหนักรวม</th>
                                                    <th>ราคาต่อกิโลกรัม</th>
                                                    <th>จำนวนเงิน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $sale_detail_query = $db->prepare("
                                                        SELECT * FROM data_sale 
                                                        WHERE dcd_id = :lot_id AND agc_id = :agc_id
                                                        ORDER BY sale_date DESC
                                                    ");
                                                    $sale_detail_query->bindParam(':lot_id', $lot_id);
                                                    $sale_detail_query->bindParam(':agc_id', $agc_id);
                                                    $sale_detail_query->execute();
                                                    $sale_details = $sale_detail_query->fetchAll(PDO::FETCH_ASSOC);

                                                    if (!$sale_details) {
                                                        echo "<tr><td colspan='5' class='text-center'>ไม่พบข้อมูลการขาย</td></tr>";
                                                    } else {
                                                        foreach($sale_details as $sale) {
                                                ?>
                                                <tr>
                                                    <td><?= $sale['sale_date'] ?></td>
                                                    <td align="center"><?= $sale['sale_quan'] ?> ตัว</td>
                                                    <td align="center"><?= $sale['sale_weigth'] ?> กก.</td>
                                                    <td align="right"><?= number_format($sale['sale_priceKg'], 2) ?></td>
                                                    <td align="right"><?= number_format($sale['sale_total'], 2) ?></td>
                                                </tr>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                                <tr class="bg-success text-white">
                                                    <td colspan="4" align="right"><strong>รวม</strong></td>
                                                    <td align="right"><strong><?= number_format($total_income, 2) ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รายจ่าย -->
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-danger">รายจ่าย</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr align="center">
                                                    <th>รายการ</th>
                                                    <th>จำนวนเงิน</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>ค่าไก่</td>
                                                    <td align="right"><?= number_format($lot_data['dcd_price'], 2) ?></td>
                                                </tr>
                                                <?php
                                                    $feed_detail_query = $db->prepare("
                                                        SELECT feed_name, SUM(feed_price) as total_price, SUM(feed_quan) as total_quan
                                                        FROM data_feeding 
                                                        WHERE dcd_id = :lot_id AND agc_id = :agc_id
                                                        GROUP BY feed_name
                                                    ");
                                                    $feed_detail_query->bindParam(':lot_id', $lot_id);
                                                    $feed_detail_query->bindParam(':agc_id', $agc_id);
                                                    $feed_detail_query->execute();
                                                    $feed_details = $feed_detail_query->fetchAll(PDO::FETCH_ASSOC);

                                                    if (!$feed_details) {
                                                        echo "<tr><td>ค่าอาหาร</td><td align='right'>0.00</td></tr>";
                                                    } else {
                                                        foreach($feed_details as $feed) {
                                                ?>
                                                <tr>
                                                    <td>ค่าอาหาร (<?= $feed['feed_name'] ?>) - <?= $feed['total_quan'] ?> กก.</td>
                                                    <td align="right"><?= number_format($feed['total_price'], 2) ?></td>
                                                </tr>
                                                <?php
                                                        }
                                                    }
                                                ?>
                                                <tr class="bg-danger text-white">
                                                    <td align="right"><strong>รวม</strong></td>
                                                    <td align="right"><strong><?= number_format($total_expense, 2) ?></strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- สรุป -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">สรุปผลกำไร/ขาดทุน</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td width="50%" align="right"><strong>รายรับทั้งหมด</strong></td>
                                            <td width="50%" align="right" class="text-success"><?= number_format($total_income, 2) ?> บาท</td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>รายจ่ายทั้งหมด</strong></td>
                                            <td align="right" class="text-danger"><?= number_format($total_expense, 2) ?> บาท</td>
                                        </tr>
                                        <tr>
                                            <td align="right"><strong>กำไร/ขาดทุน สุทธิ</strong></td>
                                            <td align="right" class="text-<?= $status_color ?>">
                                                <strong><?= number_format($profit, 2) ?> บาท (<?= $status ?>)</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->

            <?php include("../../footer/footer.php");?>
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