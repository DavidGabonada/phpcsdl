<?php
include "headers.php";

class User
{

  function addadministrator($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_admin(adm_employee_id, adm_first_name, adm_last_name, adm_password, adm_email)
    VALUES(1, :adm_first_name, :adm_last_name, :adm_password, :adm_email)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("adm_first_name", $json["adm_first_name"]);
    $stmt->bindParam("adm_last_name", $json["adm_last_name"]);
    $stmt->bindParam("adm_password", $json["adm_password"]);
    $stmt->bindParam("adm_email", $json["adm_email"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addDepartment($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_departments(dept_name)
    VALUES(:dept_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("dept_name", $json["dept_name"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addSchoolyear($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_sy(sy_name)
    VALUES(:sy_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("sy_name", $json["sy_name"]);
    // $stmt->bindParam("sy_status", $json["sy_status"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addCourse($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_course(crs_name, crs_dept_id)
    VALUES(:crs_name, :crs_dept_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("crs_name", $json["crs_name"]);
    $stmt->bindParam("crs_dept_id", $json["crs_dept_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addScholarshipType($json)
  {
    // {"type_name":"bea"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_scholarship_type(type_name)
    VALUES(:type_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("type_name", $json["type_name"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addScholar($json)
  {
    // {"stud_firstname":"joe","stud_lastname":"rogan","stud_year_level":"1st Year","stud_course_id":1,"scholarship_type_id":1,"stud_scholarship_sub_id":2}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_scholars(stud_school_id, stud_first_name, stud_last_name, stud_year_level, stud_scholarship_type_id, stud_scholarship_sub_type_id, stud_course_id) 
    VALUES(:stud_school_id, :stud_firstname, :stud_lastname, :stud_year_level, :scholarship_type_id, :stud_scholarship_sub_id, :stud_course_id)";
    $stmt = $conn->prepare($sql);


    $stmt->bindParam("stud_firstname", $json["stud_firstname"]);
    $stmt->bindParam("stud_lastname", $json["stud_lastname"]);
    $stmt->bindParam("stud_year_level", $json["stud_year_level"]);
    $stmt->bindParam("scholarship_type_id", $json["scholarship_type_id"]);
    $stmt->bindParam("stud_scholarship_sub_id", $json["stud_scholarship_sub_id"]);
    $stmt->bindParam("stud_course_id", $json["stud_course_id"]);
    $stmt->bindParam("stud_school_id", $json["stud_school_id"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addSchoolar_sub_type($json)

  {
    //{"stype_type_id":1, "stype_name":"bea", "stype_dutyhours_id":1}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_scholarship_sub_type(stype_type_id, stype_name, stype_dutyhours_id)
    VALUES(:stype_type_id, :stype_name, :stype_dutyhours_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("stype_type_id", $json["stype_type_id"]);
    $stmt->bindParam("stype_name", $json["stype_name"]);
    $stmt->bindParam("stype_dutyhours_id", $json["stype_dutyhours_id"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addOfficeMaster($json)
  {
    //{"off_subject":"bea", "off_descriptive_title": "bea", "off_section": "bea", "off_room": "bea", "off_type_id": 1, off_timeIn: 1, off_timeOut: 1, off_dayRemote: "wednesday", off_remoteTimeIn: 1, off_remoteTimeOut: 1}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_office_master(off_subject_code, off_descriptive_title, off_section, off_room, off_type_id, off_timeIn, off_timeOut, off_dayRemote, off_remoteTimeIn, off_remoteTimeOut)
    VALUES(:off_subject_code, :off_descriptive_title, :off_section, :off_room, :off_type_id, :off_timeIn, :off_timeOut, :off_dayRemote, :off_remoteTimeIn, :off_remoteTimeOut)";
    $stmt = $conn->prepare($sql);
    // $stmt->bindParam("off_name", $json["off_name"]);
    $stmt->bindParam("off_subject_code", $json["off_subject_code"]);
    $stmt->bindParam("off_descriptive_title", $json["off_descriptive_title"]);
    $stmt->bindParam("off_section", $json["off_section"]);
    $stmt->bindParam("off_room", $json["off_room"]);
    $stmt->bindParam("off_type_id", $json["off_type_id"]);
    $stmt->bindParam("off_timeIn", $json["off_timeIn"]);
    $stmt->bindParam("off_timeOut", $json["off_timeOut"]);
    $stmt->bindParam("off_dayRemote", $json["off_dayRemote"]);
    $stmt->bindParam("off_remoteTimeIn", $json["off_remoteTimeIn"]);
    $stmt->bindParam("off_remoteTimeOut", $json["off_remoteTimeOut"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }


  function getscholarship_type()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_type WHERE type_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }

  function getcourse()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_course WHERE crs_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }

  function getscholarship_type_list()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }
  function getSubType()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_sub_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }

  function getschoolyear()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_sy WHERE sy_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getScholar()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholars";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
  }

  function getSchoolYearLevel()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_school_year_level";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getDutyHours()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_duty_hours";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getsublist()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getDutyHours"] = $this->getDutyHours();
    $returnValue["scholarshipType"] = $this->getscholarship_type();
    return json_encode($returnValue);
  }

  function getAddScholarDropDown()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["yearLevel"] = $this->getSchoolYearLevel();
    $returnValue["course"] = $this->getcourse();
    $returnValue["scholarshipType"] = $this->getscholarship_type();
    $returnValue["scholarshipSub"] = $this->getSubType();

    return json_encode($returnValue);
  }

  function getadminList()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_admin";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getCourseList()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_course";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getDepartment()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_departments WHERE dept_status = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? json_encode($stmt->fetchAll(PDO::FETCH_ASSOC)) : 0;
  }
  function getScholarTypeList()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getSchoolYearList()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_sy";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getOfficeMaster()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_office_master";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getSupervisor()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_office_supervisors";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getScholarshipSubType()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_sub_type WHERE stype_id = 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }


  function getAllList()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["adminList"] = $this->getadminList();
    $returnValue["departmentList"] = $this->getDepartment();
    $returnValue["schoolyearlist"] = $this->getschoolyear();
    $returnValue["courselist"] = $this->getCourseList();
    $returnValue["scholarshiptypelist"] = $this->getscholarship_type_list();
    $returnValue["officeMasterlist"] = $this->getOfficeMaster();
    $returnValue["scholarlist"] = $this->getScholar();
    $returnValue["supervisorlist"] = $this->getSupervisor();
    $returnValue["scholarsubtype"] = $this->getScholarshipSubType();
    return json_encode($returnValue);
  }

  function updateAdmin($json)
  {
    //{"adm_employee_id":1, "adm_first_name":"bea","adm_last_name":"macario", "adm_password":"143llove", "adm_email": "beamacario@gmail.com", "adm_id":1}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_admin SET adm_employee_id = :adm_employee_id, adm_first_name = :adm_first_name, adm_last_name = :adm_last_name, adm_password = :adm_password, adm_email = :adm_email WHERE adm_id = :adm_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":adm_employee_id", $data["adm_employee_id"]);
    $stmt->bindParam(":adm_first_name", $data["adm_first_name"]);
    $stmt->bindParam(":adm_last_name", $data["adm_last_name"]);
    $stmt->bindParam(":adm_password", $data["adm_password"]);
    $stmt->bindParam(":adm_email", $data["adm_email"]);
    $stmt->bindParam(":adm_id", $data["adm_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function updateDerpartment($json)
  {
    //{"dep_name":"bea","dep_id":2}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_departments SET dept_name = :dept_name WHERE dept_id = :dept_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":dept_name", $data["dept_name"]);
    $stmt->bindParam(":dept_id", $data["dept_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }


  function updateSchoolYear($json)
  {
    //{"sy_name":"bea","sy_id":2}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_sy SET sy_name = :sy_name WHERE sy_id = :sy_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sy_name", $data["sy_name"]);
    $stmt->bindParam(":sy_id", $data["sy_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function updateCourse($json)
  {
    //{"crs_name":"bea","crs_dept_id":2, "crs_id":1}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_course SET crs_name = :crs_name, crs_dept_id = :crs_dept_id WHERE crs_id = :crs_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":crs_name", $data["crs_name"]);
    $stmt->bindParam(":crs_dept_id", $data["crs_dept_id"]);
    $stmt->bindParam(":crs_id", $data["crs_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function updateScholarshipType($json)
  {

    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_scholarship_type SET type_name = :type_name WHERE type_id = :type_id";
    $stsmt->bindParam(":type_name", $data["type_name"]);
    $stmt->bindParam(":type_id", $data["type_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function updateOfficeMaster($json)
  {
    //{"off_name":"bea", "off_subject_code": "bea", "off_descriptive_title": "bea", off_section: "bea", "off_room": "bea", "off_type_id": 1, "off_timeIn": 1, "off_timeOut": 1, "off_dayRemote": "wednesday", "off_remoteTimeIn": 1, "off_remoteTimeOut": 1, "off_id": 1792}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_office_master SET off_name = :off_name, off_subject_code = :off_subject_code, off_descriptive_title = :off_descriptive_title, 
    off_section = :off_section, off_room = :off_room, off_type_id = :off_type_id, off_timeIn = :off_timeIn, off_timeOut = :off_timeOut, 
    off_dayRemote = :off_dayRemote, off_remoteTimeIn = :off_remoteTimeIn, off_remoteTimeOut = :off_remoteTimeOut WHERE off_id = :off_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":off_name", $data["off_name"]);
    $stmt->bindParam(":off_subject_code", $data["off_subject_code"]);
    $stmt->bindParam(":off_descriptive_title", $data["off_descriptive_title"]);
    $stmt->bindParam(":off_section", $data["off_section"]);
    $stmt->bindParam(":off_room", $data["off_room"]);
    $stmt->bindParam(":off_type_id", $data["off_type_id"]);
    $stmt->bindParam(":off_timeIn", $data["off_timeIn"]);
    $stmt->bindParam(":off_timeOut", $data["off_timeOut"]);
    $stmt->bindParam(":off_dayRemote", $data["off_dayRemote"]);
    $stmt->bindParam(":off_remoteTimeIn", $data["off_remoteTimeIn"]);
    $stmt->bindParam(":off_remoteTimeOut", $data["off_remoteTimeOut"]);
    $stmt->bindParam(":off_id", $data["off_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function updateScholar($json)
  {
    //{"stud_school_id":1, "stud_password" :"beamell", "stud_last_name":"bea", "stud_first_name":"bea", "stud_year_level":1, "stud_scholarship_type_id":1, "stud_scholarship_sub_type_id":1, "stud_course_id":1, "stud_id":1}
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_scholars SET stud_school_id = :stud_school_id, stud_password = :stud_password, stud_last_name = :stud_last_name, 
    stud_first_name = :stud_first_name, stud_year_level = :stud_year_level, stud_scholarship_type_id = :stud_scholarship_type_id, 
    stud_scholarship_sub_type_id = :stud_scholarship_sub_type_id, stud_course_id = :stud_course_id WHERE stud_id  = :stud_id ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":stud_school_id", $data["stud_school_id"]);
    $stmt->bindParam(":stud_password", $data["stud_password"]);
    $stmt->bindParam(":stud_last_name", $data["stud_last_name"]);
    $stmt->bindParam(":stud_first_name", $data["stud_first_name"]);
    $stmt->bindParam(":stud_year_level", $data["stud_year_level"]);
    $stmt->bindParam(":stud_scholarship_type_id", $data["stud_scholarship_type_id"]);
    $stmt->bindParam(":stud_scholarship_sub_type_id", $data["stud_scholarship_sub_type_id"]);
    $stmt->bindParam(":stud_course_id", $data["stud_course_id"]);
    $stmt->bindParam(":stud_id", $data["stud_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function deleteDepartment($json)
  {
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_departments SET dept_status = 0 WHERE dept_id = :dept_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":dept_id", $data["dept_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function deleteSchoolYear($json)
  {
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_sy SET sy_status = 0 WHERE sy_id = :sy_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":sy_id", $data["sy_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function deleteCourse($json)
  {
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_course SET crs_status = 0 WHERE crs_id = :crs_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":crs_id", $data["crs_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function deleteScholarshipSub($json)
  {
    include "connection.php";
    $data = json_decode($jjson, true);
    $sql = "UPDATE tbl_scholarship_sub_type SET stype_status = 0 WHERE stype_id = :stype_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":stype_id", $data["stype_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function deleteScholarshipType($json)
  {
    include "connection.php";
    $data = json_decode($json, true);
    $sql = "UPDATE tbl_scholarship_type SET type_status = 0 WHERE type_id = :type_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":type_id", $data["type_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";

$user = new User();

switch ($operation) {

  case "addDepartment":
    echo $user->addDepartment($json);
    break;

  case "addSchoolyear":
    echo $user->addSchoolyear($json);
    break;

  case "addCourse":
    echo $user->addCourse($json);
    break;

  case "addScholarshipType":
    echo $user->addScholarshipType($json);
    break;

  case "addScholar":
    echo $user->addScholar($json);
    break;

  case "addSchoolar_sub_type":
    echo $user->addSchoolar_sub_type($json);
    break;

  case "addOfficeMaster":
    echo $user->addOfficeMaster($json);
    break;

  case "addadministrator":
    echo $user->addadministrator($json);
    break;

  case "getAddScholarDropDown":
    echo $user->getAddScholarDropDown();
    break;

  case "getscholarship_type":
    echo json_encode($user->getscholarship_type());
    break;

  case "getschoolyear":
    echo json_encode($user->getschoolyear());
    break;

  case "getSupervisor":
    echo $user->getSupervisor();
    break;

  case "getScholarshipSubType":
    echo $user->getScholarshipSubType();
    break;

  case "getAdminList":
    echo $user->getadminList();
    break;

  case "getCourseList":
    echo $user->getCourseList();
    break;

  case "getDutyHours":
    echo json_encode($user->getDutyHours());
    break;

  case "getSchoolYearList":
    echo $user->getSchoolYearList();
    break;

  case "getDepartment":
    echo $user->getDepartment();
    break;

  case "getcourse":
    echo json_encode($user->getcourse());
    break;

  case "getSubType":
    echo $user->getSubType();
    break;

  case "getSchoolYearLevel":
    echo $user->getSchoolYearLevel();
    break;

  case "getDepartment":
    echo $user->getDepartment();
    break;

  case "getOfficeMaster":
    echo $user->getOfficeMaster();
    break;

  case "getScholarTypeList":
    echo $user->getScholarTypeList();
    break;

  case "getscholarship_type_list":
    echo $user->getscholarship_type_list();
    break;

  case "getsublist":
    echo $user->getsublist();
    break;

  case "getAllList":
    echo $user->getAllList();
    break;

  case "updateAdmin":
    echo $user->updateAdmin($json);
    break;

  case "updateDerpartment":
    echo $user->updateDerpartment($json);
    break;

  case "updateSchoolYear":
    echo $user->updateSchoolYear($json);
    break;

  case "updateCourse":
    echo $user->updateCourse($json);
    break;

  case "updateScholarshipType":
    echo $user->updateScholarshipType($json);
    break;

  case "updateOfficeMaster":
    echo $user->updateOfficeMaster($json);
    break;

  case "updateScholar":
    echo $user->updateScholar($json);
    break;

  case "deleteDepartment":
    echo $user->deleteDepartment($json);
    break;

  case "deleteSchoolYear":
    echo $user->deleteSchoolYear($json);
    break;

  case "deleteCourse":
    echo $user->deleteCourse($json);
    break;

  case "deleteScholarshipSub":
    echo $user->deleteScholarshipSub($json);
    break;

  case "deleteScholarshipType";
    echo $user->deleteScholarshipType($json);
    break;

  default:
    echo "WALAY " . $operation . " NGA OPERATION SA UBOS HAHHAHA BOBO NOYNAY";
    break;
}
