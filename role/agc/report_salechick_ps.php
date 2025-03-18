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
                <?php include("../../topbar/tb_admin.php");?>
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

                                $sql = $db->prepare("SELECT MONTH(`sale_date`) as month  , SUM(`sale_total`) as total
                                                    FROM `data_sale` 
                                                    WHERE MONTH(`sale_date`) BETWEEN MONTH('$start_date') AND MONTH('$end_date') AND `agc_id` = '$agc_id'
                                                    GROUP BY MONTH(`sale_date`)
                                                    ORDER BY  MONTH(`sale_date`) ASC");
                                $sql->execute();

                                $data_Sale = array();
                                while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                    $data_Sale[] = $row;
                                }
                                $data_SaleResult = json_encode($data_Sale);
                                // echo $data_SaleResult;
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
                                <div class="col-xl-12 col-lg-7">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold ">
                                                สรุปยอดการขายไก่ในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-bar">
                                                <canvas id="myBarChart"></canvas>
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
        const data_SaleResult = <?php echo $data_SaleResult; ?>;
        var data_sale = []; 
        var data_date = [];
        var data_date_unique = [];
            data_SaleResult.forEach(item => {
                switch(item.month){
                    case '1':
                        data_sale.push(item.total);
                        break;
                    case '2':
                        data_sale.push(item.total);
                        break;
                    case '3':
                        data_sale.push(item.total);
                        break;
                    case '4':
                        data_sale.push(item.total);
                        break;  
                    case '5':
                        data_sale.push(item.total);
                        break;
                    case '6':
                        data_sale.push(item.total);
                        break;  
                    case '7':
                        data_sale.push(item.total);
                        break;
                    case '8':
                        data_sale.push(item.total);
                        break;
                    case '9':
                        data_sale.push(item.total);
                        break;
                    case '10':
                        data_sale.push(item.total);
                        break;
                    case '11':
                        data_sale.push(item.total);
                        break;
                    case '12':
                        data_sale.push(item.total);
                        break;
                        
                }
                switch(item.month){ 
                    case '1':
                        data_date.push("มกราคม");
                        break;
                    case '2':
                        data_date.push("กุมภาพันธ์");
                        break;      
                    case '3':
                        data_date.push("มีนาคม");
                        break;
                    case '4':
                        data_date.push("เมษายน");
                        break;  
                    case '5':
                        data_date.push("พฤษภาคม");
                        break;
                    case '6':
                        data_date.push("มิถุนายน");
                        break;  
                    case '7':
                        data_date.push("กรกฎาคม");
                        break;
                    case '8':
                        data_date.push("สิงหาคม");
                        break;  
                    case '9':
                        data_date.push("กันยายน");
                        break;      
                    case '10':
                        data_date.push("ตุลาคม");
                        break;    
                    case '11':
                        data_date.push("พฤศจิกายน");
                        break;      
                    case '12':
                        data_date.push("ธันวาคม");
                        break;  
                }
            });
        for (let i = 0; i < data_date.length; i++) {
            if (data_date_unique.indexOf(data_date[i]) < 0) {
                data_date_unique.push(data_date[i]);
            }
        }

        var ctx = document.getElementById("myBarChart");    
        var myBarChart = new Chart(ctx, {   
            type: 'bar',
            data: {
                labels: data_date_unique,
                datasets: [{
                    label: "ยอดขาย",
                    data: data_sale,
                    backgroundColor: ["#ef34f6","#ec396e","#6a4903","#9f9f9f","#c33e22","#ec9206","#eef73e","#87be7e","#2aa251","#17d1ae","#256ae3","#8450ca"],
                    borderColor: ["#ef34f6","#ec396e","#6a4903","#9f9f9f","#c33e22","#ec9206","#eef73e","#87be7e","#2aa251","#17d1ae","#256ae3","#8450ca"],
                    pointRadius: 5,
                    pointBackgroundColor: ["#ef34f6","#ec396e","#6a4903","#9f9f9f","#c33e22","#ec9206","#eef73e","#87be7e","#2aa251","#17d1ae","#256ae3","#8450ca"],
                    pointBorderColor: ["#ef34f6","#ec396e","#6a4903","#9f9f9f","#c33e22","#ec9206","#eef73e","#87be7e","#2aa251","#17d1ae","#256ae3","#8450ca"],
                    pointHoverRadius: 5,
                }],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                legend: {
                    display: true
                }       
            }
        });
    </script>

</body>

</html>