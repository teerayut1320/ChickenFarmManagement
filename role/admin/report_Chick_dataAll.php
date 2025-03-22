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

    <title>รายงานข้อมูลไก่โดยภาพรวม</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body id="page-top">
    <?php
        $dayTH = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
        $monthTH = [null,'มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
        $monthTH_brev = [null,'ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];

        function thai_date_fullmonth($time){   // 19 ธันวาคม 2556
            global $dayTH,$monthTH;
            $thai_date_return = date("j",$time);
            $thai_date_return.=" ".$monthTH[date("n",$time)];
            $thai_date_return.= " ".(date("Y",$time)+543);
            return $thai_date_return;
        } 
    ?>
    <div id="wrapper">
        <?php include("../../sidebar/sb_admin.php");?>
        <!--  Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("../../topbar/tb_admin.php");?>
                <!-- Topbar -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลไก่โดยภาพรวม</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row mb-2">
                                    <div class="col-md-3"></div>
                                    <label for="inputState" class="form-label mt-2">ตั้งแต่วันที่</label>
                                    <div class="col-md-2">
                                        <input type="date" style="border-radius: 30px;" id="start_date"
                                            name="start_date" class="form-control" required>
                                    </div>
                                    <label for="inputState" class="form-label mt-2">ถึงวันที่</label>
                                    <div class="col-md-2">
                                        <input type="date" style="border-radius: 30px;" id="end_date" name="end_date"
                                            class="form-control" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" style="border-radius: 30px;" type="submit"
                                            name="submit">เรียกดู</button>
                                    </div>
                                </div>
                            </form>
                            <?php
                                // ตัวแปรสำหรับเก็บข้อมูล
                                $chick_quan_data = [];
                                $chick_price_data = [];
                                $farm_names = [];
                                $date_filter = "";

                                if (isset($_POST['submit'])) {
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $date_filter = "WHERE dcd_date BETWEEN '$start_date' AND '$end_date'";
                                }

                                // ดึงข้อมูลจำนวนไก่รายฟาร์ม
                                $sql_quantity = "
                                    SELECT a.agc_Fname as farm_name, SUM(dcd.dcd_quan) as total_quantity
                                    FROM data_chick_detail dcd
                                    JOIN agriculturist a ON dcd.agc_id = a.agc_id
                                    $date_filter
                                    GROUP BY a.agc_id, a.agc_Fname
                                    ORDER BY total_quantity DESC
                                    LIMIT 10
                                ";

                                // ดึงข้อมูลค่าไก่รายฟาร์ม
                                $sql_price = "
                                    SELECT a.agc_Fname as farm_name, SUM(dcd.dcd_price) as total_price
                                    FROM data_chick_detail dcd
                                    JOIN agriculturist a ON dcd.agc_id = a.agc_id
                                    $date_filter
                                    GROUP BY a.agc_id, a.agc_Fname
                                    ORDER BY total_price DESC
                                    LIMIT 10
                                ";

                                try {
                                    // ดึงข้อมูลจำนวนไก่
                                    $stmt_quantity = $db->prepare($sql_quantity);
                                    $stmt_quantity->execute();
                                    $quantity_results = $stmt_quantity->fetchAll(PDO::FETCH_ASSOC);

                                    $quantity_farm_names = [];
                                    $quantity_values = [];
                                    
                                    foreach ($quantity_results as $row) {
                                        $quantity_farm_names[] = $row['farm_name'];
                                        $quantity_values[] = $row['total_quantity'];
                                    }
                                    
                                    // ดึงข้อมูลค่าไก่
                                    $stmt_price = $db->prepare($sql_price);
                                    $stmt_price->execute();
                                    $price_results = $stmt_price->fetchAll(PDO::FETCH_ASSOC);

                                    $price_farm_names = [];
                                    $price_values = [];
                                    
                                    foreach ($price_results as $row) {
                                        $price_farm_names[] = $row['farm_name'];
                                        $price_values[] = $row['total_price'];
                                    }
                                    
                                    // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                    $quantity_farm_names_json = json_encode($quantity_farm_names);
                                    $quantity_values_json = json_encode($quantity_values);
                                    $price_farm_names_json = json_encode($price_farm_names);
                                    $price_values_json = json_encode($price_values);
                                    
                                } catch (PDOException $e) {
                                    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
                                }
                            ?>
                            <div class="md-2">
                                <h5 class="m-0 font-weight-bold text-primary text-center mb-2">ช่วงเวลาที่กำหนด
                                    <?php
                                        if (empty($start_date) and empty($end_date)) {
                                            echo "ยังไม่กำหนดช่วงเวลา";
                                        }else {
                                            echo  thai_date_fullmonth(strtotime($start_date))." ถึง ".thai_date_fullmonth(strtotime($end_date));
                                        }
                                    ?>
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดจำนวนไก่คงเหลือเข้าฟาร์มทั้งหมด</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="chickQuantityChart"></canvas> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดการจ่ายค่าไก่ที่รับเข้าทั้งหมด</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="chickPriceChart"></canvas> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <?php include("../../footer/footer.php");?>
            <!-- footer -->

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

    <!-- สร้างกราฟ -->
    <script>
        // ตั้งค่าสีสำหรับกราฟ
        const colors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#5a5c69', '#6610f2', '#fd7e14', '#20c9a6', '#858796'
        ];

        // กราฟแสดงจำนวนไก่
        var quantityFarmNames = <?php echo $quantity_farm_names_json ?? '[]'; ?>;
        var quantityValues = <?php echo $quantity_values_json ?? '[]'; ?>;
        
        var ctxQuantity = document.getElementById('chickQuantityChart').getContext('2d');
        var chickQuantityChart = new Chart(ctxQuantity, {
            type: 'bar',
            data: {
                labels: quantityFarmNames,
                datasets: [{
                    label: 'จำนวนไก่ (ตัว)',
                    data: quantityValues,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH') + ' ตัว';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                    context.parsed.y.toLocaleString('th-TH') + ' ตัว';
                            }
                        }
                    }
                }
            }
        });

        // กราฟแสดงค่าไก่
        var priceFarmNames = <?php echo $price_farm_names_json ?? '[]'; ?>;
        var priceValues = <?php echo $price_values_json ?? '[]'; ?>;
        
        var ctxPrice = document.getElementById('chickPriceChart').getContext('2d');
        var chickPriceChart = new Chart(ctxPrice, {
            type: 'bar',
            data: {
                labels: priceFarmNames,
                datasets: [{
                    label: 'ค่าไก่ (บาท)',
                    data: priceValues,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + 
                                    context.parsed.y.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>