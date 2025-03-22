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

    <title>รายงานข้อมูลรายรับ-รายจ่ายโดยภาพรวม</title>

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
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลรายรับ-รายจ่ายโดยภาพรวม</h3>
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
                                $months_data = [];
                                $income_data = [];
                                $expense_data = [];
                                $date_filter = "";

                                if (isset($_POST['submit'])) {
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $date_filter = "WHERE inex_date BETWEEN '$start_date' AND '$end_date'";
                                    
                                    // ดึงข้อมูลรายรับรายจ่ายรายเดือน
                                    $sql_inex = "
                                        SELECT 
                                            MONTH(inex_date) as month,
                                            YEAR(inex_date) as year,
                                            inex_type,
                                            SUM(inex_price) as total_amount
                                        FROM data_inex
                                        $date_filter
                                        GROUP BY YEAR(inex_date), MONTH(inex_date), inex_type
                                        ORDER BY YEAR(inex_date), MONTH(inex_date)
                                    ";
                                    
                                    try {
                                        // ประมวลผลข้อมูลรายรับรายจ่าย
                                        $stmt_inex = $db->prepare($sql_inex);
                                        $stmt_inex->execute();
                                        $inex_results = $stmt_inex->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // สร้าง array เก็บข้อมูลรายเดือน
                                        $monthly_data = [];
                                        
                                        foreach ($inex_results as $row) {
                                            $month_key = $row['year'] . '-' . $row['month'];
                                            $month_name = $monthTH[$row['month']];
                                            
                                            if (!isset($monthly_data[$month_key])) {
                                                $monthly_data[$month_key] = [
                                                    'month_name' => $month_name,
                                                    'รายรับ' => 0,
                                                    'รายจ่าย' => 0
                                                ];
                                            }
                                            
                                            $monthly_data[$month_key][$row['inex_type']] += $row['total_amount'];
                                        }
                                        
                                        // เรียงข้อมูลตามเดือน
                                        ksort($monthly_data);
                                        
                                        // แยกข้อมูลสำหรับใช้ในกราฟ
                                        foreach ($monthly_data as $data) {
                                            $months_data[] = $data['month_name'];
                                            $income_data[] = $data['รายรับ'];
                                            $expense_data[] = $data['รายจ่าย'];
                                        }
                                        
                                        // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                        $months_json = json_encode($months_data);
                                        $income_json = json_encode($income_data);
                                        $expense_json = json_encode($expense_data);
                                        
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
                                <div class="col-xl-12 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดรายรับ-รายจ่ายในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="inExChart"></canvas> </div>
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
        <?php if (isset($_POST['submit']) && !empty($months_data)): ?>
        // กราฟแสดงรายรับ-รายจ่ายรายเดือน
        var months = <?php echo $months_json; ?>;
        var incomeData = <?php echo $income_json; ?>;
        var expenseData = <?php echo $expense_json; ?>;
        
        var ctx = document.getElementById('inExChart').getContext('2d');
        var inExChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'รายรับ',
                    data: incomeData,
                    backgroundColor: '#1cc88a', // สีเขียว
                    borderColor: '#1cc88a',
                    borderWidth: 1
                }, {
                    label: 'รายจ่าย',
                    data: expenseData,
                    backgroundColor: '#e74a3b', // สีแดง
                    borderColor: '#e74a3b',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
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
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toLocaleString('th-TH') + ' บาท';
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // คำนวณยอดรวมและกำไร/ขาดทุน
        var totalIncome = incomeData.reduce((a, b) => a + b, 0);
        var totalExpense = expenseData.reduce((a, b) => a + b, 0);
        var profit = totalIncome - totalExpense;
        
        // เพิ่มการแสดงสรุปยอดรวม
        var summaryHTML = `
            <div class="row mt-4">
                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        รวมรายรับทั้งหมด</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${totalIncome.toLocaleString('th-TH')} บาท</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        รวมรายจ่ายทั้งหมด</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${totalExpense.toLocaleString('th-TH')} บาท</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-md-6 mb-4">
                    <div class="card border-left-${profit >= 0 ? 'info' : 'warning'} shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-${profit >= 0 ? 'info' : 'warning'} text-uppercase mb-1">
                                        ${profit >= 0 ? 'กำไรสุทธิ' : 'ขาดทุนสุทธิ'}</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">${Math.abs(profit).toLocaleString('th-TH')} บาท</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-${profit >= 0 ? 'plus' : 'minus'}-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.querySelector('.chart-bar').insertAdjacentHTML('afterend', summaryHTML);
        
        <?php else: ?>
        // กรณียังไม่กดปุ่มเรียกดูหรือไม่มีข้อมูล
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('inExChart').getContext('2d');
            
            // แสดงข้อความเมื่อยังไม่มีข้อมูล
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: []
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'กรุณากำหนดช่วงเวลาและกดปุ่มเรียกดู',
                            font: {
                                size: 16
                            }
                        }
                    }
                }
            });
        });
        <?php endif; ?>
    </script>

</body>

</html>