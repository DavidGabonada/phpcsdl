<?php
include "headers.php";

class User
{
    function login($json)
    {
        // {"username":"02-2223-08904","password":"IloveXena143"}
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT a.*, b.userLevel_name FROM tbl_scholars a
                INNER JOIN tbl_user_level b ON b.userLevel_privilege = a.stud_user_level
                WHERE (a.stud_school_id = :username OR a.stud_email = :username) AND BINARY a.stud_password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("username", $json["username"]);
        $stmt->bindParam("password", $json["password"]);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return json_encode([
                "stud_id" => $user["stud_id"],
                "stud_school_id" => $user["stud_school_id"],
                "stud_password" => $user["stud_password"],
                "stud_last_name" => $user["stud_last_name"],
                "stud_first_name" => $user["stud_first_name"],
                "stud_year_level" => $user["stud_year_level"],
                "stud_email" => $user["stud_email"],
                "stud_typeScholar_id" => $user["stud_typeScholar_id"],
                "stud_scholarship_type_id" => $user["stud_scholarship_type_id"],
                "stud_scholarship_sub_type_id" => $user["stud_scholarship_sub_type_id"],
                "stud_contact_number" => $user["stud_contact_number"],
                "stud_course_id" => $user["stud_course_id"],
                "user_level" => $user["userLevel_name"]
            ]);
        }
        //{}
        // $sql = "SELECT a.*, b.userLevel_name FROM tbl_admin a
        // INNER JOIN tbl_user_level b ON b.userLevel_privilege = a.adm_user_level
        // WHERE (adm_employee_id = :username OR adm_email = :username) AND BINARY adm_password = :password";
        // $stmt = $conn->prepare($sql);
        // $stmt->bindParam("username", $json["username"]);
        // $stmt->bindParam("password", $json["password"]);
        // $stmt->execute();
        // if ($stmt->rowCount() > 0) {
        //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
        //     return json_encode([
        //         "adm_id" => $user["adm_id"],
        //         "adm_employee_id" => $user["adm_employee_id"],
        //         "adm_password" => $user["adm_password"],
        //         "adm_last_name" => $user["adm_last_name"],
        //         "adm_first_name" => $user["adm_first_name"],
        //         "adm_email" => $user["adm_email"],
        //         "user_level" => $user["userLevel_name"],
        //     ]);
        // }
        $sql = "SELECT a.*, b.userLevel_name FROM tbl_supervisor_master a 
        INNER JOIN tbl_user_level b ON b.userLevel_privilege = a.supM_user_level 
        WHERE (a.supM_employee_id = :username OR a.supM_email = :username) AND BINARY a.supM_password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("username", $json["username"]);
        $stmt->bindParam("password", $json["password"]);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return json_encode([
                "supM_id" => $user["supM_id"],
                "supM_employee_id" => $user["supM_employee_id"],
                "supM_password" => $user["supM_password"],
                "supM_last_name" => $user["supM_last_name"],
                "supM_first_name" => $user["supM_first_name"],
                "supM_email" => $user["supM_email"],
                "user_level" => $user["userLevel_name"],
            ]);
        }
        return 0;
    }

    function adminLogin($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_admin WHERE BINARY adm_email = :username AND BINARY adm_password = :password";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("username", $json["username"]);
        $stmt->bindParam("password", $json["password"]);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return 0;
    }
}
$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$user = new User();
switch ($operation) {
    case "login":
        echo $user->login($json);
        break;
    case "adminLogin":
        echo json_encode($user->adminLogin($json));
        break;
        // case "getAdmin":
        //     echo $user->getAdmin();
        //     break;
        // case "getscholarship_type":
        //     echo $user->getscholarship_type();
        //     break;
        // case "getcourse":
        //     echo $user->getcourse();
        //     break;
        // case "getscholarship_type_list":
        //     echo $user->getscholarship_type_list();
        //     break;
        // case "getSubType":
        //     echo $user->getSubType();
        //     break;
        // case "getschoolyear":
        //     echo $user->getschoolyear();
    default:
        echo "WALAY " . $operation . " NGA OPERATION SA UBOS HAHHAHA BOBO NOYNAY";
        break;
}
