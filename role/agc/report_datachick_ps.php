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
                            <h3 class="m-0 font-weight-bold text-center">รายงานข้อมูลการรับไก่ตามล็อต</h3>
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
                                            <h6 class="m-0 font-weight-bold text-primary">จำนวนไก่ที่รับเข้าตามล็อต</h6>
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
                                            <!-- สรุปจำนวนไก่ทั้งหมด Card -->
                                            <div class="row justify-content-center mb-4">
                                                <div class="col-xl-6 col-md-8">
                                                    <div class="card border-left-primary shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col mr-2">
                                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                                        จำนวนไก่ที่รับเข้าทั้งหมด</div>
                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalChickenDisplay">0 ตัว</div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-feather fa-2x text-gray-300"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- ตารางข้อมูลจำนวนไก่ -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="chickDataTable" width="100%" cellspacing="0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>เดือน</th>
                                                            <th class="text-right">จำนวนไก่ที่รับเข้า (ตัว)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- จะถูกเติมด้วย JavaScript -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-primary font-weight-bold">
                                                            <td>รวมทั้งหมด</td>
                                                            <td class="text-right" id="totalChickenTable">0 ตัว</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold text-danger">ค่าใช้จ่ายในการซื้อไก่ตามล็อต</h6>
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
                                            <!-- ค่าใช้จ่ายทั้งหมด Card -->
                                            <div class="row justify-content-center mb-4">
                                                <div class="col-xl-6 col-md-8">
                                                    <div class="card border-left-danger shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col mr-2">
                                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                                        ค่าใช้จ่ายในการซื้อไก่ทั้งหมด</div>
                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPriceDisplay">0 บาท</div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- ตารางข้อมูลค่าใช้จ่าย -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="priceDataTable" width="100%" cellspacing="0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>เดือน</th>
                                                            <th class="text-right">ค่าใช้จ่ายในการซื้อไก่ (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <!-- จะถูกเติมด้วย JavaScript -->
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-danger font-weight-bold">
                                                            <td>รวมทั้งหมด</td>
                                                            <td class="text-right" id="totalPriceTable">0 บาท</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
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
        // เพิ่มตัวแปรสำหรับข้อมูล
        const lot_dataResult = <?php echo $lot_dataResult ?? '[]'; ?>;
        const lot_dataPriceResult = <?php echo $lot_dataPriceResult ?? '[]'; ?>;

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

            // เตรียมข้อมูลสำหรับตาราง
            const sortedMonths = Object.keys(monthlyData).map(Number).sort((a, b) => a - b);
            
            // คำนวณยอดรวม
            const totalChicken = Object.values(monthlyData).reduce((sum, val) => sum + val, 0);
            
            // อัพเดทการ์ดสรุป
            document.getElementById('totalChickenDisplay').textContent = totalChicken.toLocaleString() + ' ตัว';
            
            // อัพเดทตาราง
            const tableBody = document.querySelector('#chickDataTable tbody');
            tableBody.innerHTML = '';
            
            sortedMonths.forEach(month => {
                const quantity = monthlyData[month];
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${thaiMonths[month]}</td>
                    <td class="text-right">${quantity.toLocaleString()} ตัว</td>
                `;
                tableBody.appendChild(row);
            });
            
            document.getElementById('totalChickenTable').textContent = totalChicken.toLocaleString() + ' ตัว';
        }

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

            filteredData.forEach(item => {
                const month = parseInt(item.month);
                if (!monthlyData[month]) {
                    monthlyData[month] = 0;
                }
                monthlyData[month] += parseFloat(item.dcd_price);
            });

            // เตรียมข้อมูลสำหรับตาราง
            const sortedMonths = Object.keys(monthlyData).map(Number).sort((a, b) => a - b);
            
            // คำนวณยอดรวม
            const totalPrice = Object.values(monthlyData).reduce((sum, val) => sum + val, 0);
            
            // อัพเดทการ์ดสรุป
            document.getElementById('totalPriceDisplay').textContent = totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
            
            // อัพเดทตาราง
            const tableBody = document.querySelector('#priceDataTable tbody');
            tableBody.innerHTML = '';
            
            sortedMonths.forEach(month => {
                const price = monthlyData[month];
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${thaiMonths[month]}</td>
                    <td class="text-right">${price.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} บาท</td>
                `;
                tableBody.appendChild(row);
            });
            
            document.getElementById('totalPriceTable').textContent = totalPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' บาท';
        }

        // เพิ่ม Event Listener สำหรับ select
        document.getElementById('lotSelect').addEventListener('change', function(e) {
            updateChartData(e.target.value);
        });

        document.getElementById('lotSelectPrice').addEventListener('change', function(e) {
            updatePriceChartData(e.target.value);
        });

        // สร้างตารางครั้งแรก
        document.addEventListener('DOMContentLoaded', function() {
            if (lot_dataResult.length > 0) {
                updateChartData('all');
                updatePriceChartData('all');
            }
        });
    </script>

</body>

</html>