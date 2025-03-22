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

    <title>รายรับ-รายจ่าย รายล็อต</title>

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
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">สรุปยอดรายรับ-รายจ่าย/กำไร รายล็อต </h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr align="center">
                                            <th>รหัสล็อตไก่</th>
                                            <th>รายรับจากการขาย (บาท)</th>
                                            <th>รายจ่ายค่าไก่และอาหาร (บาท)</th>
                                            <th>กำไร/ขาดทุน (บาท)</th>
                                            <th>สถานะ</th>
                                            <th>รายละเอียด</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $id = $_SESSION['agc_id'];

                                            // ดึงข้อมูลล็อตไก่ทั้งหมด
                                            $check_lots = $db->prepare("
                                                SELECT DISTINCT dcd_id 
                                                FROM data_chick_detail 
                                                WHERE agc_id = :id 
                                                ORDER BY dcd_id DESC
                                            ");
                                            $check_lots->bindParam(':id', $id);
                                            $check_lots->execute();
                                            
                                            $lots = $check_lots->fetchAll();
                                            
                                            if (!$lots) {
                                                echo "<p><td colspan='6' class='text-center'>ไม่พบข้อมูล</td></p>";
                                            } else {
                                                foreach($lots as $lot) {
                                                    $lot_id = $lot['dcd_id'];
                                                    
                                                    // 1. คำนวณรายรับจากการขายไก่
                                                    $sales_query = $db->prepare("
                                                        SELECT SUM(sale_total) as total_income 
                                                        FROM data_sale 
                                                        WHERE dcd_id = :lot_id AND agc_id = :agc_id
                                                    ");
                                                    $sales_query->bindParam(':lot_id', $lot_id);
                                                    $sales_query->bindParam(':agc_id', $id);
                                                    $sales_query->execute();
                                                    $sales_data = $sales_query->fetch(PDO::FETCH_ASSOC);
                                                    $total_income = $sales_data['total_income'] ?? 0;
                                                    
                                                    // 2. คำนวณรายจ่ายค่าไก่
                                                    $chick_cost_query = $db->prepare("
                                                        SELECT SUM(dcd_price) as chick_cost 
                                                        FROM data_chick_detail 
                                                        WHERE dcd_id = :lot_id AND agc_id = :agc_id
                                                    ");
                                                    $chick_cost_query->bindParam(':lot_id', $lot_id);
                                                    $chick_cost_query->bindParam(':agc_id', $id);
                                                    $chick_cost_query->execute();
                                                    $chick_data = $chick_cost_query->fetch(PDO::FETCH_ASSOC);
                                                    $chick_cost = $chick_data['chick_cost'] ?? 0;
                                                    
                                                    // 3. คำนวณรายจ่ายค่าอาหาร
                                                    $feed_cost_query = $db->prepare("
                                                        SELECT SUM(feed_price) as feed_cost 
                                                        FROM data_feeding 
                                                        WHERE dcd_id = :lot_id AND agc_id = :agc_id
                                                    ");
                                                    $feed_cost_query->bindParam(':lot_id', $lot_id);
                                                    $feed_cost_query->bindParam(':agc_id', $id);
                                                    $feed_cost_query->execute();
                                                    $feed_data = $feed_cost_query->fetch(PDO::FETCH_ASSOC);
                                                    $feed_cost = $feed_data['feed_cost'] ?? 0;
                                                    
                                                    // รวมรายจ่ายทั้งหมด
                                                    $total_expense = $chick_cost + $feed_cost;
                                                    
                                                    // คำนวณกำไร/ขาดทุน
                                                    $profit = $total_income - $total_expense;
                                                    
                                                    // กำหนดสถานะ
                                                    $status = ($profit > 0) ? 'กำไร' : (($profit < 0) ? 'ขาดทุน' : 'เท่าทุน');
                                                    $status_class = ($profit > 0) ? 'text-success' : (($profit < 0) ? 'text-danger' : 'text-warning');
                                        ?>
                                        <tr>
                                            <td align="center">ล็อตที่ <?= $lot_id; ?></td>
                                            <td align="right"><?= number_format($total_income, 2); ?></td>
                                            <td align="right"><?= number_format($total_expense, 2); ?></td>
                                            <td align="right" class="<?= $status_class; ?> font-weight-bold">
                                                <?= number_format(abs($profit), 2); ?>
                                            </td>
                                            <td align="center" class="<?= $status_class; ?> font-weight-bold"><?= $status; ?></td>
                                            <td align="center">
                                                <a href="lot_detail.php?lot_id=<?= $lot_id; ?>" class="btn btn-info" style="border-radius: 3rem; font-size: .9rem;">
                                                    ดูรายละเอียด
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
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