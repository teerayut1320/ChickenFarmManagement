<?php
    session_start(); 
    require_once '../../connect.php';
    
    // เพิ่มอาร์เรย์สำหรับชื่อเดือนภาษาไทย
    $monthTH = [null,'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>เพิ่มข้อมูลการขาย</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        select option:disabled {
            color: #999;
            background-color: #f5f5f5;
        }
        
        .sale-date-alert {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>

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
                            <h3 class="m-0 font-weight-bold text-center">เพิ่มข้อมูลการขาย</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="p-5">
                                        <?php if (isset($_SESSION['error'])): ?>
                                            <div class="alert alert-danger">
                                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                                            </div>
                                        <?php endif; ?>
                                        <form class="user" action="checkadd_dataSale.php" method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">วันที่ทำรายการขาย</label>
                                                    <input type="date" class="form-control" name="date" style="border-radius: 3rem;" required >
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">รหัสล็อตไก่</label>
                                                    <select class="form-control" name="chick_lot" style="border-radius: 3rem;" required>
                                                        <option selected disabled>กรุณาเลือกล็อตไก่....</option>
                                                        <?php
                                                            $id = $_SESSION['agc_id'];
                                                            $check_lots = $db->prepare("
                                                                SELECT 
                                                                    `dcd_id`, 
                                                                    `dcd_date`, 
                                                                    `dcd_quan`,
                                                                    DATEDIFF(CURDATE(), `dcd_date`) AS days_raised,
                                                                    DATE_ADD(`dcd_date`, INTERVAL 60 DAY) AS sale_date
                                                                FROM `data_chick_detail` 
                                                                WHERE `agc_id` = :id AND `dcd_quan` > 0 
                                                                ORDER BY `dcd_id` DESC
                                                            ");
                                                            $check_lots->bindParam(':id', $id);
                                                            $check_lots->execute();
                                                            $chick_lots = $check_lots->fetchAll();
                                                            
                                                            foreach($chick_lots as $lot) {
                                                                $days_raised = $lot['days_raised'];
                                                                $canSell = $days_raised >= 60; // 2 เดือน (60 วัน)
                                                                
                                                                // แปลงรูปแบบวันที่เป็นไทย
                                                                $thai_day = date("d", strtotime($lot['sale_date']));
                                                                $thai_month = $monthTH[date("n", strtotime($lot['sale_date']))];
                                                                $thai_year = date("Y", strtotime($lot['sale_date'])) + 543;
                                                                $thai_date = $thai_day . " " . $thai_month . " " . $thai_year;
                                                        ?>
                                                            <option value="<?= $lot['dcd_id']; ?>" <?= $canSell ? "" : "disabled"; ?>>
                                                                รหัสล็อต <?= $lot['dcd_id']; ?> (<?= $lot['dcd_quan']; ?> ตัว) 
                                                                <?php if (!$canSell): ?>
                                                                    - ขายได้วันที่ <?= $thai_day; ?> <?= $thai_month; ?> <?= $thai_year; ?>
                                                                <?php endif; ?>
                                                            </option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                    <?php if (count($chick_lots) === 0): ?>
                                                        <div class="alert alert-warning mt-2">ไม่พบล็อตไก่ที่มีจำนวนเหลืออยู่</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">จำนวนรวม(ตัว)</label>
                                                    <input type="number" class="form-control" name="quan" style="border-radius: 3rem;" required >
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">น้ำหนักรวม(กิโลกรัม)</label>
                                                    <input type="number" class="form-control" step="0.1" name="weigth" style="border-radius: 3rem;" required >
                                                </div> 
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">ราคาต่อกิโลกรัม</label>
                                                    <input type="number" class="form-control"  step="0.1"  name="priceKg" style="border-radius: 3rem;" required >
                                                </div> 
                                            </div>
                                            <div class="row">
                                                <div class="col-md-5"></div>
                                                <div class="col-md-3">
                                                    <a href="data_sale.php" class="btn btn-danger" style="border-radius: 3rem; font-size: 1rem;">ยกเลิก</a>
                                                    <button type="submit" class="btn btn-chick1" name="submit" style="border-radius: 3rem; font-size: 1rem;">บันทึกข้อมูล</button>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lotSelect = document.querySelector('select[name="chick_lot"]');
            const submitButton = document.querySelector('button[name="submit"]');
            
            lotSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                
                if (selectedOption.disabled) {
                    // ถ้าเลือกล็อตที่ยังขายไม่ได้
                    alert('ล็อตนี้ยังไม่สามารถขายได้ ' + selectedOption.text);
                    submitButton.disabled = true;
                } else {
                    submitButton.disabled = false;
                }
            });
        });
    </script>

</body>

</html>