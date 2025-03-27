<?php
  require_once '../../connect.php';
  session_start();  

//   if (isset($_REQUEST[''];)) {
//     # code...
//   }
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>ข้อมูลอาหารไก่</title>

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
    <div id="wrapper">
        <?php include("../../sidebar/sb_agc.php");?> <!--  Sidebar -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
            <?php include("../../topbar/tb_admin.php");?> <!-- Topbar -->
                <div class="container-fluid">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h3 class="m-0 font-weight-bold text-center">ข้อมูลอาหารไก่</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr align="center">
                                            <th>ชื่ออาหาร</th>
                                            <th>ปริมาณ (กก.)</th>
                                            <th>ราคา/กก. (บาท)</th>
                                            <th>จัดการ</th>  
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $id = $_SESSION['agc_id'];
                                            // echo $id;
                                            // $check_id = $db->prepare("SELECT  `agc_id` FROM `user_login` WHERE `us_id` = '$id'");
                                            // $check_id->execute();
                                            // $agc_id = $check_id->fetch(PDO::FETCH_ASSOC);
                                            // extract($agc_id);

                                            $check_df = $db->prepare("SELECT * FROM `data_food` WHERE `agc_id` = '$id'");
                                            $check_df->execute();
                                            $food_datas = $check_df->fetchAll();

                                            if (!$food_datas) {
                                                echo "<p><td colspan='4' class='text-center'>ไม่พบข้อมูล</td></p>";
                                            } else {
                                                foreach($food_datas as $food_data)  {
                                                    // ตรวจสอบว่ามีคอลัมน์หรือไม่ก่อนใช้งาน
                                                    $quantity = isset($food_data['df_quantity']) ? $food_data['df_quantity'] : 0;
                                                    $price_per_kg = isset($food_data['df_price_per_kg']) ? $food_data['df_price_per_kg'] : 0;
                                        ?>
                                        <tr>
                                            <td><?= $food_data['df_name'];?></td>
                                            <td align="right"><?= number_format($quantity, 2);?></td>
                                            <td align="right"><?= number_format($price_per_kg, 2);?></td>
                                            <td align="center">
                                                <a href="edit_datafood.php?edit_id=<?= $food_data['df_id'];?>" class="btn btn-warning" style="border-radius: 3rem; font-size: .9rem;">แก้ไขข้อมูลอาหารไก่</a>
                                                <!-- <a href="?delete_id=<?= $food_data['df_id'];?>" class="btn btn-danger " name="delete" style = "border-radius: 3rem; font-size: .9rem;">ลบข้อมูล</a> -->
                                            </td>
                                        </tr>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- <buttona type="button" class="btn btn-chick1" style="border-redies" >เพิ่มข้อมูลไก่</buttona> -->
                    <a href="add_datafood.php" class="btn btn-chick1">เพิ่มข้อมูลอาหารไก่</a>
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