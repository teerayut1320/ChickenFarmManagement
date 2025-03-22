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

    <title>รายงานข้อมูลการให้อาหารไก่โดยภาพรวม</title>

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
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลการให้อาหารไก่โดยภาพรวม</h3>
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
                                $food_quantity_by_month = [];
                                $food_price_by_month = [];
                                $date_filter = "";

                                if (isset($_POST['submit'])) {
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $date_filter = "WHERE feed_date BETWEEN '$start_date' AND '$end_date'";
                                    
                                    // ดึงข้อมูลปริมาณอาหารไก่รายเดือน
                                    $sql_quantity = "
                                        SELECT 
                                            MONTH(feed_date) as month,
                                            YEAR(feed_date) as year,
                                            SUM(feed_quan) as total_quantity
                                        FROM data_feeding
                                        $date_filter
                                        GROUP BY YEAR(feed_date), MONTH(feed_date)
                                        ORDER BY YEAR(feed_date), MONTH(feed_date)
                                    ";
                                    
                                    // ดึงข้อมูลค่าอาหารไก่รายเดือน
                                    $sql_price = "
                                        SELECT 
                                            MONTH(feed_date) as month,
                                            YEAR(feed_date) as year,
                                            SUM(feed_price) as total_price
                                        FROM data_feeding
                                        $date_filter
                                        GROUP BY YEAR(feed_date), MONTH(feed_date)
                                        ORDER BY YEAR(feed_date), MONTH(feed_date)
                                    ";
                                    
                                    try {
                                        // ประมวลผลข้อมูลปริมาณอาหาร
                                        $stmt_quantity = $db->prepare($sql_quantity);
                                        $stmt_quantity->execute();
                                        $quantity_results = $stmt_quantity->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        $months_quantity = [];
                                        $quantity_values = [];
                                        
                                        foreach ($quantity_results as $row) {
                                            $month_name = $monthTH[$row['month']];
                                            $months_quantity[] = $month_name;
                                            $quantity_values[] = $row['total_quantity'];
                                        }
                                        
                                        // ประมวลผลข้อมูลค่าอาหาร
                                        $stmt_price = $db->prepare($sql_price);
                                        $stmt_price->execute();
                                        $price_results = $stmt_price->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        $months_price = [];
                                        $price_values = [];
                                        
                                        foreach ($price_results as $row) {
                                            $month_name = $monthTH[$row['month']];
                                            $months_price[] = $month_name;
                                            $price_values[] = $row['total_price'];
                                        }
                                        
                                        // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                        $months_quantity_json = json_encode($months_quantity);
                                        $quantity_values_json = json_encode($quantity_values);
                                        $months_price_json = json_encode($months_price);
                                        $price_values_json = json_encode($price_values);
                                        
                                    } catch (PDOException $e) {
                                        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
                                    }
                                }
                            ?>
                            <div class="md-2">
                                <h5 class="m-0 font-weight-bold text-primary text-center mb-2">ช่วงเวลาที่กำหนด
                                    <?php
                                        if (empty($start_date) and empty($end_date)) {
                                            echo "ยังไม่กำหนดช่วงเวลา";
                                        } else {
                                            echo thai_date_fullmonth(strtotime($start_date))." ถึง ".thai_date_fullmonth(strtotime($end_date));
                                        }
                                    ?>
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-xl-6 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดปริมาณอาหารไก่ที่ให้ในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="foodQuantityChart"></canvas> </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดการจ่ายค่าอาหารไก่ในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="foodPriceChart"></canvas> </div>
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
            '#5a5c69', '#6610f2', '#fd7e14', '#20c9a6', '#858796',
            '#5D2E8C', '#2C7873'
        ];

        // กราฟแสดงปริมาณอาหารรายเดือน
        var monthsQuantity = <?php echo $months_quantity_json ?? '[]'; ?>;
        var quantityValues = <?php echo $quantity_values_json ?? '[]'; ?>;
        
        var ctxQuantity = document.getElementById('foodQuantityChart').getContext('2d');
        var foodQuantityChart = new Chart(ctxQuantity, {
            type: 'bar',
            data: {
                labels: monthsQuantity,
                datasets: [{
                    label: 'ปริมาณอาหาร (กก.)',
                    data: quantityValues,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
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
                                return value.toLocaleString('th-TH') + ' กก.';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'ปริมาณอาหาร: ' + 
                                    context.parsed.y.toLocaleString('th-TH') + ' กก.';
                            }
                        }
                    }
                }
            }
        });

        // กราฟแสดงค่าอาหารรายเดือน
        var monthsPrice = <?php echo $months_price_json ?? '[]'; ?>;
        var priceValues = <?php echo $price_values_json ?? '[]'; ?>;
        
        var ctxPrice = document.getElementById('foodPriceChart').getContext('2d');
        var foodPriceChart = new Chart(ctxPrice, {
            type: 'bar',
            data: {
                labels: monthsPrice,
                datasets: [{
                    label: 'ค่าอาหาร (บาท)',
                    data: priceValues,
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a',
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
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'ค่าอาหาร: ' + 
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