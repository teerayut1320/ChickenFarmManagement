<?php

    require_once '../../connect.php';
    header('Content-Type: application/json');
    session_start();    
    
    $sql = $db->prepare("SELECT MONTH(`dcd_date`) as month , SUM(`dcd_quan`) as total
                        FROM `data_chick_detail` 
                        WHERE MONTH(`dcd_date`) BETWEEN Month('2025-01-01') AND Month('2025-03-31') AND `agc_id`= '22'
                        GROUP BY MONTH(`dcd_date`)
                        ORDER BY MONTH(`dcd_date`) ASC");
    $sql->execute();

    $data_inex = array();
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $data_inex[] = $row;
    }

    $data_inexResult = json_encode($data_inex);
    echo $data_inexResult;

    ?>
    