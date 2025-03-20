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
                            <h3 class="m-0 font-weight-bold text-center">รายงานข้อมูลรายรับ-รายจ่าย</h3>
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

                                    $sql = $db->prepare("SELECT MONTH(`inex_date`) as \"month\" ,`inex_type`, `inex_price` FROM `data_inex` 
                                                         WHERE MONTH(`inex_date`) BETWEEN MONTH('$start_date') AND MONTH('$end_date')  AND `agc_id`= '$agc_id' 
                                                         ORDER BY  MONTH(`inex_date`) ASC");
                                    $sql->execute();

                                    $data_inex = array();
                                    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
                                        $data_inex[] = $row;
                                    }
                                    $data_inexResult = json_encode($data_inex);
                                    // echo $data_inexResult;
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
                                <div class="col-xl-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3">
                                            <h6 class="m-0 font-weight-bold ">
                                                สรุปยอดรายรับ-รายจ่ายในแต่ละเดือน</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-area">
                                                <canvas id="myAreaChart"></canvas>
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
        const data_inexResult = <?php echo $data_inexResult; ?>;
        var data_income = []; 
        var data_expense = []; 
        var data_date = [];
        var data_date_unique = [];
        data_inexResult.forEach(item => {
            switch(item.inex_type){
                case "รายรับ":
                    switch(item.month){
                        case '1':
                            data_income.push(item.inex_price);
                            break;
                        case '2':
                            data_income.push(item.inex_price);
                            break;
                        case '3':
                            data_income.push(item.inex_price);
                            break;  
                        case '4':
                            data_income.push(item.inex_price);
                            break;
                        case '5':
                            data_income.push(item.inex_price);
                            break;  
                        case '6':
                            data_income.push(item.inex_price);
                            break;
                        case '7':
                            data_income.push(item.inex_price);
                            break;  
                        case '8':
                            data_income.push(item.inex_price);
                            break;
                        case '9':
                            data_income.push(item.inex_price);
                            break;      
                        case '10':
                            data_income.push(item.inex_price);
                            break;  
                        case '11':
                            data_income.push(item.inex_price);
                            break;      
                        case '12':
                            data_income.push(item.inex_price);
                            break;      
                    }
                    break;
                
                case "รายจ่าย":
                    switch(item.month){
                        case '1':
                            data_expense.push(item.inex_price);
                            break;
                        case '2':
                            data_expense.push(item.inex_price);
                            break;
                        case '3':
                            data_expense.push(item.inex_price);
                            break;  
                        case '4':
                            data_expense.push(item.inex_price);
                            break;
                        case '5':
                            data_expense.push(item.inex_price);
                            break;  
                        case '6':
                            data_expense.push(item.inex_price);
                            break;
                        case '7':
                            data_expense.push(item.inex_price);
                            break;  
                        case '8':
                            data_expense.push(item.inex_price);
                            break;
                        case '9':
                            data_expense.push(item.inex_price);
                            break;      
                        case '10':
                            data_expense.push(item.inex_price);
                            break;  
                        case '11':
                            data_expense.push(item.inex_price);
                            break;      
                        case '12':
                            data_expense.push(item.inex_price);
                            break; 
                    }
                    break;
            }
            switch (item.month) {
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

            for (let i = 0; i < data_date.length; i++) {
                if (data_date_unique.indexOf(data_date[i]) < 0) {
                    data_date_unique.push(data_date[i]);
                }
            }
        });
        // console.log(data_inexResult);
        console.log(data_income);
        console.log(data_expense);
        console.log(data_date_unique);
        

        var ctx = document.getElementById("myAreaChart");
        var myAreaChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data_date_unique,
                datasets: [{
                    label: "รายรับ",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.1)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: data_income,
                    fill: true
                },{
                    label: "รายจ่าย",
                    lineTension: 0.3,
                    backgroundColor: "rgba(255,23,0,0.1)",
                    borderColor: "rgba(255,23,0,1)",
                    pointRadius: 5,
                    pointBackgroundColor: "rgba(255,23,0,1)",
                    pointBorderColor: "rgba(255,23,0,1)",
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: "rgba(178,21,6,1)",
                    pointHoverBorderColor: "rgba(178,21,6,1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: data_expense,
                    fill: true
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: "rgba(0, 0, 0, 0.1)"
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('th-TH', {
                                    style: 'currency',
                                    currency: 'THB',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 14
                        },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed.y.toLocaleString('th-TH', {
                                    style: 'currency',
                                    currency: 'THB',
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                });
                                return label;
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    line: {
                        tension: 0.3
                    }
                }
            }
        });             
    </script>
</body>

</html>

