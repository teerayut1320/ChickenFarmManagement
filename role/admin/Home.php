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

    <title>หน้าหลัก</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Kanit:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">
    <?php
        // นับจำนวนผู้ใช้งานระบบ
        $count_users = $db->prepare("SELECT COUNT(*) as total_users FROM user_login WHERE us_role = '2'");
        $count_users->execute();
        $total_users = $count_users->fetch(PDO::FETCH_ASSOC)['total_users'];

        // ดึงข้อมูลการขายรายเดือนสำหรับกราฟเส้น
        $monthly_sales = $db->prepare("
            SELECT 
                MONTH(sale_date) as month,
                YEAR(sale_date) as year,
                SUM(sale_total) as total_sales
            FROM data_sale
            WHERE YEAR(sale_date) = YEAR(CURDATE())
            GROUP BY YEAR(sale_date), MONTH(sale_date)
            ORDER BY YEAR(sale_date), MONTH(sale_date)
        ");
        $monthly_sales->execute();
        $sales_data = $monthly_sales->fetchAll(PDO::FETCH_ASSOC);
        
        // สร้าง array สำหรับเก็บข้อมูลรายเดือน (12 เดือน)
        $sales_by_month = array_fill(0, 12, 0);
        foreach ($sales_data as $sale) {
            $month_index = (int)$sale['month'] - 1; // -1 เพราะ array เริ่มที่ 0
            $sales_by_month[$month_index] = (float)$sale['total_sales'];
        }
        $sales_by_month_json = json_encode($sales_by_month);

        // ดึงข้อมูลรายรับ-รายจ่ายสำหรับกราฟวงกลม
        $income_expense = $db->prepare("
            SELECT 
                inex_type,
                SUM(inex_price) as total
            FROM data_inex
            WHERE YEAR(inex_date) = YEAR(CURDATE())
            GROUP BY inex_type
        ");
        $income_expense->execute();
        $inex_data = $income_expense->fetchAll(PDO::FETCH_ASSOC);
        
        // จัดเตรียมข้อมูลสำหรับกราฟวงกลม
        $income = 0;
        $expense = 0;
        foreach ($inex_data as $item) {
            if ($item['inex_type'] == 'รายรับ') {
                $income = (float)$item['total'];
            } elseif ($item['inex_type'] == 'รายจ่าย') {
                $expense = (float)$item['total'];
            }
        }
        $inex_json = json_encode(['income' => $income, 'expense' => $expense]);
    ?>
    <div id="wrapper">
        <?php include("../../sidebar/sb_admin.php");?> <!--  Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("../../topbar/tb_admin.php");?> <!-- Topbar -->
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xl-3 col-md-6 mb-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-lg font-weight-bold text-warning text-uppercase mb-1">จำนวนเกษตรกรในระบบ</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_users ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                                    <h6 class="m-0 font-weight-bold text-chick1">กราฟแสดงสรุปยอดขายภาพรวมในเเต่ละปี</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">รายงาน:</div>
                                            <a class="dropdown-item" href="#">ดูรายงานรายเดือน</a>
                                            <a class="dropdown-item" href="#">ดูรายงานรายปี</a>
                                        </div>
                                    </div>
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
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-chick1">กราฟแสดงข้อมูล รายรับ-รายจ่าย</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                    </div>
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
                                            <i class="fas fa-circle text-success"></i> รายจ่าย
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
        <script src="vendor/chart.js/Chart.min.js"></script>

        <!-- แทนที่ js/demo/chart-area-demo.js และ js/demo/chart-pie-demo.js -->
        <script>
            // กราฟเส้นแสดงยอดขายรายเดือน
            var ctx = document.getElementById("myAreaChart");
            var salesData = <?php echo $sales_by_month_json; ?>;
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค."],
                    datasets: [{
                        label: "ยอดขาย (บาท)",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: salesData,
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
                            time: {
                                unit: 'date'
                            },
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
                                    return value.toLocaleString('th-TH') + ' บาท';
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
                        display: true
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
                                return datasetLabel + ': ' + tooltipItem.yLabel.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    }
                }
            });

            // กราฟวงกลมแสดงรายรับ-รายจ่าย
            var ctx2 = document.getElementById("myPieChart");
            var inExData = <?php echo $inex_json; ?>;
            var myPieChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ["รายรับ", "รายจ่าย"],
                    datasets: [{
                        data: [inExData.income, inExData.expense],
                        backgroundColor: ['#4e73df', '#1cc88a'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673'],
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
                        callbacks: {
                            label: function(tooltipItem, data) {
                                var dataset = data.datasets[tooltipItem.datasetIndex];
                                var value = dataset.data[tooltipItem.index];
                                return data.labels[tooltipItem.index] + ': ' + value.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    },
                    legend: {
                        display: false
                    },
                    cutoutPercentage: 70,
                },
            });
        </script>

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