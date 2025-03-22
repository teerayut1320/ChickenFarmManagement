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

    <title>รายงานข้อมูลรายรับ-รายจ่ายรายบุคคล</title>

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
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลรายรับ-รายจ่ายรายบุคคล</h3>
                        </div>
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row mb-2">
                                    <div class="col-md-2"></div>
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
                                    <label for="inputState" class="form-label mt-2">เกษตรกรผู้เลี้ยงไก่</label>
                                    <div class="col-md-2">
                                        <select class="form-control" aria-label="Default select example" id="agc_id" name="agc_id" style="border-radius: 30px;">
                                            <option selected disabled>กรุณาเลือกเกษตรกรผู้เลี้ยงไก่....</option>
                                            <?php 
                                                $stmt = $db->query("SELECT `agc_id`, `agc_name`  FROM `agriculturist`");
                                                $stmt->execute();
                                                $agcs = $stmt->fetchAll();
                                                
                                                foreach($agcs as $agc){
                                            ?>
                                            <option value="<?= $agc['agc_id']?>"><?= $agc['agc_name']?></option>
                                            <?php
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-primary" style="border-radius: 30px;" type="submit"
                                            name="submit">เรียกดู</button>
                                    </div>
                                </div>
                            </form>
                            <?php
                                // ตัวแปรสำหรับเก็บข้อมูล
                                $month_labels = [];
                                $income_data = [];
                                $expense_data = [];
                                $balance_data = [];
                                $selected_agc_name = "";
                                
                                if (isset($_POST['submit'])) {
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $agc_id = $_POST['agc_id'];
                                    
                                    // ดึงชื่อเกษตรกร
                                    $stmt_agc = $db->prepare("SELECT agc_name FROM agriculturist WHERE agc_id = ?");
                                    $stmt_agc->execute([$agc_id]);
                                    $agc_result = $stmt_agc->fetch(PDO::FETCH_ASSOC);
                                    $selected_agc_name = $agc_result ? $agc_result['agc_name'] : "";
                                    
                                    // สร้างเงื่อนไข SQL
                                    $date_filter = "inex_date BETWEEN '$start_date' AND '$end_date'";
                                    $agc_filter = "agc_id = $agc_id";
                                    
                                    // ดึงข้อมูลรายรับ-รายจ่ายรายเดือน
                                    $sql_inex = "
                                        SELECT 
                                            MONTH(inex_date) as month,
                                            YEAR(inex_date) as year,
                                            inex_type,
                                            SUM(inex_price) as total_amount
                                        FROM data_inex
                                        WHERE $date_filter AND $agc_filter
                                        GROUP BY YEAR(inex_date), MONTH(inex_date), inex_type
                                        ORDER BY YEAR(inex_date), MONTH(inex_date), inex_type
                                    ";
                                    
                                    try {
                                        // ประมวลผลข้อมูลรายรับ-รายจ่าย
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
                                            $month_labels[] = $data['month_name'];
                                            $income_data[] = $data['รายรับ'];
                                            $expense_data[] = $data['รายจ่าย'];
                                            $balance_data[] = $data['รายรับ'] - $data['รายจ่าย'];
                                        }
                                        
                                        // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                        $month_labels_json = json_encode($month_labels);
                                        $income_json = json_encode($income_data);
                                        $expense_json = json_encode($expense_data);
                                        $balance_json = json_encode($balance_data);
                                        
                                    } catch (PDOException $e) {
                                        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
                                    }
                                }
                            ?>
                            <div class="md-2">
                                <h5 class="m-0 font-weight-bold text-primary text-center mb-2">
                                    <?php if (!empty($selected_agc_name)): ?>
                                        เกษตรกร: <?php echo $selected_agc_name; ?><br>
                                    <?php endif; ?>
                                    ช่วงเวลาที่กำหนด
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
                                            <div class="chart-bar"> <canvas id="incomeExpenseChart"></canvas> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_POST['submit']) && !empty($month_labels)): ?>
                            <!-- แสดงข้อมูลในรูปแบบตาราง -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-chick1">
                                        ตารางสรุปข้อมูลรายรับ-รายจ่าย</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>เดือน</th>
                                                    <th>รายรับ (บาท)</th>
                                                    <th>รายจ่าย (บาท)</th>
                                                    <th>ยอดคงเหลือ (บาท)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_income = 0;
                                                $total_expense = 0;
                                                $total_balance = 0;
                                                
                                                for ($i = 0; $i < count($month_labels); $i++) { 
                                                    $total_income += $income_data[$i];
                                                    $total_expense += $expense_data[$i];
                                                    $total_balance += $balance_data[$i];
                                                    
                                                    // กำหนดสีตามยอดคงเหลือ
                                                    $balance_class = $balance_data[$i] >= 0 ? 'text-success' : 'text-danger';
                                                ?>
                                                <tr>
                                                    <td><?php echo $month_labels[$i]; ?></td>
                                                    <td class="text-right"><?php echo number_format($income_data[$i], 2); ?></td>
                                                    <td class="text-right"><?php echo number_format($expense_data[$i], 2); ?></td>
                                                    <td class="text-right <?php echo $balance_class; ?>"><?php echo number_format($balance_data[$i], 2); ?></td>
                                                </tr>
                                                <?php } ?>
                                                <tr class="font-weight-bold bg-light">
                                                    <td>รวมทั้งหมด</td>
                                                    <td class="text-right"><?php echo number_format($total_income, 2); ?></td>
                                                    <td class="text-right"><?php echo number_format($total_expense, 2); ?></td>
                                                    <td class="text-right <?php echo $total_balance >= 0 ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($total_balance, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- แสดงสรุปผลรวม -->
                            <div class="row">
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-success shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                        รวมรายรับทั้งหมด</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_income, 2); ?> บาท</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
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
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_expense, 2); ?> บาท</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-xl-4 col-md-6 mb-4">
                                    <div class="card border-left-<?php echo $total_balance >= 0 ? 'primary' : 'warning'; ?> shadow h-100 py-2">
                                        <div class="card-body">
                                            <div class="row no-gutters align-items-center">
                                                <div class="col mr-2">
                                                    <div class="text-xs font-weight-bold text-<?php echo $total_balance >= 0 ? 'primary' : 'warning'; ?> text-uppercase mb-1">
                                                        ยอดคงเหลือ</div>
                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($total_balance, 2); ?> บาท</div>
                                                </div>
                                                <div class="col-auto">
                                                    <i class="fas fa-<?php echo $total_balance >= 0 ? 'chart-line' : 'exclamation-triangle'; ?> fa-2x text-gray-300"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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
        <?php if (isset($_POST['submit']) && !empty($month_labels)): ?>
        // กราฟแสดงข้อมูลรายรับ-รายจ่ายรายเดือน
        var monthLabels = <?php echo $month_labels_json; ?>;
        var incomeData = <?php echo $income_json; ?>;
        var expenseData = <?php echo $expense_json; ?>;
        var balanceData = <?php echo $balance_json; ?>;
        
        var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
        var incomeExpenseChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
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
                }, {
                    label: 'ยอดคงเหลือ',
                    data: balanceData,
                    backgroundColor: '#4e73df', // สีน้ำเงิน
                    borderColor: '#4e73df',
                    borderWidth: 1,
                    type: 'line',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.parsed.y;
                                return label + ': ' + value.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
                            }
                        }
                    }
                }
            }
        });
        <?php else: ?>
        // กรณียังไม่กดปุ่มเรียกดูหรือไม่มีข้อมูล
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('incomeExpenseChart').getContext('2d');
            
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
                            text: 'กรุณากำหนดช่วงเวลา เลือกเกษตรกร และกดปุ่มเรียกดู',
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