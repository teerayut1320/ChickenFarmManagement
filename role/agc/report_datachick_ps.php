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
        <?php include("../../sidebar/sb_agc.php");?>
        <!--  Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <?php include("../../topbar/tb_admin.php");?>
                <!-- Topbar -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">รายงานข้อมูลไก่</h3>
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
                                if (isset($_POST['submit'])) {
                                    $start_date = $_POST['start_date'];
                                    $end_date = $_POST['end_date'];
                                    $agc_id = $_SESSION['agc_id'];

                                    // เพิ่มการดึงข้อมูลไก่ตามล็อต
                                    $sql_lot = $db->prepare("
                                        SELECT 
                                            dcd_id,
                                            dcd_date,
                                            dcd_quan,
                                            MONTH(dcd_date) as month
                                        FROM data_chick_detail 
                                        WHERE dcd_date BETWEEN :start_date AND :end_date 
                                        AND agc_id = :agc_id 
                                        ORDER BY dcd_date ASC
                                    ");
                                    $sql_lot->execute([
                                        ':start_date' => $start_date,
                                        ':end_date' => $end_date,
                                        ':agc_id' => $agc_id
                                    ]);

                                    $lot_data = array();
                                    while ($row = $sql_lot->fetch(PDO::FETCH_ASSOC)) {
                                        $lot_data[] = $row;
                                    }
                                    $lot_dataResult = json_encode($lot_data);


                                    // เพิ่มการดึงข้อมูลไก่ตามล็อต
                                    $sql_price = $db->prepare("
                                        SELECT 
                                            dcd_id,
                                            dcd_date,
                                            dcd_price,
                                            MONTH(dcd_date) as month
                                        FROM data_chick_detail 
                                        WHERE dcd_date BETWEEN :start_date AND :end_date 
                                        AND agc_id = :agc_id 
                                        ORDER BY dcd_date ASC
                                    ");
                                    $sql_price->execute([
                                        ':start_date' => $start_date,
                                        ':end_date' => $end_date,
                                        ':agc_id' => $agc_id
                                    ]);

                                    $lot_dataPrice = array();
                                    while ($row = $sql_price->fetch(PDO::FETCH_ASSOC)) {
                                        $lot_dataPrice[] = $row;
                                    }
                                    $lot_dataPriceResult = json_encode($lot_dataPrice);
                                }

                            ?>
                            <div class="md-2">
                                <h5 class="m-0 font-weight-bold text-primary text-center mb-2">ช่วงเวลาที่กำหนด
                                    <?php
                                        // echo "start_date".$start_date;
                                        // echo "end_date".$end_date;
                                        if (empty($start_date) and empty($end_date)) {
                                            echo "ยังไม่กำหนดช่วงเวลา";
                                        }else {
                                            echo  thai_date_fullmonth(strtotime($start_date))." ถึง ".thai_date_fullmonth(strtotime($end_date));
                                        }
                                    ?>
                                </h5>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold">สรุปยอดปริมาณไก่ตามล็อต</h6>
                                            <div class="d-flex align-items-center">
                                                <label for="lotSelect" class="mr-2">เลือกล็อต:</label>
                                                <select id="lotSelect" class="form-control" style="width: 200px; border-radius: 30px;">
                                                    <option value="all">ทั้งหมด</option>
                                                    <?php
                                                    if (isset($_POST['submit'])) {
                                                        $sql_lots = $db->prepare("
                                                            SELECT DISTINCT dcd_id 
                                                            FROM data_chick_detail 
                                                            WHERE dcd_date BETWEEN :start_date AND :end_date 
                                                            AND agc_id = :agc_id 
                                                            ORDER BY dcd_id ASC
                                                        ");
                                                        $sql_lots->execute([
                                                            ':start_date' => $start_date,
                                                            ':end_date' => $end_date,
                                                            ':agc_id' => $agc_id
                                                        ]);
                                                        while ($lot = $sql_lots->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$lot['dcd_id']}'>ล็อต {$lot['dcd_id']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar">
                                                <canvas id="chickLotChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold">สรุปยอดการจ่ายค่าไก่ที่รับเข้าตามล็อต</h6>
                                            <div class="d-flex align-items-center">
                                                <label for="lotSelectPrice" class="mr-2">เลือกล็อต:</label>
                                                <select id="lotSelectPrice" class="form-control" style="width: 200px; border-radius: 30px;">
                                                    <option value="all">ทั้งหมด</option>
                                                    <?php
                                                    if (isset($_POST['submit'])) {
                                                        $sql_lots = $db->prepare("
                                                            SELECT DISTINCT dcd_id 
                                                            FROM data_chick_detail 
                                                            WHERE dcd_date BETWEEN :start_date AND :end_date 
                                                            AND agc_id = :agc_id 
                                                            ORDER BY dcd_id ASC
                                                        ");
                                                        $sql_lots->execute([
                                                            ':start_date' => $start_date,
                                                            ':end_date' => $end_date,
                                                            ':agc_id' => $agc_id
                                                        ]);
                                                        while ($lot = $sql_lots->fetch(PDO::FETCH_ASSOC)) {
                                                            echo "<option value='{$lot['dcd_id']}'>ล็อต {$lot['dcd_id']}</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar">
                                                <canvas id="chickPriceChart"></canvas>
                                            </div>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/demo/datatables-demo.js"></script>

    <script>

        // เพิ่มตัวแปรสำหรับกราฟใหม่
        const lot_dataResult = <?php echo $lot_dataResult ?? '[]'; ?>;
        let chickLotChart = null;

        function updateChartData(selectedLot) {
            const filteredData = selectedLot === 'all' 
                ? lot_dataResult 
                : lot_dataResult.filter(item => item.dcd_id === selectedLot);

            const monthlyData = {};
            const thaiMonths = {
                1: 'มกราคม', 2: 'กุมภาพันธ์', 3: 'มีนาคม', 4: 'เมษายน',
                5: 'พฤษภาคม', 6: 'มิถุนายน', 7: 'กรกฎาคม', 8: 'สิงหาคม',
                9: 'กันยายน', 10: 'ตุลาคม', 11: 'พฤศจิกายน', 12: 'ธันวาคม'
            };

            filteredData.forEach(item => {
                const month = parseInt(item.month);
                if (!monthlyData[month]) {
                    monthlyData[month] = 0;
                }
                monthlyData[month] += parseFloat(item.dcd_quan);
            });

            const labels = Object.keys(monthlyData).map(month => thaiMonths[month]);
            const data = Object.values(monthlyData);

            if (chickLotChart) {
                chickLotChart.destroy();
            }

            const colorPalette = [
                '#FF6B6B', // แดงสด
                '#4ECDC4', // เขียวมิ้นท์
                '#45B7D1', // ฟ้าอ่อน
                '#96CEB4', // เขียวพาสเทล
                '#FFEEAD', // เหลืองอ่อน
                '#D4A5A5', // ชมพูอมน้ำตาล
                '#9B5DE5', // ม่วง
                '#F15BB5', // ชมพูเข้ม
                '#00BBF9', // ฟ้าสด
                '#00F5D4', // เขียวเทอร์ควอยซ์
                '#FEE440', // เหลืองสด
                '#FF85A1'  // ชมพูอ่อน
            ];

            const ctx = document.getElementById("chickLotChart");
            chickLotChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: selectedLot === 'all' ? "จำนวนไก่ทั้งหมด" : `จำนวนไก่ล็อต ${selectedLot}`,
                        data: data,
                        backgroundColor: colorPalette,
                        borderColor: colorPalette,
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ตัว';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + 
                                           context.parsed.y.toLocaleString() + ' ตัว';
                                }
                            }
                        }
                    }
                }
            });
        }

        // เพิ่ม Event Listener สำหรับ select
        document.getElementById('lotSelect').addEventListener('change', function(e) {
            updateChartData(e.target.value);
        });

        // สร้างกราฟครั้งแรกแสดงข้อมูลทั้งหมด
        updateChartData('all');

        // เพิ่มตัวแปรสำหรับกราฟใหม่
        const lot_dataPriceResult = <?php echo $lot_dataPriceResult ?? '[]'; ?>;
        let chickPriceChart = null;

        // เพิ่มฟังก์ชันสำหรับอัพเดทกราฟราคา
        function updatePriceChartData(selectedLot) {
            const filteredData = selectedLot === 'all' 
                ? lot_dataPriceResult 
                : lot_dataPriceResult.filter(item => item.dcd_id === selectedLot);

            const monthlyData = {};
            const thaiMonths = {
                1: 'มกราคม', 2: 'กุมภาพันธ์', 3: 'มีนาคม', 4: 'เมษายน',
                5: 'พฤษภาคม', 6: 'มิถุนายน', 7: 'กรกฎาคม', 8: 'สิงหาคม',
                9: 'กันยายน', 10: 'ตุลาคม', 11: 'พฤศจิกายน', 12: 'ธันวาคม'
            };

            const colorPalette = [
                '#FF9F40', // ส้มสด
                '#32CD32', // เขียวสด
                '#FF69B4', // ชมพูอ่อน
                '#4169E1', // น้ำเงินรอยัล
                '#FFB6C1',
                '#FF6384', // ชมพูเข้ม
                '#36A2EB', // ฟ้าสด
                '#4BC0C0', // เขียวมิ้นท์
                '#FFCD56', // เหลืองทอง
                '#9966FF', // ม่วงอ่อน // ชมพูพาสเทล
                '#20B2AA', // เขียวฟ้าอ่อน
                '#BA55D3'  // ม่วงกลาง
];

            filteredData.forEach(item => {
                const month = parseInt(item.month);
                if (!monthlyData[month]) {
                    monthlyData[month] = 0;
                }
                monthlyData[month] += parseFloat(item.dcd_price);
            });

            const labels = Object.keys(monthlyData).map(month => thaiMonths[month]);
            const data = Object.values(monthlyData);

            if (chickPriceChart) {
                chickPriceChart.destroy();
            }

            const ctx = document.getElementById("chickPriceChart");
            chickPriceChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: selectedLot === 'all' ? "ค่าใช้จ่ายทั้งหมด" : `ค่าใช้จ่ายล็อต ${selectedLot}`,
                        data: data,
                        backgroundColor: colorPalette,
                        borderColor: colorPalette,
                        borderWidth: 1
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' บาท';
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + 
                                           context.parsed.y.toLocaleString() + ' บาท';
                                }
                            }
                        }
                    }
                }
            });
        }

        // เพิ่ม Event Listener สำหรับ select ใหม่
        document.getElementById('lotSelectPrice').addEventListener('change', function(e) {
            updatePriceChartData(e.target.value);
        });

        // สร้างกราฟราคาครั้งแรกแสดงข้อมูลทั้งหมด
        document.addEventListener('DOMContentLoaded', function() {
            updatePriceChartData('all');
        });
    </script>

</body>

</html>