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
    $returnValue["getRoom"] = $this->getRoom();
    $returnValue["getBuilding"] = $this->getBuilding();
    $returnValue["getDays"] = $this->getDays();
    $returnValue["getSubject"] = $this->getSubject();
    $returnValue["getDutyHours"] = $this->getDutyHours();
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
  case "AddSupervisorMaster":
    echo $user->AddSupervisorMaster($json);
    break;
  case "getAddScholarDropDown":
    echo $user->getAddScholarDropDown();
    break;
  case "getAdmin":
    echo json_encode($user->getAdmin());
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
  case "deleteScholarshipType";
    echo $user->deleteScholarshipType($json);
    break;
  default:
    echo "WALAY " . $operation . " NGA OPERATION SA UBOS HAHHAHA BOBO NOYNAY";
    break;
}
