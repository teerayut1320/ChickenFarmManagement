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

    <title>เพิ่มข้อมูลการให้อาหารไก่</title>

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

    <!-- Page Wrapper -->
    <div id="wrapper">
    <?php include("../../sidebar/sb_agc.php");?> <!--  Sidebar -->
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
            <?php include("../../topbar/tb_admin.php");?> <!-- Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">เพิ่มข้อมูลการให้อาหารไก่</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <div class="p-5">
                                        <!-- แสดงข้อความแจ้งเตือนความผิดพลาด -->
                                        <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                                        
                                        <form class="user" action="checkadd_feeding.php" method="post" id="feedingForm">
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">วันที่ให้อาหาร</label>
                                                    <input type="date" class="form-control" name="date" style="border-radius: 3rem;" required >
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">รหัสล็อตไก่</label>
                                                    <select class="form-control" name="chick_lot" style="border-radius: 3rem;" required>
                                                        <option selected disabled>กรุณาเลือกล็อตไก่....</option>
                                                        <?php
                                                            $id = $_SESSION['agc_id'];
                                                            $check_lots = $db->prepare("
                                                                SELECT `dcd_id`, `dcd_date`, `dcd_quan` 
                                                                FROM `data_chick_detail` 
                                                                WHERE `agc_id` = :id AND `dcd_quan` > 0
                                                                ORDER BY `dcd_id` DESC
                                                            ");
                                                            $check_lots->bindParam(':id', $id);
                                                            $check_lots->execute();
                                                            $chick_lots = $check_lots->fetchAll();
                                                            foreach($chick_lots as $lot) {
                                                        ?>
                                                            <option value="<?= $lot['dcd_id']; ?>">
                                                                รหัสล็อต <?= $lot['dcd_id']; ?> (<?= $lot['dcd_quan']; ?> ตัว)
                                                            </option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-2">
                                                    <label for="" style="font-size: 1.125rem;">ชื่ออาหาร</label>
                                                    <select class="form-control" id="food_select" name="name" style="border-radius: 3rem;" required>
                                                        <option selected disabled>กรุณาเลือกอาหาร....</option>
                                                        <?php
                                                            $id = $_SESSION['agc_id'];
                                                            $check_agc = $db->prepare("
                                                                SELECT `df_id`, `df_name`, `df_price_per_kg`, `df_quantity` 
                                                                FROM `data_food` 
                                                                WHERE `agc_id` = :id
                                                            ");
                                                            $check_agc->bindParam(':id', $id);
                                                            $check_agc->execute();
                                                            $agc_datas = $check_agc->fetchAll();
                                                            
                                                            foreach($agc_datas as $agc_data) {
                                                                $food_price = isset($agc_data['df_price_per_kg']) ? $agc_data['df_price_per_kg'] : 0;
                                                                $food_quantity = isset($agc_data['df_quantity']) ? $agc_data['df_quantity'] : 0;
                                                        ?>
                                                            <option value="<?=$agc_data['df_name']; ?>" 
                                                                    data-price="<?=$food_price; ?>" 
                                                                    data-quantity="<?=$food_quantity; ?>">
                                                                <?=$agc_data['df_name']; ?> (<?=number_format($food_price, 2); ?> บาท/กก., คงเหลือ <?=number_format($food_quantity, 2); ?> กก.)
                                                            </option>
                                                        <?php 
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">ปริมาณอาหาร(กิโลกรัม) <span id="available_quantity" class="text-info"></span></label>
                                                    <input type="number" class="form-control" id="quantity" name="quan" style="border-radius: 3rem;" step="0.01" required >
                                                </div> 
                                            </div>
                                            
                                            <!-- แสดงราคาที่คำนวณได้ แต่ไม่ให้แก้ไข -->
                                            <div class="row mb-3">
                                                <div class="col-md-4 mb-2"></div>
                                                <div class="col-md-4 mb-3">
                                                    <label for="" style="font-size: 1.125rem;">จำนวนเงิน(บาท)</label>
                                                    <input type="text" class="form-control" id="price_display" readonly style="border-radius: 3rem; background-color: #f8f9fc;" >
                                                    <input type="hidden" id="price_input" name="price" >
                                                </div> 
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-5"></div>
                                                <div class="col-md-3">
                                                    <a href="feeding.php" class="btn btn-danger" style="border-radius: 3rem; font-size: 1rem;">ยกเลิก</a>
                                                    <button type="submit" class="btn btn-chick1" id="submitBtn" name="submit" style="border-radius: 3rem; font-size: 1rem;">บันทึกข้อมูล</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->
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
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/datatables-demo.js"></script>
    
    <script>
        $(document).ready(function() {
            var maxQuantity = 0;
            
            function updateAvailableQuantity() {
                var selectedOption = $("#food_select option:selected");
                maxQuantity = parseFloat(selectedOption.data('quantity')) || 0;
                
                if (maxQuantity > 0) {
                    $("#available_quantity").text("(คงเหลือ " + maxQuantity.toFixed(2) + " กก.)");
                    $("#quantity").attr("max", maxQuantity);
                } else {
                    $("#available_quantity").text("(ไม่มีอาหารคงเหลือ)");
                    $("#quantity").attr("max", 0);
                }
            }
            
            function validateQuantity() {
                var quantity = parseFloat($("#quantity").val()) || 0;
                
                if (quantity > maxQuantity) {
                    $("#errorMessage").text("ไม่สามารถให้อาหารได้ เนื่องจากจำนวนที่ให้เกินกว่าจำนวนที่มีอยู่").show();
                    $("#submitBtn").prop("disabled", true);
                    return false;
                } else if (quantity <= 0) {
                    $("#errorMessage").text("กรุณาระบุปริมาณอาหารให้ถูกต้อง").show();
                    $("#submitBtn").prop("disabled", true);
                    return false;
                } else {
                    $("#errorMessage").hide();
                    $("#submitBtn").prop("disabled", false);
                    return true;
                }
            }
            
            function calculatePrice() {
                var selectedOption = $("#food_select option:selected");
                var pricePerKg = parseFloat(selectedOption.data('price')) || 0;
                var quantity = parseFloat($("#quantity").val()) || 0;
                
                var totalPrice = pricePerKg * quantity;
                
                // แสดงราคาทศนิยม 2 ตำแหน่ง
                $("#price_display").val(totalPrice.toFixed(2));
                $("#price_input").val(totalPrice.toFixed(2));
                
                // ตรวจสอบปริมาณทุกครั้งที่คำนวณราคา
                validateQuantity();
            }
            
            // เรียกคำนวณราคาเมื่อมีการเปลี่ยนแปลงอาหารหรือปริมาณ
            $("#food_select").change(function() {
                updateAvailableQuantity();
                calculatePrice();
                
                // ถ้าอาหารหมด ให้แสดงข้อความเตือน
                if (maxQuantity <= 0) {
                    $("#errorMessage").text("ไม่สามารถให้อาหารได้ เนื่องจากไม่มีอาหารชนิดนี้คงเหลือ").show();
                    $("#submitBtn").prop("disabled", true);
                }
            });
            
            $("#quantity").on('input', calculatePrice);
            
            // ตรวจสอบฟอร์มก่อนส่ง
            $("#feedingForm").submit(function(e) {
                if (!validateQuantity()) {
                    e.preventDefault();
                    return false;
                }
                return true;
            });
            
            // ซ่อนข้อความแจ้งเตือนเมื่อโหลดหน้า
            $("#errorMessage").hide();
        });
    </script>

</body>

</html>