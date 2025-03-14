<?php
    session_start(); 
    require_once '../../connect.php';


    $agc_id = $_SESSION['agc_id'];
    // $agc_id = $_SESSION["agc_id"];

    $check_data = $db->prepare("SELECT `agc_name`, `agc_Fname` FROM `agriculturist` WHERE `agc_id` = '$agc_id'");
    $check_data->execute();
    $dcd_data = $check_data->fetch(PDO::FETCH_ASSOC);
    extract($dcd_data);
?>

<nav class="navbar navbar-expand navbar-light bg-white topbar mb-3 static-top shadow">
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
    <ul class="navbar-nav ml-auto">
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                
                <span class="d-lg-inline text-dark text-right" style="font-size: 1rem;">
                    <?= $agc_name?>
                    <br>
                    <span class="d-lg-inline text-gray-600 small text-right">
                    <?= "ชื่อฟาร์ม  ".$agc_Fname?>
                </span>
                </span>
                
                <!-- <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= $agc_Fname?></span> -->
            </a>
        </li>
    </ul>
</nav>