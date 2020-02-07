<?php
/**
 * Created by PhpStorm.
 * User: Hollyphat
 * Date: 6/20/2018
 * Time: 8:08 AM
 */

header("Access-Control-Allow-Origin: *");
//define('ENV','local');

require_once "admin/core/db.php";

/*function user_details($id,$v){
    global $db;
    $sql = $db->prepare("SELECT * FROM users WHERE id = :id");
    $sql->execute(array(
        'id' => $id
    ));

    $rs = $sql->fetch(PDO::FETCH_ASSOC);

    $sql->closeCursor();

    return $rs[$v];
}
*/
if(isset($_POST['reg-ok'])){    
    $names = $_POST['name'];
    $password = sha1($_POST['password']);
    $gender = $_POST['gender'];
    $email = $_POST['email'];
    
    $phone = $_POST['phone'];    
    $error = array();

    
    
    $sql_check = $db->prepare("SELECT NULL FROM users WHERE email = :email");
    $sql_check->execute(array('email' => $email));
    $counts = $sql_check->rowCount();
    $sql_check->closeCursor();

    if($counts > 0) {
        $email_err = "<span style='color: #f00;'>Email address already exist!</span>";
        $error[] = $email_err;
    }

    $sql_check = $db->prepare("SELECT NULL FROM users WHERE phone = :phone");
    $sql_check->execute(array('phone' => $phone));
    $counts = $sql_check->rowCount();
    $sql_check->closeCursor();

    if($counts > 0) {
        $phone_err = "<span style='color: #f00;'>Phone number already exist!</span>";
        $error[] = $phone_err;

        //$out = array('ok' => 0, 'msg' => $phone_err);
        //echo json_encode($out);
    }

    if(strlen($_POST['password']) < 7){
        $pass_err = "<span style='color: #f00;'>Password is invalid, it must be at least 7 characters!</span>";
        $error[] = $pass_err;

        //$out = array('ok' => 0, 'msg' => $pass_err);
        //echo json_encode($out);
    }

    $err_count = count($error);

    if($err_count == 0){
        //save
        $save = $db->query("INSERT INTO users(names,email,password,phone,gender) 
            VALUES('$names','$email','$password','$phone','$gender')");
        $save->closeCursor();

        $out = array('ok' => 1, 'msg' => "Your account was created successfully, you can now login");
        echo json_encode($out);
    }
    else{
            $m = "<p>Some error occured</p>";

            foreach ($error as $err){
                $m .= $err."<br>";
            }
            $out = array('ok' => 0, 'msg' => $m);

            echo json_encode($out);
            exit();

    }
    exit();
}



if(isset($_POST['login-ok'])){
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = $db->prepare("SELECT id,names FROM users WHERE username = :u AND password = :p");
    $sql->execute(array(
        'u' => $username,
        'p' => $password
    ));
    $rows = $sql->rowCount();

    if($rows == 0){
        $msg = array('ok' => 0, 'msg' => "Invalid login details!");
    }else{
        $rs = $sql->fetch(PDO::FETCH_ASSOC);
        
        $me = $rs['id'];
        //herb lists
        $offence = $db->query("SELECT id,name,fee FROM offence");
        $offence_list = array();
        while($offence_rs = $offence->fetch(PDO::FETCH_ASSOC)){
            $offence_list[] = $offence_rs;
        }
        
        //saved herbs
        
        /*$saved_herbs = $db->query("SELECT saved_herbs.herb_id, herbs.id, herbs.title FROM saved_herbs INNER JOIN herbs ON saved_herbs.herb_id = herbs.id WHERE saved_herbs.user_id = '$me'");
        $saved_herbs_lists = array();

        while($saved_herbs_rs = $saved_herbs->fetch(PDO::FETCH_ASSOC)){            
            $saved_herbs_lists[] = $saved_herbs_rs;
        }*/
        $msg = array('ok' => 1, 'datas' => $rs, 'offence' => $offence_list);
    }
    $sql->closeCursor();

    echo json_encode($msg);
    exit();
}

if(isset($_POST['add-ok'])){
    $offence_id = $_POST['offence_id'];
    $offence_info = $db->prepare("SELECT fee,name FROM offence WHERE id = :id");
    $offence_info->execute(array('id' => $offence_id));

    $offence_rs = $offence_info->fetch(PDO::FETCH_ASSOC);

    $offender_name = $_POST['name'];
    $offence_name = $offence_rs['name'];
    $licence_number = $_POST['licence'];
    $plate_number = $_POST['plate'];
    $user_id = $_POST['user_id'];
    $comment = $_POST['comment'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    $date_added = time();
    $fees = $offence_rs['fee'];
    $image = $_POST['image'];

    $save = $db->prepare("INSERT INTO offenders(offence_id, offender_name, offence_name, licence_number, plate_number, user_id, comment, phone, address, gender, date_added, fees, image) VALUES(:a,:b,:c,:d,:e,:f,:g,:h,:i,:j,:k,:l,:m)");

    $save->execute(array(
        'a' => $offence_id,
        'b' => $offender_name,
        'c' => $offence_name,
        'd' => $licence_number,
        'e' => $plate_number,
        'f' => $user_id,
        'g' => $comment,
        'h' => $phone,
        'i' => $address,
        'j' => $gender,
        'k' => $date_added,
        'l' => $fees,
        'm' => $image
    ));
    $in_id = $db->lastInsertId();

    $code = "TODS-OSUN-".$in_id;
    $offence_info->closeCursor();
    $save->closeCursor();

    $up = $db->query("UPDATE offenders SET code = '$code' WHERE id = '$in_id'");
    $up->closeCursor();

    $url = $config['site_url'];

    $output = $url."/report.pdf?id=".$code;

    $d = array('id' => $code);

 
    $out = array('ok' => 1, 'msg' => "Offender added successfully!", 'codes' => $code, 'htmls' => $output);

    echo json_encode($out);
    exit();
}


if(isset($_GET['offence_details'])){
    $code = $_GET['code'];
    $user_id = $_GET['user_id'];

    $sql = $db->query("SELECT * FROM offenders WHERE code = '$code' AND user_id = '$user_id'");
    $out['total'] = $sql->rowCount();

    $out['record'] = $sql->fetch(PDO::FETCH_ASSOC);
    $sql->closeCursor();
    echo json_encode($out);
}


if(isset($_POST['edit-ok'])){
    $code = $_POST['code'];
    
    
    $offender_name = $_POST['name'];
    $licence_number = $_POST['licence'];
    $plate_number = $_POST['plate'];
    $user_id = $_POST['user_id'];
    $comment = $_POST['comment'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $gender = $_POST['gender'];
    
    $save = $db->prepare("UPDATE offenders SET offender_name = :b, licence_number = :d, plate_number = :e, comment = :g, phone = :h, address = :i, gender = :j WHERE code = :a AND user_id = :f");

    $save->execute(array(
        'a' => $code,
        'b' => $offender_name,
        'd' => $licence_number,
        'e' => $plate_number,
        'f' => $user_id,
        'g' => $comment,
        'h' => $phone,
        'i' => $address,
        'j' => $gender        
    ));
    
    $save->closeCursor();

    
    /*$url = $config['site_url'];

    $output = "$url/report.php?id=$code";*/

    $url = $config['site_url'];

    $output = $url."/report.pdf?id=".$code;

    $d = array('id' => $code);

    //my_curl($d,$url."/reports.php");
    //file_get_contents($url."/reports.php?id=".$code);
    //include_once 'reports.php';
    $out = array('ok' => 1, 'msg' => "Offender edited successfully!", 'codes' => $code, 'htmls' => $output);

    echo json_encode($out);
    exit();
}