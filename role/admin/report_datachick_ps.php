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

    <title>รายงานข้อมูลไก่รายบุคคล</title>

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
                            <h3 class="m-0 font-weight-bold text-chick1 text-center">รายงานข้อมูลไก่รายบุคคล</h3>
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
                                $monthly_quantity = [];
                                $monthly_price = [];
                                $month_labels = [];
                                $date_filter = "";
                                $agc_filter = "";
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
                                    $date_filter = "dcd_date BETWEEN '$start_date' AND '$end_date'";
                                    $agc_filter = "agc_id = $agc_id";
                                    
                                    // ดึงข้อมูลจำนวนไก่รายเดือน
                                    $sql_quantity = "
                                        SELECT 
                                            MONTH(dcd_date) as month,
                                            YEAR(dcd_date) as year,
                                            SUM(dcd_quan) as total_quantity
                                        FROM data_chick_detail
                                        WHERE $date_filter AND $agc_filter
                                        GROUP BY YEAR(dcd_date), MONTH(dcd_date)
                                        ORDER BY YEAR(dcd_date), MONTH(dcd_date)
                                    ";
                                    
                                    // ดึงข้อมูลค่าไก่รายเดือน
                                    $sql_price = "
                                        SELECT 
                                            MONTH(dcd_date) as month,
                                            YEAR(dcd_date) as year,
                                            SUM(dcd_price) as total_price
                                        FROM data_chick_detail
                                        WHERE $date_filter AND $agc_filter
                                        GROUP BY YEAR(dcd_date), MONTH(dcd_date)
                                        ORDER BY YEAR(dcd_date), MONTH(dcd_date)
                                    ";
                                    
                                    try {
                                        // ประมวลผลข้อมูลจำนวนไก่
                                        $stmt_quantity = $db->prepare($sql_quantity);
                                        $stmt_quantity->execute();
                                        $quantity_results = $stmt_quantity->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // ประมวลผลข้อมูลค่าไก่
                                        $stmt_price = $db->prepare($sql_price);
                                        $stmt_price->execute();
                                        $price_results = $stmt_price->fetchAll(PDO::FETCH_ASSOC);
                                        
                                        // สร้าง array สำหรับเก็บข้อมูลทุกเดือน
                                        $monthly_data = [];
                                        
                                        // รวมข้อมูลจำนวนไก่
                                        foreach ($quantity_results as $row) {
                                            $month_key = $row['year'] . '-' . $row['month'];
                                            $month_name = $monthTH[$row['month']];
                                            
                                            if (!isset($monthly_data[$month_key])) {
                                                $monthly_data[$month_key] = [
                                                    'month_name' => $month_name,
                                                    'quantity' => 0,
                                                    'price' => 0
                                                ];
                                            }
                                            
                                            $monthly_data[$month_key]['quantity'] = $row['total_quantity'];
                                        }
                                        
                                        // รวมข้อมูลค่าไก่
                                        foreach ($price_results as $row) {
                                            $month_key = $row['year'] . '-' . $row['month'];
                                            $month_name = $monthTH[$row['month']];
                                            
                                            if (!isset($monthly_data[$month_key])) {
                                                $monthly_data[$month_key] = [
                                                    'month_name' => $month_name,
                                                    'quantity' => 0,
                                                    'price' => 0
                                                ];
                                            }
                                            
                                            $monthly_data[$month_key]['price'] = $row['total_price'];
                                        }
                                        
                                        // เรียงข้อมูลตามเดือน
                                        ksort($monthly_data);
                                        
                                        // แยกข้อมูลสำหรับใช้ในกราฟ
                                        foreach ($monthly_data as $data) {
                                            $month_labels[] = $data['month_name'];
                                            $monthly_quantity[] = $data['quantity'];
                                            $monthly_price[] = $data['price'];
                                        }
                                        
                                        // แปลงข้อมูลเป็น JSON สำหรับใช้ใน JavaScript
                                        $month_labels_json = json_encode($month_labels);
                                        $monthly_quantity_json = json_encode($monthly_quantity);
                                        $monthly_price_json = json_encode($monthly_price);
                                        
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
                                <div class="col-xl-6 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold text-chick1">
                                                สรุปยอดจำนวนรับไปเข้าฟาร์มทั้งหมด</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar"> <canvas id="quantityChart"></canvas> </div>
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
                                            <div class="chart-bar"> <canvas id="priceChart"></canvas> </div>
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
        <?php if (isset($_POST['submit']) && !empty($month_labels)): ?>
        // กราฟแสดงจำนวนไก่รายเดือน
        var monthLabels = <?php echo $month_labels_json; ?>;
        var quantityData = <?php echo $monthly_quantity_json; ?>;
        var priceData = <?php echo $monthly_price_json; ?>;
        
        // สีประจำเดือน
        var backgroundColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
            '#5a5c69', '#6610f2', '#fd7e14', '#20c9a6', '#858796',
            '#5D2E8C', '#2C7873'
        ];
        
        // กราฟจำนวนไก่
        var ctxQuantity = document.getElementById('quantityChart').getContext('2d');
        var quantityChart = new Chart(ctxQuantity, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'จำนวนไก่ (ตัว)',
                    data: quantityData,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'จำนวนไก่: ' + 
                                    context.parsed.y.toLocaleString('th-TH') + ' ตัว';
                            }
                        }
                    }
                }
            }
        });
        
        // กราฟค่าไก่
        var ctxPrice = document.getElementById('priceChart').getContext('2d');
        var priceChart = new Chart(ctxPrice, {
            type: 'bar',
            data: {
                labels: monthLabels,
                datasets: [{
                    label: 'ค่าไก่ (บาท)',
                    data: priceData,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
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
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'ค่าไก่: ' + 
                                    context.parsed.y.toLocaleString('th-TH') + ' บาท';
                            }
                        }
                    }
                }
            }
        });
        
        // คำนวณยอดรวม
        var totalQuantity = quantityData.reduce((a, b) => a + b, 0);
        var totalPrice = priceData.reduce((a, b) => a + b, 0);
        
        // เพิ่มการแสดงสรุปยอดรวม
        var summaryQuantityHTML = `
            <div class="mt-4 text-center">
                <div class="h5 mb-0 font-weight-bold text-gray-800">รวมจำนวนไก่ทั้งหมด: ${totalQuantity.toLocaleString('th-TH')} ตัว</div>
            </div>
        `;
        
        var summaryPriceHTML = `
            <div class="mt-4 text-center">
                <div class="h5 mb-0 font-weight-bold text-gray-800">รวมค่าไก่ทั้งหมด: ${totalPrice.toLocaleString('th-TH')} บาท</div>
            </div>
        `;
        
        document.querySelector('#quantityChart').parentNode.insertAdjacentHTML('afterend', summaryQuantityHTML);
        document.querySelector('#priceChart').parentNode.insertAdjacentHTML('afterend', summaryPriceHTML);
        
        <?php else: ?>
        // กรณียังไม่กดปุ่มเรียกดูหรือไม่มีข้อมูล
        document.addEventListener('DOMContentLoaded', function() {
            var ctx1 = document.getElementById('quantityChart').getContext('2d');
            var ctx2 = document.getElementById('priceChart').getContext('2d');
            
            // แสดงข้อความเมื่อยังไม่มีข้อมูล
            new Chart(ctx1, {
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
            
            new Chart(ctx2, {
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