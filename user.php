<?php
include "headers.php";

class User
{
    function login($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        // Query for scholars
        $sqlScholars = "SELECT a.*, b.userLevel_name 
            FROM tbl_scholars a
            LEFT JOIN tbl_user_level b 
                ON b.userLevel_privilege = a.stud_user_level
            WHERE (a.stud_id = :username OR a.stud_email = :username)";

        $stmtScholars = $conn->prepare($sqlScholars);
        $stmtScholars->bindParam(":username", $json["username"]);
        $stmtScholars->execute();

        // Check if user exists in scholars table
        if ($stmtScholars->rowCount() > 0) {
            $user = $stmtScholars->fetch(PDO::FETCH_ASSOC);

            // Use password_verify to validate the password
            if (password_verify($json["password"], $user["stud_password"])) {
                
                
                // If password is correct
                $isDefaultPassword = password_verify($user["stud_id"], $user["stud_password"]);
                return json_encode([
                    "stud_id" => $user["stud_id"],
                    "stud_name" => $user["stud_name"],
                    "stud_email" => $user["stud_email"],
                    "stud_user_level" => $user["userLevel_name"],
                    "stud_login_attempts" => $user["stud_login_attempts"],
                    "stud_authentication_status" => $user["stud_authentication_status"],
                    "is_default_password" => $isDefaultPassword
                ]);
            } else {
                // If password is incorrect for an existing user
                return json_encode([
                    "status" => 2, // Incorrect password
                    "stud_login_attempts" => $user["stud_login_attempts"]
                ]);
            }
        }

        // Query for supervisors
        $sqlSupervisors = "SELECT * FROM tbl_supervisors_master WHERE (supM_id = :username OR supM_email = :username)";

        $stmtSupervisors = $conn->prepare($sqlSupervisors);
        $stmtSupervisors->bindParam(":username", $json["username"]);
        $stmtSupervisors->execute();

        // Check if user exists in supervisors table
        if ($stmtSupervisors->rowCount() > 0) {
            $user = $stmtSupervisors->fetch(PDO::FETCH_ASSOC);

            // Use password_verify to validate the password
            if (password_verify($json["password"], $user["supM_password"])) {
                // If password is correct
                $isDefaultPassword = password_verify($user["supM_id"], $user["supM_password"]);
                return json_encode([
                    "supM_id" => $user["supM_id"],
                    "supM_name" => $user["supM_name"],
                    "supM_email" => $user["supM_email"],
                    "supM_login_attempts" => $user["supM_login_attempts"],
                    "supM_password" => $user["supM_password"],
                    "supM_authentication_status" => $user["supM_authentication_status"],
                    "is_default_password" => $isDefaultPassword
                ]);
            } else {
                // If password is incorrect for an existing user
                return json_encode([
                    "status" => 2, // Incorrect password
                    "supM_login_attempts" => $user["supM_login_attempts"]
                ]);
            }
        }

        // Return a specific error message if username doesn't exist in either table
        return 0;
    }


    function adminLogin($json)
    {
        include "connection.php";
    
        $json = json_decode($json, true);
    
        if (!isset($json["username"], $json["password"])) {
            return json_encode(["success" => false, "message" => "Missing credentials"]);
        }
    
        $username = $json["username"];
        $password = $json["password"];
    
        // Modify the SQL query to fetch the user by email only
        $sql = "SELECT * FROM tbl_admin WHERE adm_email = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
    
        // Fetch the user
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verify the password using password_verify
        if ($user && password_verify($password, $user["adm_password"])) {
            return $user; // Return the user if password matches
        }
    
        return 0; // Return 0 if credentials are invalid
    }
    




    function updateImage($json)
    {
        try {
            include "connection.php";

            $json = json_decode($json, true);

            // Check if JSON is valid
            if (!$json) {
                echo json_encode(["error" => "Invalid JSON data"]);
                exit();
            }

            // Upload Image
            $returnValueImage = $this->uploadImage();

            // Check for upload errors
            if (in_array($returnValueImage, [2, 3, 4])) {
                $errorMessages = [
                    2 => "Invalid file type (Allowed: JPG, JPEG, PNG, GIF, WEBP)",
                    3 => "Error uploading the file",
                    4 => "File is too large (Max: 25MB)"
                ];
                echo json_encode(["error" => $errorMessages[$returnValueImage]]);
                exit();
            }

            // Ensure an image was uploaded successfully
            if (empty($returnValueImage)) {
                echo json_encode(["error" => "No image uploaded"]);
                exit();
            }

            // echo "reetrum mvaue " . $returnValueImage;
            // die();

            // Update image filename in DB
            $sql = "UPDATE tbl_admin SET adm_image_filename = :image WHERE adm_id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":image", $returnValueImage);
            $stmt->bindParam(":id", $json["userId"]);
            $stmt->execute();
            return $stmt->rowCount() > 0 ? $returnValueImage : 0;
            // Check if update was successful
            // if ($stmt->rowCount() > 0) {
            //     echo json_encode(["success" => true, "image" => $returnValueImage]);
            // } else {
            //     echo json_encode(["error" => "Database update failed or no changes made"]);
            // }

            exit();
        } catch (\Throwable $th) {
            echo json_encode(["error" => $th->getMessage()]);
            exit();
        }
    }

    function uploadImage()
    {
        if (isset($_FILES["file"])) {
            $file = $_FILES['file'];
            // print_r($file);
            $fileName = $_FILES['file']['name'];
            $fileTmpName = $_FILES['file']['tmp_name'];
            $fileSize = $_FILES['file']['size'];
            $fileError = $_FILES['file']['error'];
            // $fileType = $_FILES['file']['type'];

            $fileExt = explode(".", $fileName);
            $fileActualExt = strtolower(end($fileExt));

            $allowed = ["jpg", "jpeg", "png"];

            if (in_array($fileActualExt, $allowed)) {
                if ($fileError === 0) {
                    if ($fileSize < 25000000) {
                        $fileNameNew = uniqid("", true) . "." . $fileActualExt;
                        $fileDestination =  'images/' . $fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        return $fileNameNew;
                    } else {
                        return 4;
                    }
                } else {
                    return 3;
                }
            } else {
                return 2;
            }
        } else {
            return "";
        }

        // $returnValueImage = uploadImage();

        // switch ($returnValueImage) {
        //     case 2:
        //         // You cannot Upload files of this type!
        //         return 2;
        //     case 3:
        //         // There was an error uploading your file!
        //         return 3;
        //     case 4:
        //         // Your file is too big (25mb maximum)
        //         return 4;
        //     default:
        //         break;
        // }
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
    case "updateImage":
        echo $user->updateImage($json);
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
