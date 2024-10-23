<?php
include "headers.php";

class User
{
  function addadministrator($json)
  {
    //{"adm_employee_id":01122, "adm_first_name":"bea","adm_last_name":"macario", adm_middle_name:"S", "adm_password":"143llove", "adm_email": "beamacario@gmail.com", "adm_contact_number":"09123456789"}
    include "connection.php";
    $json = json_decode($json, true);
    $password = $json["adm_employee_id"] . substr($json["adm_last_name"], 0, 2);
    $password = substr($json["adm_last_name"], 0, 2) . $json["adm_employee_id"];
    $sql = "INSERT INTO tbl_admin(adm_employee_id, adm_last_name, adm_first_name, adm_middle_name, adm_password, adm_email, adm_contact_number)
    VALUES(:adm_employee_id, :adm_last_name, :adm_first_name, :adm_middle_name, :adm_password, :adm_email, :adm_contact_number)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("adm_employee_id", $json["adm_employee_id"]);
    $stmt->bindParam("adm_last_name", $json["adm_last_name"]);
    $stmt->bindParam("adm_first_name", $json["adm_first_name"]);
    $stmt->bindParam("adm_middle_name", $json["adm_middle_name"]);
    $stmt->bindParam("adm_password", $password);
    $stmt->bindParam("adm_email", $json["adm_email"]);
    $stmt->bindParam("adm_contact_number", $json["adm_contact_number"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addDepartment($json)
  {
    //{"dept_name":"bea"}
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
  function AddScholar($json)
  {
    // {"stud_school_id":"02-2223-08904", "stud_last_name" :"Ignalig", "stud_first_name":"Kitty", "stud_middle_name":"Tanhaga-Doble", "stud_course_id":1,"stud_year_level":3, "stud_scholarship_type_id":9, "stud_scholarship_sub_type_id":1,"stud_contact_number":"0991234533","stud_email":"xenamylove@gmail.com"}
    include "connection.php";
    $json = json_decode($json, true);
    $password = substr($json["stud_last_name"], 0, 2) . $json["stud_school_id"];
    $sql = "INSERT INTO tbl_scholars (
    stud_school_id, stud_last_name, stud_first_name, stud_middle_name, stud_course_id, stud_year_level, stud_scholarship_type_id, stud_scholarship_sub_type_id,
    stud_password, stud_contact_number, stud_email, stud_typeScholar_id) 
    VALUES(:stud_school_id, :stud_last_name, :stud_first_name, :stud_middle_name, :stud_course_id, :stud_year_level, :stud_scholarship_type_id, :stud_scholarship_sub_type_id,
    :stud_password, :stud_contact_number, :stud_email, :stud_typeScholar_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':stud_school_id', $json['stud_school_id']);
    $stmt->bindParam(':stud_last_name', $json['stud_last_name']);
    $stmt->bindParam(':stud_first_name', $json['stud_first_name']);
    $stmt->bindParam(':stud_middle_name', $json['stud_middle_name']);
    $stmt->bindParam(':stud_course_id', $json['stud_course_id']);
    $stmt->bindParam(':stud_year_level', $json['stud_year_level']);
    $stmt->bindParam(':stud_scholarship_type_id', $json['stud_scholarship_type_id']);
    $stmt->bindParam(':stud_scholarship_sub_type_id', $json['stud_scholarship_sub_type_id']);
    $stmt->bindParam(':stud_password', $password);
    $stmt->bindParam(':stud_contact_number', $json['stud_contact_number']);
    $stmt->bindParam(':stud_email', $json['stud_email']);
    $stmt->bindParam(':stud_typeScholar_id', $json['stud_typeScholar_id']);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function addAssignStudent($json)
  {
    //{stud_school_id":02-2021-03668,"stud_firstname":"joe","stud_lastname":"rogan","stud_scholarship_type_id":2}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_scholars(stud_school_id, stud_first_name, stud_last_name, stud_scholarship_type_id)
    VALUES(:stud_school_id, :stud_firstname, :stud_lastname, :stud_scholarship_type_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("stud_school_id", $json["stud_school_id"]);
    $stmt->bindParam("stud_firstname", $json["stud_firstname"]);
    $stmt->bindParam("stud_lastname", $json["stud_lastname"]);
    $stmt->bindParam("stud_scholarship_type_id", $json["stud_scholarship_type_id"]);
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
  function AddBuilding($json)
  {
    //{"build_name":"MS - Main South"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_building(build_name)
      VALUES(:build_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("build_name", $json["build_name"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddRoom($json)
  {
    //{"room_number":"103"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_room(room_number)
    VALUES(:room_number)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("room_number", $json["room_number"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddSubject($json)
  {
    //{"subject_code":"ITE 103", "subject_name": "Quantitative Reasoning"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_subject(subject_code, subject_name)
    VALUES(:subject_code, :subject_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("subject_code", $json["subject_code"]);
    $stmt->bindParam("subject_name", $json["subject_name"]);
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
  function AddModality($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_stud_type_scholars(stypeScholar_name)
    VALUES(:stypeScholar_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("stypeScholar_name", $json["stypeScholar_name"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddSupervisor($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_supervisor(sup_office_id, sup_supM_id, sup_sy, sup_sem)
    VALUES (:sup_office_id, :sup_supM_id, :sup_sy, :sup_sem)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("sup_office_id", $json["sup_office_id"]);
    $stmt->bindParam("sup_supM_id", $json["sup_supM_id"]);
    $stmt->bindParam("sup_sy", $json["sup_sy"]);
    $stmt->bindParam("sup_sem", $json["sup_sem"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddSupervisorMaster($json)
  {
    //{"supM_employee_id": "1", "supM_first_name": "david", "supM_last_name": "gabonada", "supM_middle_name": "candelasa", "supM_department_id": "4", "supM_email": "gabonada@gmail", "supM_contact_number": "02221"}
    include "connection.php";
    $json = json_decode($json, true);
    $password = $json["supM_employee_id"] . substr($json["supM_last_name"], 0, 2);
    $sql = "INSERT INTO tbl_supervisor_master(supM_employee_id, supM_password, supM_first_name, supM_last_name, supM_middle_name, supM_department_id, supM_email, supM_contact_number)
    VALUES (:supM_employee_id, :supM_password, :supM_first_name, :supM_last_name, :supM_middle_name, :supM_department_id, :supM_email, :supM_contact_number)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("supM_employee_id", $json["supM_employee_id"]);
    $stmt->bindParam("supM_password", $password);
    $stmt->bindParam("supM_first_name", $json["supM_first_name"]);
    $stmt->bindParam("supM_last_name", $json["supM_last_name"]);
    $stmt->bindParam("supM_middle_name", $json["supM_middle_name"]);
    $stmt->bindParam("supM_department_id", $json["supM_department_id"]);
    $stmt->bindParam("supM_email", $json["supM_email"]);
    $stmt->bindParam("supM_contact_number", $json["supM_contact_number"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddAssignScholar($json)
  {
    //"assign_stud_id": "7", "assign_supM_id": "7", "assign_build_id": "2", "assign_day_id": "1", "assign_time_schedule_in": "3", "assign_time_schedule_out": "5", assign_room_id": "1", "assign_subject_id": "1", "assign_dutyH_Id": "1"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_assign_scholars(assign_stud_id, assign_supM_id, assign_build_id, assign_day_id, assign_time_schedule_in, assign_time_schedule_out, assign_room_id, assign_subject_id, assign_duty_hours) 
    VALUES (:assign_stud_id, :assign_supM_id, :assign_build_id, :assign_day_id, :assign_time_schedule_in, :assign_time_schedule_out, :assign_room_id, :assign_subject_id, :assign_duty_hours)";

    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":assign_stud_id", $json["assign_stud_id"]);
    $stmt->bindParam(":assign_supM_id", $json["assign_supM_id"]);
    $stmt->bindParam(":assign_build_id", $json["assign_build_id"]);
    $stmt->bindParam(":assign_day_id", $json["assign_day_id"]);
    $stmt->bindParam(":assign_time_schedule_in", $json["assign_time_schedule_in"]);
    $stmt->bindParam(":assign_time_schedule_out", $json["assign_time_schedule_out"]);
    $stmt->bindParam(":assign_room_id", $json["assign_room_id"]);
    $stmt->bindParam(":assign_subject_id", $json["assign_subject_id"]);
    $stmt->bindParam(":assign_duty_hours", $json["assign_duty_hours"]);
    $stmt->execute();

    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function getAdmin()
  {
    include "connection.php";
    $stmt = "SELECT * FROM tbl_admin";
    $stmt = $conn->prepare($stmt);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
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
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
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
  function getTime()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_time_schedule_in";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getTimeOut()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_time_schedule_out";
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
    $returnValue["modality"] = $this->getModality();
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
    $sql = "SELECT * FROM tbl_supervisors";
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
  function getDays()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_day";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getBuilding()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_building";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getRoom()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_room";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getSubject()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_subject";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getModality()
  {
    include "connection.php";
    $sql = "SELECT  * FROM tbl_stud_type_scholars";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getSupervisorMaster()
  {
    include "connection.php";
    $sql = "SELECT a.supM_id, 
    a.supM_employee_id, a.supM_first_name, a.supM_middle_name, a.supM_last_name, b.dept_name, a.supM_email, a.supM_contact_number 
    FROM tbl_supervisor_master a 
    INNER JOIN tbl_departments b ON a.supM_department_id = b.dept_id;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  // function login($json)
  // {
  //   include "connection.php";
  //   $json = json_decode($json, true);
  //   $sql = "SELECT a.adm_id, a.adm_name, a.adm_email, a.adm_password a.adm_user_level FROM tbladmin a
  //           INNER JOIN tbluserlevel b ON a.adm_userLevel = b.userL_id
  //           WHERE adm_email = :username AND BINARY adm_password = :password";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->bindParam(':username', $json['username']);
  //   $stmt->bindParam(':password', $json['password']);
  //   $stmt->execute();

  //   if ($stmt->rowCount() > 0) {
  //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
  //     return json_encode([
  //       'adm_id' => $user['adm_id'],
  //       'adm_user_level' => $user['adm_user_level'],
  //       'adm_name' => $user['adm_name'],
  //       'adm_email' => $user['adm_email']
  //     ]);
  //   }
  //   $sql = "SELECT * FROM tblsupervisor_master WHERE supM_email = :username AND BINARY supM_password = :password";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->bindParam(':username', $json['username']);
  //   $stmt->bindParam(':password', $json['password']);
  //   $stmt->execute();
  //   if ($stmt->rowCount() > 0) {
  //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
  //     return json_encode([
  //       'supM_id' => $user['supM_id'],
  //       'sup_user_level' => $user['sup_user_level'],
  //       'supM_name' => $user['supM_name'],
  //       'supM_email' => $user['supM_email']
  //     ]);
  //   }
  //   $sql = "SELECT a.*, b.* FROM tbl_scholars a

  //   WHERE a.cand_email = :username AND BINARY a.cand_password = :password";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->bindParam(':username', $json['username']);
  //   $stmt->bindParam(':password', $json['password']);
  //   $stmt->execute();
  //   if ($stmt->rowCount() > 0) {
  //     $user = $stmt->fetch(PDO::FETCH_ASSOC);
  //     return json_encode([
  //       'cand_id' => $user['cand_id'],
  //       'cand_firstname' => $user['cand_firstname'],
  //       'cand_lastname' => $user['cand_lastname'],
  //       'cand_email' => $user['cand_email'],
  //       'cand_userLevel' => $user['cand_userLevel']
  //     ]);
  //   }
  //   return json_encode(null);
  // }
  function getDutyAssign()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getScholar"] = $this->getScholar();
    $returnValue["getRoom"] = $this->getRoom();
    $returnValue["getBuilding"] = $this->getBuilding();
    $returnValue["getSupervisorMaster"] = $this->getSupervisorMaster();
    $returnValue["getDays"] = $this->getDays();
    $returnValue["getSubject"] = $this->getSubject();
    $returnValue["getDutyHours"] = $this->getDutyHours();
    $returnValue["getTimeIn"] = $this->getTime();
    $returnValue["getTimeOut"] = $this->getTimeOut();
    return json_encode($returnValue);
  }
  function getAllList()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["adminList"] = $this->getadminList();
    $returnValue["departmentList"] = json_decode($this->getDepartment(), true);
    $returnValue["schoolyearlist"] = $this->getschoolyear();
    $returnValue["courselist"] = $this->getCourseList();
    $returnValue["scholarshiptypelist"] = $this->getscholarship_type_list();
    $returnValue["officeMasterlist"] = $this->getOfficeMaster();
    $returnValue["scholarlist"] = json_decode($this->getScholar(), true);
    $returnValue["supervisorlist"] = $this->getSupervisor();
    $returnValue["scholarsubtype"] = $this->getScholarshipSubType();
    return json_encode($returnValue);
  }
  function getAllScholarList()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["scholarshiptypelist"] = $this->getscholarship_type_list();
    $returnValue["courselist"] = $this->getCourseList();
    $returnValue["SchoolYearLevel"] = $this->getSchoolYearLevel();
    return json_encode($returnValue);
  }
  function getTimeList()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getTime"] = $this->getTime();
    $returnValue["getTimeOut"] = $this->getTimeOut();
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
    try {
      $sql = "DELETE FROM tbl_departments WHERE dept_id = :dept_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":dept_id", $data["dept_id"]);
      $stmt->execute();
      return $stmt->rowCount() > 0 ? 1 : 0;
    } catch (PDOException $e) {
      if ($e->getCode() == 23000) {
        return -1;
      }
      throw $e;
    }
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

  function getStudentsDetailsAndStudentDutyAssign($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    // Updated SQL query to match the new structure
    $sql = "SELECT 
                  a.stud_school_id, 
                  CONCAT(a.stud_first_name, ' ', a.stud_last_name) AS StudentFullname, 
                  e.room_number, 
                  f.build_name, 
                  c.subject_code, 
                  c.subject_name, 
                  CONCAT(d.supM_first_name, ' ', d.supM_last_name) AS AdvisorFullname,
                  CONCAT(g.timeShed_name, ' - ', h.time_out_name) AS DutyTime, 
                  j.dutyH_hours, 
                  i.day_name
              FROM tbl_scholars AS a 
              INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
              INNER JOIN tbl_subject AS c ON c.subject_id = b.assign_subject_id
              INNER JOIN tbl_supervisor_master AS d ON d.supM_id = b.assign_supM_id
              INNER JOIN tbl_room AS e ON e.room_id = b.assign_room_id
              INNER JOIN tbl_building AS f ON f.build_id = b.assign_build_id
              INNER JOIN tbl_time_schedule AS g ON g.timeSched_id = b.assign_time_schedule_in
              INNER JOIN tbl_time_schedule_out AS h ON h.time_out_id = b.assign_time_schedule_out
              INNER JOIN tbl_duty_hours AS j ON j.dutyH_id = b.assign_dutyH_Id
              INNER JOIN tbl_day AS i ON i.day_id = b.assign_day_id
              WHERE b.assign_stud_id = :assign_stud_id";

    // Prepare and execute the SQL query
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':assign_stud_id', $json['assign_stud_id']);
    $stmt->execute();

    // Fetch and return the result as JSON
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? json_encode($result) : 0;
  }

  function getStudentDtr($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    $sql = "SELECT 
                  dtr_date, 
                  TIME(dtr_time_in) AS dtr_time_in, 
                  TIME(dtr_time_out) AS dtr_time_out, 
                  dtr_sy, 
                  dtr_sem, 
                  dutyH_hours, 
                  TIMESTAMPDIFF(SECOND, dtr_time_in, dtr_time_out) AS total_seconds
              FROM 
                  tbl_dtr AS a 
              INNER JOIN 
                  tbl_assign_scholars AS b ON b.assign_id = a.dtr_assign_id
              INNER JOIN 
                  tbl_sy AS c ON c.sy_id = a.dtr_sy
              INNER JOIN 
                  tbl_semester AS d ON d.sem_id = a.dtr_sem
              INNER JOIN 
                  tbl_duty_hours AS e ON e.dutyH_id = b.assign_dutyH_Id
              WHERE 
                  b.assign_stud_id = :assign_stud_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':assign_stud_id', $json['assign_stud_id']);
    $stmt->execute();

    // Fetch all records
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the total_seconds into hours, minutes, and seconds
    foreach ($result as &$row) {
      $totalSeconds = $row['total_seconds'];
      // Calculate hours, minutes, and seconds
      $hours = floor($totalSeconds / 3600);
      $minutes = floor(($totalSeconds % 3600) / 60);
      $seconds = $totalSeconds % 60;

      // Format the time as "HH:MM:SS" in 24-hour format
      $row['dutyH_time'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // Return as JSON: either the result or an empty array
    return json_encode($result);
  }






  function studentsAttendance($json)
  {
    include "connection.php"; // Include your database connection
    $json = json_decode($json, true); // Decode the JSON input
    $scannedID = $json['stud_school_id']; // Get the student ID from the JSON input

    // Get the current year and generate the school year string (e.g., "2024-2025")
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $schoolYear = "$currentYear-$nextYear";

    // Set the default semester
    $semester = 1;

    // Get the current date
    $currentDate = date('Y-m-d');

    // Query to check if the scanned ID exists and get duty_id and today's attendance record
    $sql = "
          SELECT stud_school_id, assign_id, dtr_time_in, dtr_time_out, dtr_id, dtr_date, dutyH_name
          FROM tbl_scholars AS a 
          INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
          LEFT JOIN tbl_dtr AS c ON c.dtr_stud_id = a.stud_id AND c.dtr_date = :currentDate
          INNER JOIN tbl_duty_hours AS d ON d.dutyH_id = b.assign_dutyH_Id
          WHERE a.stud_school_id = :scannedID
      ";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
    $stmt->bindValue(':currentDate', $currentDate, PDO::PARAM_STR); // Bind current date
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
      $dutyAssignId = $result['assign_id']; // Get the duty assignment ID

      // Check if a record exists for today
      if (empty($result['dtr_id'])) {
        // No record exists for today, insert a new attendance record
        $sqlInsert = "
                  INSERT INTO tbl_dtr 
                      (dtr_assign_id, dtr_date, dtr_time_in, dtr_sy, dtr_sem) 
                  VALUES 
                      (:dtr_assign_id, CURDATE(), NOW(), :school_year, :semester)
              ";

        // Prepare the insert statement
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(':dtr_assign_id', $dutyAssignId, PDO::PARAM_INT);
        $stmtInsert->bindValue(':school_year', $schoolYear, PDO::PARAM_STR);
        $stmtInsert->bindValue(':semester', $semester, PDO::PARAM_INT);

        // Execute the insert query
        $stmtInsert->execute();

        // Check if the insert query succeeded
        return $stmtInsert->rowCount() > 0 ? 1 : 0;
      } else {
        // If the record exists for today but is incomplete, update it
        $dtrId = $result['dtr_id'];

        if (empty($result['dtr_time_in'])) {
          // Update to insert dtr_time_in if it's empty
          $sqlUpdate = "
                      UPDATE tbl_dtr 
                      SET dtr_time_in = NOW() 
                      WHERE dtr_id = :dtr_id
                  ";
        } else {
          // Update to insert dtr_time_out if dtr_time_in exists
          $sqlUpdate = "
                      UPDATE tbl_dtr 
                      SET dtr_time_out = NOW() 
                      WHERE dtr_id = :dtr_id
                  ";
        }

        // Prepare and execute the update statement
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bindValue(':dtr_id', $dtrId, PDO::PARAM_INT);
        $stmtUpdate->execute();

        // Check if the update query succeeded
        if ($stmtUpdate->rowCount() > 0) {
          // Now we need to check if both dtr_time_in and dtr_time_out are set for the record
          $sqlCheckTime = "
                      SELECT dtr_time_in, dtr_time_out 
                      FROM tbl_dtr 
                      WHERE dtr_id = :dtr_id
                  ";

          // Prepare and execute the check statement
          $stmtCheckTime = $conn->prepare($sqlCheckTime);
          $stmtCheckTime->bindValue(':dtr_id', $dtrId, PDO::PARAM_INT);
          $stmtCheckTime->execute();
          $timeResult = $stmtCheckTime->fetch(PDO::FETCH_ASSOC);

          if ($timeResult && !empty($timeResult['dtr_time_in']) && !empty($timeResult['dtr_time_out'])) {
            // Calculate total hours, minutes, and seconds worked
            $timeIn = new DateTime($timeResult['dtr_time_in']);
            $timeOut = new DateTime($timeResult['dtr_time_out']);
            $interval = $timeIn->diff($timeOut);

            // Convert the interval to total seconds worked
            $totalWorkedSeconds = ($interval->h * 3600) + ($interval->i * 60) + $interval->s;

            // Update the duty_hours in the tblduty_assign table
            $sqlUpdateDutyHours = "
                          UPDATE tblduty_assign 
                          SET duty_hours = duty_hours - :worked_seconds
                          WHERE duty_id = :duty_assign_id
                      ";

            // Prepare the update for duty hours
            $stmtUpdateDutyHours = $conn->prepare($sqlUpdateDutyHours);
            $stmtUpdateDutyHours->bindValue(':worked_seconds', $totalWorkedSeconds, PDO::PARAM_INT);
            $stmtUpdateDutyHours->bindValue(':duty_assign_id', $dutyAssignId, PDO::PARAM_INT);
            $stmtUpdateDutyHours->execute();

            // Return 1 indicating success
            return 1;
          }

          // If update succeeded but time calculation is not applicable
          return 1;
        }
      }
    }

    // If the scanned ID is not found, return 0
    return 0;
  }
}
function recordExists($value, $table, $column)
{
  include "connection.php";
  $sql = "SELECT COUNT(*) FROM $table WHERE $column = :value";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(":value", $value);
  $stmt->execute();
  $count = $stmt->fetchColumn();
  return $count > 0;
}
$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$user = new User();
switch ($operation) {
    // case "login":
    //   echo $user->login($json);
    //   break;
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
  case "AddScholar":
    echo $user->AddScholar($json);
    break;
  case "addAssignStudent":
    echo $user->addAssignStudent($json);
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
  case "AddBuilding":
    echo $user->AddBuilding($json);
    break;
  case "AddRoom":
    echo $user->AddRoom($json);
    break;
  case "AddSubject":
    echo $user->AddSubject($json);
    break;
  case "AddModality":
    echo $user->AddModality($json);
    break;
  case "AddSupervisor":
    echo $user->AddSupervisor($json);
    break;
  case "AddAssignScholar":
    echo $user->AddAssignScholar($json);
    break;
  case "AddSupervisorMaster":
    echo $user->AddSupervisorMaster($json);
    break;
  case "getAddScholarDropDown":
    echo $user->getAddScholarDropDown();
    break;
  case "getAdmin":
    echo json_encode($user->getAdmin());
    break;
  case "getScholar":
    echo json_encode($user->getScholar());
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
  case "getSupervisorMaster":
    echo json_encode($user->getSupervisorMaster());
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
  case "getTime":
    echo json_encode($user->getTime());
    break;
  case "getTimeOut":
    echo json_encode($user->getTimeOut());
    break;
  case "getTimeList":
    echo $user->getTimeList();
    break;
  case "getSubType":
    echo $user->getSubType();
    break;
  case "getSchoolYearLevel":
    echo json_encode($user->getSchoolYearLevel());
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
  case "getDays":
    echo json_encode($user->getDays());
    break;
  case "getBuilding":
    echo json_encode($user->getBuilding());
    break;
  case "getRoom":
    echo json_encode($user->getRoom());
    break;
  case "getSubject":
    echo json_encode($user->getSubject());
    break;
  case "getDutyAssign":
    echo $user->getDutyAssign();
    break;
  case "getModality":
    echo json_encode($user->getModality());
    break;
  case "getAllList":
    echo $user->getAllList();
    break;
  case "getAllScholarList":
    echo $user->getAllScholarList();
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
  case "deleteScholarshipType":
    echo $user->deleteScholarshipType($json);
    break;
  case "getStudentsDetailsAndStudentDutyAssign":
    echo $user->getStudentsDetailsAndStudentDutyAssign($json);
    break;
  case "getStudentDtr":
    echo $user->getStudentDtr($json);
    break;
  case "studentsAttendance":
    echo $user->studentsAttendance($json);
    break;
  default:
    echo "error";
    break;
}
