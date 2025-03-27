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

    <title>รายงานข้อมูลการให้อาหารไก่รายบุคคล</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <style>
        .table th, .table td {
            vertical-align: middle;
        }
        .text-right {
            text-align: right !important;
        }
    </style>

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
                            <h3 class="m-0 font-weight-bold text-center">รายงานข้อมูลการให้อาหารไก่</h3>
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

                                    // Query เพื่อดึงข้อมูลปริมาณอาหารที่ให้ตามเดือน แยกตามชนิดอาหาร
                                    $sql_food_types = $db->prepare("
                                        SELECT 
                                            MONTH(feed_date) as month, 
                                            feed_name,
                                            SUM(feed_quan) as total_quantity,
                                            SUM(feed_price) as total_price
                                        FROM data_feeding
                                        WHERE feed_date BETWEEN :start_date AND :end_date 
                                        AND agc_id = :agc_id
                                        GROUP BY MONTH(feed_date), feed_name
                                        ORDER BY MONTH(feed_date), feed_name
                                    ");
                                    $sql_food_types->execute([
                                        ':start_date' => $start_date,
                                        ':end_date' => $end_date,
                                        ':agc_id' => $agc_id
                                    ]);

                                    // เตรียมข้อมูลแยกตามเดือนและชนิดอาหาร
                                    $monthlyData = [];
                                    $foodTypes = [];
                                    $totalByMonth = [];
                                    $totalQuantity = 0;
                                    $totalPrice = 0;
                                    
                                    while ($row = $sql_food_types->fetch(PDO::FETCH_ASSOC)) {
                                        $month = (int)$row['month'];
                                        $foodName = $row['feed_name'];
                                        $quantity = floatval($row['total_quantity']);
                                        $price = floatval($row['total_price']);
                                        
                                        if (!isset($monthlyData[$month])) {
                                            $monthlyData[$month] = [];
                                            $totalByMonth[$month] = [
                                                'quantity' => 0,
                                                'price' => 0
                                            ];
                                        }
                                        
                                        $monthlyData[$month][$foodName] = [
                                            'quantity' => $quantity,
                                            'price' => $price
                                        ];
                                        
                                        $totalByMonth[$month]['quantity'] += $quantity;
                                        $totalByMonth[$month]['price'] += $price;
                                        
                                        $totalQuantity += $quantity;
                                        $totalPrice += $price;
                                        
                                        if (!in_array($foodName, $foodTypes)) {
                                            $foodTypes[] = $foodName;
                                        }
                                    }
                                    
                                    // เรียงชนิดอาหาร
                                    sort($foodTypes);
                                    
                                    // Query เพื่อดึงล็อตทั้งหมดที่มีในช่วงเวลาที่กำหนด
                                    $sql_lots = $db->prepare("
                                        SELECT DISTINCT dcd_id 
                                        FROM data_feeding 
                                        WHERE feed_date BETWEEN :start_date AND :end_date 
                                        AND agc_id = :agc_id 
                                        ORDER BY dcd_id ASC
                                    ");
                                    $sql_lots->execute([
                                        ':start_date' => $start_date,
                                        ':end_date' => $end_date,
                                        ':agc_id' => $agc_id
                                    ]);

                                    $allLots = [];
                                    while ($lot = $sql_lots->fetch(PDO::FETCH_ASSOC)) {
                                        if (!empty($lot['dcd_id'])) {
                                            $allLots[] = $lot['dcd_id'];
                                        }
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
                            
                            <?php if (isset($_POST['submit']) && !empty($monthlyData)): ?>
                            <!-- ปริมาณอาหารไก่ -->
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold text-primary">ปริมาณอาหารไก่ที่ให้ตามเดือน</h6>
                                            <div class="d-flex align-items-center">
                                                <label for="foodLotSelect" class="mr-2">เลือกล็อต:</label>
                                                <select id="foodLotSelect" class="form-control" style="width: 200px; border-radius: 30px;">
                                                    <option value="all">ทั้งหมด</option>
                                                    <?php
                                                    foreach ($allLots as $lot) {
                                                        echo "<option value='{$lot}'>ล็อต {$lot}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- สรุปการใช้อาหารทั้งหมด Card -->
                                            <div class="row justify-content-center mb-4">
                                                <div class="col-xl-6 col-md-8">
                                                    <div class="card border-left-primary shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col mr-2">
                                                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                                        ปริมาณอาหารที่ให้ทั้งหมด</div>
                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalQuantity, 2) ?> กิโลกรัม</div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-drumstick-bite fa-2x text-gray-300"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- ตารางปริมาณอาหารตามเดือน -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="foodDataTable" width="100%" cellspacing="0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th rowspan="2" style="vertical-align: middle;">เดือน</th>
                                                            <?php foreach ($foodTypes as $food): ?>
                                                                <th colspan="1" class="text-center"><?= $food ?></th>
                                                            <?php endforeach; ?>
                                                            <th rowspan="2" style="vertical-align: middle;" class="text-right">รวม (กก.)</th>
                                                        </tr>
                                                        <tr>
                                                            <?php foreach ($foodTypes as $food): ?>
                                                                <th class="text-right">ปริมาณ (กก.)</th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        foreach ($monthlyData as $month => $foodData): 
                                                        ?>
                                                        <tr>
                                                            <td><?= $monthTH[$month] ?></td>
                                                            <?php foreach ($foodTypes as $food): ?>
                                                                <td class="text-right">
                                                                    <?= isset($foodData[$food]) ? number_format($foodData[$food]['quantity'], 2) : '-' ?>
                                                                </td>
                                                            <?php endforeach; ?>
                                                            <td class="text-right font-weight-bold">
                                                                <?= number_format($totalByMonth[$month]['quantity'], 2) ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-primary font-weight-bold">
                                                            <td>รวมทั้งหมด</td>
                                                            <?php 
                                                            $totalByFood = [];
                                                            foreach ($foodTypes as $food) {
                                                                $totalByFood[$food] = 0;
                                                                foreach ($monthlyData as $month => $foodData) {
                                                                    if (isset($foodData[$food])) {
                                                                        $totalByFood[$food] += $foodData[$food]['quantity'];
                                                                    }
                                                                }
                                                                echo '<td class="text-right">' . number_format($totalByFood[$food], 2) . '</td>';
                                                            }
                                                            ?>
                                                            <td class="text-right"><?= number_format($totalQuantity, 2) ?></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ค่าใช้จ่ายอาหารไก่ -->
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold text-danger">ค่าใช้จ่ายอาหารไก่ตามเดือน</h6>
                                            <div class="d-flex align-items-center">
                                                <label for="foodPriceLotSelect" class="mr-2">เลือกล็อต:</label>
                                                <select id="foodPriceLotSelect" class="form-control" style="width: 200px; border-radius: 30px;">
                                                    <option value="all">ทั้งหมด</option>
                                                    <?php
                                                    foreach ($allLots as $lot) {
                                                        echo "<option value='{$lot}'>ล็อต {$lot}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <!-- สรุปค่าใช้จ่ายทั้งหมด Card -->
                                            <div class="row justify-content-center mb-4">
                                                <div class="col-xl-6 col-md-8">
                                                    <div class="card border-left-danger shadow h-100 py-2">
                                                        <div class="card-body">
                                                            <div class="row no-gutters align-items-center">
                                                                <div class="col mr-2">
                                                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                                        ค่าใช้จ่ายอาหารไก่ทั้งหมด</div>
                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($totalPrice, 2) ?> บาท</div>
                                                                </div>
                                                                <div class="col-auto">
                                                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- ตารางค่าใช้จ่ายตามเดือน -->
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-hover" id="foodPriceDataTable" width="100%" cellspacing="0">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>เดือน</th>
                                                            <th class="text-right">ค่าใช้จ่าย (บาท)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                        foreach ($monthlyData as $month => $foodData): 
                                                        ?>
                                                        <tr>
                                                            <td><?= $monthTH[$month] ?></td>
                                                            <td class="text-right">
                                                                <?= number_format($totalByMonth[$month]['price'], 2) ?>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr class="table-danger font-weight-bold">
                                                            <td>รวมทั้งหมด</td>
                                                            <td class="text-right"><?= number_format($totalPrice, 2) ?> บาท</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
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

    <script>
        const food_lot_dataResult = <?php echo isset($food_lot_dataResult) ? $food_lot_dataResult : '[]'; ?>;
        const food_lot_datapriceResult = <?php echo isset($food_lot_datapriceResult) ? $food_lot_datapriceResult : '[]'; ?>;
        
        document.getElementById('foodLotSelect').addEventListener('change', function(e) {
            const selectedLot = e.target.value;
            filterTableByLot('foodDataTable', selectedLot);
        });
        
        document.getElementById('foodPriceLotSelect').addEventListener('change', function(e) {
            const selectedLot = e.target.value;
            filterTableByLot('foodPriceDataTable', selectedLot);
        });
        
        function filterTableByLot(tableId, lotId) {
            // This would be implemented using AJAX to fetch data for the specific lot
            // For simplicity, we'll just show a message for now
            if (lotId !== 'all') {
                alert(`กำลังกรองข้อมูลสำหรับล็อต ${lotId} - ต้องใช้ AJAX เพื่อดึงข้อมูลเฉพาะล็อต`);
            } else {
                // Reload the page to show all data
                location.reload();
            }
        }
    </script>
</body>

</html>