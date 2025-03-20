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
                <?php 
                if (file_exists("../../topbar/tb_admin.php")) {
                    include("../../topbar/tb_admin.php");
                } else {
                    echo '<div class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                            <!-- Topbar content -->
                          </div>';
                }
                ?>
                <!-- Topbar -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">รายงานข้อมูลการขายไก่</h3>
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

                                $sql_sale_lot = $db->prepare("
                                    SELECT 
                                        s.sale_date,
                                        s.sale_total,
                                        s.dcd_id,
                                        MONTH(s.sale_date) as month
                                    FROM data_sale s
                                    WHERE s.sale_date BETWEEN :start_date AND :end_date 
                                    AND s.agc_id = :agc_id 
                                    ORDER BY s.sale_date ASC
                                ");
                                $sql_sale_lot->execute([
                                    ':start_date' => $start_date,
                                    ':end_date' => $end_date,
                                    ':agc_id' => $agc_id
                                ]);

                                $sale_lot_data = array();
                                while ($row = $sql_sale_lot->fetch(PDO::FETCH_ASSOC)) {
                                    $sale_lot_data[] = $row;
                                }
                                $sale_lot_dataResult = json_encode($sale_lot_data);
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
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold">สรุปยอดการขายไก่ในแต่ละเดือนตามล็อต</h6>
                                            <div class="d-flex align-items-center">
                                                <label for="saleLotSelect" class="mr-2">เลือกล็อต:</label>
                                                <select id="saleLotSelect" class="form-control" style="width: 200px; border-radius: 30px;">
                                                    <option value="all">ทั้งหมด</option>
                                                    <?php
                                                    if (isset($_POST['submit'])) {
                                                        $sql_lots = $db->prepare("
                                                            SELECT DISTINCT dcd_id 
                                                            FROM data_sale 
                                                            WHERE sale_date BETWEEN :start_date AND :end_date 
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
                                                <canvas id="saleLotChart"></canvas>
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
        const sale_lot_dataResult = <?php echo $sale_lot_dataResult ?? '[]'; ?>;
        let saleLotChart = null;

        function updateSaleChartData(selectedLot) {
            const filteredData = selectedLot === 'all' 
                ? sale_lot_dataResult 
                : sale_lot_dataResult.filter(item => item.dcd_id === selectedLot);

            const monthlyData = {};
            const thaiMonths = {
                1: 'มกราคม', 2: 'กุมภาพันธ์', 3: 'มีนาคม', 4: 'เมษายน',
                5: 'พฤษภาคม', 6: 'มิถุนายน', 7: 'กรกฎาคม', 8: 'สิงหาคม',
                9: 'กันยายน', 10: 'ตุลาคม', 11: 'พฤศจิกายน', 12: 'ธันวาคม'
            };

            const colorPalette = [
                '#FF6384', '#36A2EB', '#4BC0C0', '#FFCD56', '#9966FF', '#FF9F40',
                '#32CD32', '#FF69B4', '#4169E1', '#FFB6C1', '#20B2AA', '#BA55D3'
            ];

            filteredData.forEach(item => {
                const month = parseInt(item.month);
                if (!monthlyData[month]) {
                    monthlyData[month] = 0;
                }
                monthlyData[month] += parseFloat(item.sale_total);
            });

            const labels = Object.keys(monthlyData).map(month => thaiMonths[month]);
            const data = Object.values(monthlyData);

            if (saleLotChart) {
                saleLotChart.destroy();
            }

            const ctx = document.getElementById("saleLotChart");
            saleLotChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: selectedLot === 'all' ? "ยอดขายทั้งหมด" : `ยอดขายล็อต ${selectedLot}`,
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

        // เพิ่ม Event Listener สำหรับ select
        document.getElementById('saleLotSelect').addEventListener('change', function(e) {
            updateSaleChartData(e.target.value);
        });

        // สร้างกราฟครั้งแรกแสดงข้อมูลทั้งหมด
        document.addEventListener('DOMContentLoaded', function() {
            updateSaleChartData('all');
        });
    </script>

</body>

</html>