<?php
    require_once 'connect.php';
    session_start();    
    

    if (isset($_POST['submit'])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        // echo "user = ".$user ;
        // echo "pass = ".$pass ;
    }
    try {
       $select = $db->query("SELECT * FROM `user_login` WHERE `us_name` = '$user' AND `us_pass` = '$pass'");
       $select->execute();

       while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
            $db_us_id = $row['us_id'];
            $db_us_name = $row['us_name'];
            $db_us_pass = $row['us_pass'];
            $db_us_role = $row['us_role'];
            $db_agc_id = $row['agc_id'];
        }

        try {
            if($user != null AND $pass != null ){
                echo "1/";
                if($select->rowCount() > 0){
                    echo "2/" ;
                    if($user == $db_us_name AND $pass == $db_us_pass){
                        echo "3/" ;
                        switch ($db_us_role) {
                            case '1':
                                $_SESSION['id'] = $db_us_id;
                                $_SESSION['username'] = $db_us_name;
                                $_SESSION['password'] = $db_us_pass;
                                $_SESSION['role'] = $db_us_role;
                                $_SESSION['agc_id'] = $db_agc_id;
                                $_SESSION['success'] = "Admin.....Sucessfuiiy LOgin";
                                header("location: role/admin/Home.php");
                                break;
                            case '2':
                                $_SESSION['id'] = $db_us_id;
                                $_SESSION['username'] = $db_us_name;
                                $_SESSION['password'] = $db_us_pass;
                                $_SESSION['role'] = $db_us_role;
                                $_SESSION['agc_id'] = $db_agc_id;
                                $_SESSION['success'] = "agc....Sucessfuiiy LOgin";
                                header("location: role/agc/Home.php");
                                break;
                            default:
                                $_SESSION['error'] = "Wrong username or password or role";
                                header("location: index.php");
                        }
                    }
                }else{
                    $_SESSION['error'] = "Wrong username or password or role";
                    header("location: index.php");
                }
            }else{
                $_SESSION['error'] = "Wrong username or password or role";
                header("location: index.php");
            }
        } catch(PDOException $e) {
            echo $e->getMessage();
        }


    } catch(PDOException $e) {
        echo $e->getMessage();
    }

?>