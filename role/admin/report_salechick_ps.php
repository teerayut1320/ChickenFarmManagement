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

    <title>รายงานข้อมูลการขายไก่รายบุคคล</title>

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
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลการขายไก่รายบุคคล</h3>
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
                                $quantity_data = [];
                                $weight_data = [];
                                $total_data = [];
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
                                    $date_filter = "sale_date BETWEEN '$start_date' AND '$end_date'";
                                    $agc_filter = "agc_id = $agc_id";
                                    
                                    // ดึงข้อมูลการขายไก่รายเดือน
                                    $sql_sales = "
                                        SELECT 
                                            MONTH(sale_date) as month,
                                            YEAR(sale_date) as year,
                                            SUM(sale_quan) as total_quantity,
                                            SUM(sale_weigth) as total_weight,
                                            SUM(sale_total) as total_sales
                                        FROM data_sale
                                        WHERE $date_filter AND $agc_filter
                                        GROUP BY YEAR(sale_date), MONTH(sale_date)
                                        ORDER BY YEAR(sale_date), MONTH(sale_date)
                                    ";
                                    
                                    try {
                                        // ประมวลผลข้อมูลการขาย
                                        $stmt_sales = $db->prepare($sql_sales);
                                        $stmt_sales->execute();
                                        $sales_results = $stmt_sales->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // สร้าง array เก็บข้อมูลรายเดือน
                                        $monthly_data = [];
                                        
                                        foreach ($sales_results as $row) {
                                            $month_key = $row['year'] . '-' . $row['month'];
                                            $month_name = $monthTH[$row['month']];
                                            
                                            $month_labels[] = $month_name;
                                            $quantity_data[] = $row['total_quantity'];
                                            $weight_data[] = $row['total_weight'];
                                            $total_data[] = $row['total_sales'];
                                        }
                                        
                                        // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                        $month_labels_json = json_encode($month_labels);
                                        $quantity_json = json_encode($quantity_data);
                                        $weight_json = json_encode($weight_data);
                                        $total_json = json_encode($total_data);
                                        
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
                                                สรุปยอดการขายไก่ในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="saleChart"></canvas> </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_POST['submit']) && !empty($month_labels)): ?>
                            <!-- แสดงข้อมูลในรูปแบบตาราง -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-chick1">
                                        ตารางสรุปข้อมูลการขายไก่</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>เดือน</th>
                                                    <th>จำนวนไก่ (ตัว)</th>
                                                    <th>น้ำหนักรวม (กก.)</th>
                                                    <th>ยอดขายรวม (บาท)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $grand_total_quantity = 0;
                                                $grand_total_weight = 0;
                                                $grand_total_sales = 0;
                                                
                                                for ($i = 0; $i < count($month_labels); $i++) { 
                                                    $grand_total_quantity += $quantity_data[$i];
                                                    $grand_total_weight += $weight_data[$i];
                                                    $grand_total_sales += $total_data[$i];
                                                ?>
                                                <tr>
                                                    <td><?php echo $month_labels[$i]; ?></td>
                                                    <td><?php echo number_format($quantity_data[$i], 0); ?> ตัว</td>
                                                    <td><?php echo number_format($weight_data[$i], 2); ?> กก.</td>
                                                    <td><?php echo number_format($total_data[$i], 2); ?> บาท</td>
                                                </tr>
                                                <?php } ?>
                                                <tr class="font-weight-bold bg-light">
                                                    <td>รวมทั้งหมด</td>
                                                    <td><?php echo number_format($grand_total_quantity, 0); ?> ตัว</td>
                                                    <td><?php echo number_format($grand_total_weight, 2); ?> กก.</td>
                                                    <td><?php echo number_format($grand_total_sales, 2); ?> บาท</td>
                                                </tr>
                                            </tbody>
                                        </table>
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
        // กราฟแสดงข้อมูลการขายไก่รายเดือน
        var monthLabels = <?php echo $month_labels_json; ?>;
        var quantityData = <?php echo $quantity_json; ?>;
        var weightData = <?php echo $weight_json; ?>;
        var totalData = <?php echo $total_json; ?>;
        
        var ctx = document.getElementById('saleChart').getContext('2d');
        var saleChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'จำนวนไก่ (ตัว)',
                    data: quantityData,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1,
                    yAxisID: 'y-quantity'
                }, {
                    label: 'น้ำหนักรวม (กก.)',
                    data: weightData,
                    backgroundColor: '#1cc88a',
                    borderColor: '#1cc88a',
                    borderWidth: 1,
                    yAxisID: 'y-weight'
                }, {
                    label: 'ยอดขายรวม (บาท)',
                    data: totalData,
                    backgroundColor: '#f6c23e',
                    borderColor: '#f6c23e',
                    borderWidth: 1,
                    yAxisID: 'y-total',
                    type: 'line', // ใช้กราฟเส้นสำหรับยอดขาย
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    'y-quantity': {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'จำนวนไก่ (ตัว)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH');
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    'y-weight': {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'น้ำหนักรวม (กก.)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH');
                            }
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    },
                    'y-total': {
                        type: 'linear',
                        display: false,
                        position: 'right',
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH');
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
                                
                                if (label.includes('จำนวนไก่')) {
                                    return label + ': ' + value.toLocaleString('th-TH') + ' ตัว';
                                } else if (label.includes('น้ำหนัก')) {
                                    return label + ': ' + value.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' กก.';
                                } else if (label.includes('ยอดขาย')) {
                                    return label + ': ' + value.toLocaleString('th-TH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
                                }
                                
                                return label;
                            }
                        }
                    }
                }
            }
        });
        <?php else: ?>
        // กรณียังไม่กดปุ่มเรียกดูหรือไม่มีข้อมูล
        document.addEventListener('DOMContentLoaded', function() {
            var ctx = document.getElementById('saleChart').getContext('2d');
            
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