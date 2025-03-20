<?php
    require_once '../../connect.php';
    session_start();  

    $agc_id = $_SESSION['agc_id'];
    
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>หน้าหลัก</title>

    <!-- Custom fonts for this template-->
    <link  rel="icon" type="image" href="../../img/house-solid.svg" content="IE=edge">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <?php include("../../sidebar/sb_agc.php");?><!-- Sidebar -->
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
                                                    $check_data = $db->prepare("SELECT SUM(`dcd_quan`) AS total_chick FROM `data_chick_detail` WHERE `agc_id` = '$id'");
                                                    $check_data->execute();
                                                    $result = $check_data->fetch(PDO::FETCH_ASSOC);
                                                    echo number_format($result['total_chick'] ?? 0, 0);
                                                ?>
                                                ตัว</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h5 class="m-0 font-weight-bold text-white">สรุปยอดการขายไก่ในปีนี้ (แบบรายเดือน)</h5>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h5 class="m-0 font-weight-bold text-white">สรุปยอดรายรับ-รายจ่ายในปีนี้</h5>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> รายรับ
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-danger"></i> รายจ่าย
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <?php include("../../footer/footer.php");?> <!-- footer -->
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

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
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <!-- <script src="js/demo/chart-area-demo.js"></script> -->
    <!-- <script src="js/demo/chart-pie-demo.js"></script> -->

    <!-- ดึงข้อมูลรายรับ-รายจ่าย -->
    <script>
    <?php
        $year = date('Y');
        // ดึงข้อมูลรายรับ
        $income = $db->prepare("SELECT COALESCE(SUM(inex_price), 0) as total FROM data_inex WHERE agc_id = :agc_id AND inex_type = 'รายรับ' AND YEAR(inex_date) = :year");
        $income->execute([':agc_id' => $agc_id, ':year' => $year]);
        $income_total = $income->fetch(PDO::FETCH_ASSOC)['total'];

        // ดึงข้อมูลรายจ่าย
        $expense = $db->prepare("SELECT COALESCE(SUM(inex_price), 0) as total FROM data_inex WHERE agc_id = :agc_id AND inex_type = 'รายจ่าย' AND YEAR(inex_date) = :year");
        $expense->execute([':agc_id' => $agc_id, ':year' => $year]);
        $expense_total = $expense->fetch(PDO::FETCH_ASSOC)['total'];
    ?>

    // อัพเดทข้อมูลในกราฟ
    var ctx = document.getElementById("myPieChart");
    var myPieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ["รายรับ", "รายจ่าย"],
        datasets: [{
          data: [<?php echo $income_total; ?>, <?php echo $expense_total; ?>],
          backgroundColor: ['#4e73df', '#e74a3b'],
          hoverBackgroundColor: ['#2e59d9', '#98160a'],
          hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
      },
      options: {
        maintainAspectRatio: false,
        tooltips: {
          backgroundColor: "rgb(255,255,255)",
          bodyFontColor: "#858796",
          borderColor: '#dddfeb',
          borderWidth: 1,
          xPadding: 15,
          yPadding: 15,
          displayColors: false,
          caretPadding: 10,
        },
        legend: {
          display: false
        },
        cutoutPercentage: 80,
      },
    });
    </script>

    <!-- ก่อน </body> -->
    <script>
    // ดึงข้อมูลยอดขายรายเดือน
    <?php
        $year = date('Y');
        $monthly_sales = $db->prepare("
            SELECT 
                MONTH(sale_date) as month,
                SUM(sale_total) as total
            FROM data_sale 
            WHERE agc_id = :agc_id 
            AND YEAR(sale_date) = :year
            GROUP BY MONTH(sale_date)
            ORDER BY MONTH(sale_date)
        ");
        $monthly_sales->execute([':agc_id' => $agc_id, ':year' => $year]);
        
        // สร้างอาเรย์ 12 เดือน เริ่มต้นด้วยค่า 0
        $sales_data = array_fill(1, 12, 0);
        
        // เติมข้อมูลยอดขายตามเดือนที่มี
        while($row = $monthly_sales->fetch(PDO::FETCH_ASSOC)) {
            $sales_data[$row['month']] = floatval($row['total']);
        }
        
        // แปลงเป็น JSON สำหรับใช้ใน JavaScript
        $sales_json = json_encode(array_values($sales_data));
    ?>

    // กราฟเส้น
    var ctx = document.getElementById("myAreaChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."],
            datasets: [{
                label: "ยอดขาย",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: <?php echo $sales_json; ?>,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value, index, values) {
                            return value.toLocaleString() + ' บาท';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString() + ' บาท';
                    }
                }
            }
        }
    });
    </script>

</body>

</html>