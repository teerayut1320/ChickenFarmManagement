<?php

    require_once '../../connect.php';
    header('Content-Type: application/json');
    session_start();    
    
    $sql = $db->prepare("SELECT MONTH(`inex_date`) as \"month\" , `inex_type` , `inex_price` 
                         FROM `data_inex` 
                         WHERE MONTH(`inex_date`) BETWEEN Month('2025-01-01') AND Month('2025-03-31') AND `agc_id`= '22'");
    $sql->execute();

    $data_inex = array();
    while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
        $data_inex[] = $row;
    }

    $data_inexResult = json_encode($data_inex);
    echo $data_inexResult;

    ?>
    