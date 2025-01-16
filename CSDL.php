<?php
include "headers.php";

class User
{
  //UPDATED
  function addadministrator($json)
  {

    include "connection.php";
    $json = json_decode($json, true);
    $password = $json["adm_employee_id"] . substr($json["adm_last_name"], 0, 2);
    $password = substr($json["adm_last_name"], 0, 2) . $json["adm_employee_id"];
    $sql = "INSERT INTO tbl_admin(adm_name, adm_middle_name, adm_password, adm_email, adm_image_filename, adm_user_level)
    VALUES(:adm_name, :adm_password, :adm_email, :adm_image_filename, :adm_user_level)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("adm_name", $json["adm_name"]);
    $stmt->bindParam("adm_password", $password);
    $stmt->bindParam("adm_email", $json["adm_email"]);
    $stmt->bindParam("adm_image_filename", $json["adm_image_filename"]);
    $stmt->bindParam("adm_user_level", $json["adm_user_level"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addDepartment($json)
  {
    //{"dept_name":"bea"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_department(dept_name)
    VALUES(:dept_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("dept_name", $json["dept_name"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addSchoolyear($json)
  {
    //
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_year(year_name)
    VALUES(:year_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("year_name", $json["year_name"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addCourse($json)
  {
    //{"course_name":"bea", "couse_dept_id":1}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_course(course_name, course_dept_id)
    VALUES(:course_name, :course_dept_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("course_name", $json["course_name"]);
    $stmt->bindParam("course_dept_id", $json["course_dept_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addScholarshipType($json)
  {
    // {"type_name":"bea"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_scholarship_type(type_name, type_percent_id)
    VALUES(:type_name, type_percent_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("type_name", $json["type_name"]);
    $stmt->bindParam("type_percent_id", $json["type_percent_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function AddSubject($json)
  {
    //{"subject_code":"ITE 103", "subject_name": "Quantitative Reasoning"}
    include "connection.php";
    $subject = json_decode($json, true);
    $sql = "INSERT INTO tbl_subjects(sub_code, sub_descriptive_title, sub_section, sub_day_f2f_id, 
    sub_time, sub_day_rc_id, sub_time_rc, sub_room)
    VALUES(:sub_code, :sub_descriptive_title, :sub_section, :sub_day_f2f_id, :sub_time,
     :sub_day_rc_id, :sub_time_rc, :sub_room)";
    $stmt = $conn->prepare($sql);

    foreach ($subject as $subject) {
      try {

        $stmt->bindParam("sub_code", $subject["sub_code"], PDO::PARAM_STR);
        $stmt->bindParam("sub_descriptive_title", $subject["sub_descriptive_title"], PDO::PARAM_STR);
        $stmt->bindParam("sub_section", $subject["sub_section"], PDO::PARAM_STR);
        $stmt->bindParam("sub_day_f2f_id", $subject["sub_day_f2f_id"], PDO::PARAM_INT);
        $stmt->bindParam("sub_time", $subject["sub_time"], PDO::PARAM_STR);
        $stmt->bindParam("sub_day_rc_id", $subject["sub_day_rc_id"], PDO::PARAM_INT);
        $stmt->bindParam("sub_time_rc", $subject["sub_time_rc"], PDO::PARAM_STR);
        $stmt->bindParam("sub_room", $subject["sub_room"], PDO::PARAM_STR);
        // $stmt->bindParam("sub_supM_id", $subject["sub_supM_id"], PDO::PARAM_INT);
        // $stmt->bindParam("sub_learning_modalities_id", $subject["sub_learning_modalities_id"], PDO::PARAM_INT);
        // $stmt->bindParam("sub_limit", $subject["sub_limit"], PDO::PARAM_INT);
        $stmt->execute();
      } catch (Exception $e) {
        return $e;
      }
    }

    return 1;
  }
  function AddScholar($json)
  {
    include "connection.php";
    $students = json_decode($json, true);

    // Correct SQL query
    $sql = "INSERT INTO tbl_scholars(
          stud_id, stud_academic_session_id, stud_name, stud_scholarship_id, 
          stud_department_id, stud_course_id, stud_year_id, stud_status_id, 
          stud_percent_id, stud_amount, stud_applied_on_tuition, stud_applied_on_misc, 
          stud_date, stud_modified_by, stud_modified_date, stud_password, 
          stud_image_filename, stud_contactNumber, stud_email
      ) VALUES (
          :stud_id, :stud_academic_session_id, :stud_name, :stud_scholarship_id, 
          :stud_department_id, :stud_course_id, :stud_year_id, :stud_status_id, 
          :stud_percent_id, :stud_amount, :stud_applied_on_tuition, :stud_applied_on_misc, 
          :stud_date, :stud_modified_by, :stud_modified_date, :stud_password, 
          :stud_image_filename, :stud_contactNumber, :stud_email
      )";

    // Prepare statement
    $stmt = $conn->prepare($sql);

    foreach ($students as $student) {
      try {
        // Generate password
        $password = $student["stud_id"] . "123";
        // Bind parameters
        $stmt->bindParam(":stud_id", $student["stud_id"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_academic_session_id", $student["stud_academic_session_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_name", $student["stud_name"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_scholarship_id", $student["stud_scholarship_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_department_id", $student["stud_department_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_course_id", $student["stud_course_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_year_id", $student["stud_year_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_status_id", $student["stud_status_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_percent_id", $student["stud_percent_id"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_amount", $student["stud_amount"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_applied_on_tuition", $student["stud_applied_on_tuition"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_applied_on_misc", $student["stud_applied_on_misc"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_date", $student["stud_date"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_modified_by", $student["stud_modified_by"], PDO::PARAM_INT);
        $stmt->bindParam(":stud_modified_date", $student["stud_modified_date"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_password", $password, PDO::PARAM_STR);
        $stmt->bindParam(":stud_image_filename", $student["stud_image_filename"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_contactNumber", $student["stud_contactNumber"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_email", $student["stud_email"], PDO::PARAM_STR);

        // Execute statement
        $stmt->execute();
      } catch (Exception $e) {
        return 0;
      }
    }

    return 1;
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
    //{"room_number":"103", "room_building_id":1}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_room(room_name, room_build_id)
    VALUES(:room_name, :room_build_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("room_name", $json["room_name"]);
    $stmt->bindParam("room_build_id", $json["room_build_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }


  function AddPercentStype($json)
  {
    //{"pstype_stype_id":1, "pstype_percent": 50}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_percent_stype(percent_name)
    VALUES(:pstype_stype_id, :pstype_percent)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("percent_name", $json["percent_name"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function addOfficeMaster($json)
  {
    //{"off_subject":"bea", "off_descriptive_title": "bea", "off_section": "bea", "off_room": "bea", "off_type_id": 1, off_timeIn: 1, off_timeOut: 1, off_dayRemote: "wednesday", off_remoteTimeIn: 1, off_remoteTimeOut: 1}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_office_master(off_name, off_subject_id, off_section, off_type_id, off_room_id)
    VALUES(:off_name, :off_subject_id, :off_section, :off_type_id, :off_room_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("off_name", $json["off_name"]);
    $stmt->bindParam("off_subject_id", $json["off_subject_id"]);
    $stmt->bindParam("off_section", $json["off_section"]);
    $stmt->bindParam("off_type_id", $json["off_type_id"]);
    $stmt->bindParam("off_room_id", $json["off_room_id"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddOfficeType($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_office_type(off_name, off_building_id)
    VALUES(:off_name, :off_building_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("off_name", $json["off_name"]);
    $stmt->bindParam("off_building_id", $json["off_building_id"]);
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
    $sql = "INSERT INTO tbl_supervisors(sup_office_id, sup_academic_session_id, sup_supM_id)
    VALUES (:sup_office_id, :sup_supM_id, :sup_academic_session_id, sup_supM_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("sup_office_id", $json["sup_office_id"]);
    $stmt->bindParam("sup_academic_session_id", $json["sup_academic_session_id"]);
    $stmt->bindParam("sup_supM_id", $json["sup_supM_id"]);

    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddSupervisorMaster($json)
  {
    //{"supM_employee_id": "1", "supM_first_name": "david", "supM_last_name": "gabonada", "supM_middle_name": "candelasa", "supM_department_id": "4", "supM_email": "gabonada@gmail", "supM_contact_number": "02221"}
    include "connection.php";
    $json = json_decode($json, true);
    $password = $json["supM_name"];
    $sql = "INSERT INTO tbl_supervisors_master(supM_name, supM_password, supM_email, supM_image_filename)
    VALUES (:supM_name, :supM_password, :supM_email, :supM_image_filename)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("supM_name", $json["supM_name"]);
    $stmt->bindParam("supM_password", $password);
    $stmt->bindParam("supM_email", $json["supM_email"]);
    $stmt->bindParam("supM_image_filename", $json["supM_image_filename"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  function AddAssignScholar($json)
  {
    //"assign_stud_id": "7", "assign_supM_id": "7", "assign_build_id": "2", "assign_day_id": "1", "assign_time_schedule_in": "3", "assign_time_schedule_out": "5", assign_room_id": "1", "assign_subject_id": "1", "assign_dutyH_Id": "1"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_assign_scholars(assign_stud_id, assign_duty_hours_id, assign_office_id, assign_session_id, assign_render_status) 
    VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_session_id, :assign_render_status)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam("assign_stud_id", $json["assign_stud_id"]);
    $stmt->bindParam("assign_duty_hours_id", $json["assign_duty_hours_id"]);
    $stmt->bindParam("assign_office_id", $json["assign_office_id"]);
    $stmt->bindParam("assign_session_id", $json["assign_session_id"]);
    $stmt->bindParam("assign_render_status", $json["assign_render_status"]);
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  // function AddOfficeMasterSubCodeAndAssignScholars($jsonArray)
  // {
  //   include "connection.php";
  //   $json = json_decode($jsonArray, true);
  //   $conn->beginTransaction();
  //   try {
  //     //{"offT_id": "1", "offT_dept_id": "1", "offT_build_id": "1", "offT_day_id": "1", "offT_time": "10:30", "offT_supM_id": "02-1213-00123"}

  //     $sql = "INSERT INTO tbl_office_type(offT_id , offT_dept_id, offT_build_id, offT_day_id, offT_time, offT_supM_id)
  //     VALUES (:offT_id, :offT_dept_id, offT_build_id, :offT_day_id, :offT_time, :offT_supM_id)";

  //     $stmt = $conn->prepare($sql);
  //     // $stmt->bindParam("offT_id", $json["offT_id"]);
  //     $stmt->bindParam("offT_dept_id", $json["offT_dept_id"]);
  //     $stmt->bindParam("offT_build_id", $json["offT_build_id"]);
  //     $stmt->bindParam("offT_day_id", $json["offT_day_id"]);
  //     $stmt->bindParam("offT_time", $json["offT_time"]);
  //     $stmt->bindParam("offT_supM_id", $json["offT_supM_id"]);
  //     $stmt->execute();
  //     $lastId = $conn->lastInsertId();

  //     if ($stmt->rowCount() > 0) {
  //       $sql = "INSERT INTO tbl_office_master(off_id, off_type_id, off_subject_id)
  //       VALUES (:off_id, :off_type_id, :off_subject_id)";

  //       $stmt = $conn->prepare($sql);
  //       $stmt->bindParam("off_id", $lastId);
  //       $stmt->bindParam("off_type_id", $json["off_type_id"]);
  //       $stmt->bindParam("off_subject_id", $json["off_subject_id"]);
  //       $stmt->execute();
  //       $lastId = $conn->lastInsertId();

  //       if ($stmt->rowCount() > 0) {
  //         $sql = "INSERT INTO tbl_assign_scholars(assign_stud_id, assign_duty_hours_id, assign_office_id, assign_session_id, assign_render_status)
  //         VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_session_id, :assign_render_status)";
  //         $stmt = $conn->prepare($sql);
  //         $stmt->bindParam("assign_stud_id", $json["assign_stud_id"]);
  //         $stmt->bindParam("assign_duty_hours_id", $json["assign_duty_hours_id"]);
  //         $stmt->bindParam("assign_office_id", $lastId);
  //         $stmt->bindParam("assign_session_id", $json["assign_session_id"]);
  //         $stmt->bindParam("assign_render_status", $json["assign_render_status"]);
  //         $stmt->execute();
  //         $lastId = $conn->lastInsertId();
  //       }
  //     }
  //   } catch (Exception $e) {
  //     $conn->rollBack();
  //     return $e;
  //   }
  //   $conn->commit();
  //   return 1;
  // }



  function AddOfficeMasterSubCodeAndAssignScholars($jsonArray)
  {
    include "connection.php";
    $json = json_decode($jsonArray, true);
    $conn->beginTransaction();

    try {
      // {    "offT_dept_id": 1,    "off_build_id": 2,    "offT_day_id": 3,    "offT_time": "08:00AM-05:00PM",    "offT_supM_id": "02-1213-00123",    "off_subject_id": 5,     "assign_stud_id": "02-2223-03766",    "assign_duty_hours_id": 2,    "assign_session_id": 1,    "assign_render_status": 0}
      $sql = "INSERT INTO tbl_office_type(offT_dept_id, off_build_id, offT_day_id, offT_time, offT_supM_id)
                VALUES (:offT_dept_id, :off_build_id, :offT_day_id, :offT_time, :offT_supM_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":offT_dept_id", $json["offT_dept_id"]);
      $stmt->bindParam(":off_build_id", $json["off_build_id"]);
      $stmt->bindParam(":offT_day_id", $json["offT_day_id"]);
      $stmt->bindParam(":offT_time", $json["offT_time"]);
      $stmt->bindParam(":offT_supM_id", $json["offT_supM_id"]);
      $stmt->execute();

      $lastOfficeTypeId = $conn->lastInsertId();

      // Insert into tbl_office_master
      $sql = "INSERT INTO tbl_office_master(off_type_id, off_subject_id)
                VALUES (:off_type_id, :off_subject_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":off_type_id", $lastOfficeTypeId);

      // Handle NULL for off_subject_id
      $offSubjectId = isset($json["off_subject_id"]) && $json["off_subject_id"] !== "" ? $json["off_subject_id"] : null;
      $stmt->bindValue(":off_subject_id", $offSubjectId, $offSubjectId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
      $stmt->execute();
      $lastOfficeMasterId = $conn->lastInsertId();

      // Insert into tbl_assign_scholars
      $sql = "INSERT INTO tbl_assign_scholars(assign_stud_id, assign_duty_hours_id, assign_office_id, assign_session_id, assign_render_status)
                VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_session_id, :assign_render_status)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":assign_stud_id", $json["assign_stud_id"]);
      $stmt->bindParam(":assign_duty_hours_id", $json["assign_duty_hours_id"]);
      $stmt->bindParam(":assign_office_id", $lastOfficeMasterId);
      $stmt->bindParam(":assign_session_id", $json["assign_session_id"]);
      $stmt->bindParam(":assign_render_status", $json["assign_render_status"]);
      $stmt->execute();

      $conn->commit();
      return json_encode(["status" => "success", "message" => "Records successfully inserted."]);
    } catch (Exception $e) {
      $conn->rollBack();
      return json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
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
    $sql = "SELECT * FROM tbl_course";
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
    $sql = "SELECT * FROM tbl_year";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getScholar()
  {
    include "connection.php";
    $sql = "SELECT a.*, b.session_name FROM tbl_scholars a
    INNER JOIN tbl_academic_session b ON a.stud_academic_session_id = b.session_id";
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
  function getAssignScholar()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getScholar"] = $this->getScholar();
    $returnValue["getDutyHours"] = $this->getDutyHours();
    $returnValue["getOfficeMaster"] = $this->getOfficeMaster();
    $returnValue["getOfficeType"] = $this->getOfficeType();
    $returnValue["getAssignmentMode"] = $this->getAssignmentMode();
    $returnValue["getDepartment"] = $this->getDepartment();
    $returnValue["getSubject"] = $this->getSubject();
    $returnValue["getDays"] = $this->getDays();
    $returnValue["getBuilding"] = $this->getBuilding();
    $returnValue["getSupervisor"] = $this->getRoom();
    $returnValue["getSupervisorMaster"] = $this->getSupervisorMaster();
    $returnValue["getAcademicSession"] = $this->getAcademicSession();

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
    $sql = "SELECT * FROM tbl_department";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getScholarTypeList()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getAssignmentMode()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_assignment_mode";
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
  function getPercentStype()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_percent_stype";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getJobType()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_job_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getSubject()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_subjects";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getAcademicSession()
  {
    include "connection.php";
    $sql = "SELECT a.session_name, b.stud_name FROM tbl_academic_session a
    INNER JOIN tbl_scholars b ON a.session_id = b.stud_academic_session_id";
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
  function getOfficeType()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_office_type";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getStudStatus()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_studstatus";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getSupervisorMaster()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_supervisors_master";
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
  function getScholarlist()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getPercentStype"] = $this->getPercentStype();
    $returnValue["getScholarTypeList"] = $this->getScholarTypeList();
    $returnValue["getschoolyear"] = $this->getschoolyear();
    $returnValue["getStudStatus"] = $this->getStudStatus();
    $returnValue["getAcademicSession"] = $this->getAcademicSession();
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
                  b.assign_duty_hours, 
                  i.day_name
              FROM tbl_scholars AS a 
              INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
              INNER JOIN tbl_subject AS c ON c.subject_id = b.assign_subject_id
              INNER JOIN tbl_supervisor_master AS d ON d.supM_id = b.assign_supM_id
              INNER JOIN tbl_room AS e ON e.room_id = b.assign_room_id
              INNER JOIN tbl_building AS f ON f.build_id = b.assign_build_id
              INNER JOIN tbl_time_schedule_in AS g ON g.timeSched_id = b.assign_time_schedule_in
              INNER JOIN tbl_time_schedule_out AS h ON h.time_out_id = b.assign_time_schedule_out
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
    include "connection.php"; // Include your database connection
    $json = json_decode($json, true); // Decode the JSON input

    // SQL query to fetch attendance records
    $sql = "
          SELECT 
              dtr_date, 
              TIME(dtr_time_in) AS dtr_time_in, 
              TIME(dtr_time_out) AS dtr_time_out, 
              dtr_school_year, 
              dtr_semester, 
              assign_duty_hours, 
              TIMESTAMPDIFF(SECOND, dtr_time_in, dtr_time_out) AS total_seconds
          FROM 
              tbl_dtr AS a 
          INNER JOIN 
              tbl_assign_scholars AS b ON b.assign_id = a.dtr_assign_id
          INNER JOIN 
              tbl_sy AS c ON c.sy_id = a.dtr_school_year
          INNER JOIN 
              tbl_semester AS d ON d.sem_id = a.dtr_semester
          WHERE 
              b.assign_stud_id = :assign_stud_id
      ";

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

    // Return as JSON
    return json_encode($result);
  }


  function studentsAttendance($json)
  {
    include "connection.php"; // Include your database connection
    $json = json_decode($json, true); // Decode the JSON input
    $scannedID = $json['stud_school_id']; // Get the student ID from the JSON input

    // Get the current year and generate school year string (e.g., "2024-2025")
    $currentYear = date('Y');
    $nextYear = $currentYear + 1;
    $schoolYear = "$currentYear-$nextYear";

    // Set the default semester
    $semester = 1;

    // Get the current date
    $currentDate = date('Y-m-d');

    // Query to check if the scanned ID exists and get today's attendance record
    $sql = "
              SELECT 
                  a.stud_school_id, 
                  b.assign_id, 
                  b.assign_duty_hours, 
                  c.dtr_time_in, 
                  c.dtr_time_out, 
                  c.dtr_id
              FROM tbl_scholars AS a
              INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
              LEFT JOIN tbl_dtr AS c ON c.dtr_stud_id = a.stud_id AND c.dtr_date = :currentDate
              WHERE a.stud_school_id = :scannedID
          ";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
    $stmt->bindValue(':currentDate', $currentDate, PDO::PARAM_STR); // Bind current date
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // If the student is found
    if ($result) {
      $dtrId = $result['dtr_id']; // Get the DTR ID
      $dtrTimeIn = $result['dtr_time_in'];
      $dtrTimeOut = $result['dtr_time_out'];
      $dutyAssignId = $result['assign_id']; // Get the assign ID
      $assignDutyHours = $result['assign_duty_hours']; // Get the assigned duty hours in seconds

      // If no record for today, insert a new attendance record
      if (empty($dtrId)) {
        $sqlInsert = "
                  INSERT INTO tbl_dtr 
                  (dtr_stud_id, dtr_date, dtr_time_in, dtr_school_year, dtr_semester, dtr_assign_id) 
                  VALUES 
                  ((SELECT stud_id FROM tbl_scholars WHERE stud_school_id = :scannedID), CURDATE(), NOW(), :school_year, :semester, :duty_assign_id)
              ";

        // Prepare and execute the insert statement
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
        $stmtInsert->bindValue(':school_year', $schoolYear, PDO::PARAM_STR);
        $stmtInsert->bindValue(':semester', $semester, PDO::PARAM_INT);
        $stmtInsert->bindValue(':duty_assign_id', $dutyAssignId, PDO::PARAM_INT); // Bind foreign key

        if ($stmtInsert->execute()) {
          return 1; // Success
        } else {
          return 0; // Failure
        }
      } else {
        // If a record exists for today
        if (empty($dtrTimeIn)) {
          // Update to insert dtr_time_in if it's empty
          $sqlUpdate = "UPDATE tbl_dtr SET dtr_time_in = NOW() WHERE dtr_id = :dtr_id";
        } elseif (empty($dtrTimeOut)) {
          // If already checked in, allow the user to check out and calculate hours worked
          $sqlUpdate = "UPDATE tbl_dtr SET dtr_time_out = NOW() WHERE dtr_id = :dtr_id";

          // Prepare and execute the update statement
          $stmtUpdate = $conn->prepare($sqlUpdate);
          $stmtUpdate->bindValue(':dtr_id', $dtrId, PDO::PARAM_INT);

          if ($stmtUpdate->execute()) {
            // Calculate the time difference in seconds between time in and time out
            $sqlTimeDiff = "SELECT TIMESTAMPDIFF(SECOND, dtr_time_in, dtr_time_out) AS time_worked FROM tbl_dtr WHERE dtr_id = :dtr_id";
            $stmtTimeDiff = $conn->prepare($sqlTimeDiff);
            $stmtTimeDiff->bindValue(':dtr_id', $dtrId, PDO::PARAM_INT);
            $stmtTimeDiff->execute();
            $timeWorked = $stmtTimeDiff->fetchColumn(); // Time worked in seconds

            // Deduct the time worked from the assigned duty hours
            $remainingDutyHours = $assignDutyHours - $timeWorked;

            // Update the remaining duty hours in the assignment table
            $sqlUpdateDutyHours = "UPDATE tbl_assign_scholars SET assign_duty_hours = :remainingDutyHours WHERE assign_id = :duty_assign_id";
            $stmtUpdateDutyHours = $conn->prepare($sqlUpdateDutyHours);
            $stmtUpdateDutyHours->bindValue(':remainingDutyHours', $remainingDutyHours, PDO::PARAM_INT);
            $stmtUpdateDutyHours->bindValue(':duty_assign_id', $dutyAssignId, PDO::PARAM_INT);

            if ($stmtUpdateDutyHours->execute()) {
              return 1; // Success if duty hours updated
            } else {
              return 0; // Failure to update duty hours
            }
          } else {
            return 0; // Update failed
          }
        } else {
          // Both times are filled, return an error
          return 0; // Attendance already marked for today.
        }
      }
    } else {
      // If the student is not found
      return 0; // No attendance record found
    }
  }



  function getAssignedScholars($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "SELECT
            FROM tbl_scholars AS a 
            INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
            INNER JOIN tbl_room AS c ON c.room_id = b.assign_room_id
            INNER JOIN tbl_building AS d ON d.build_id = b.assign_build_id
            INNER JOIN tbl_subject AS e ON e.subject_id = b.assign_subject_id
            WHERE b.assign_supM_id = :assign_supM_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":assign_supM_id", $json['assign_supM_id']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($result);
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
  case "AddPercentStype":
    echo $user->AddPercentStype($json);
    break;
  case "AddSubject":
    echo $user->AddSubject($json);
    break;
  case "AddOfficeType":
    echo $user->AddOfficeType($json);
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
  case "AddOfficeMasterSubCodeAndAssignScholars":
    echo $user->AddOfficeMasterSubCodeAndAssignScholars($json);
    break;
  case "getAddScholarDropDown":
    echo $user->getAddScholarDropDown();
    break;

  case "getAdmin":
    echo json_encode($user->getAdmin());
    break;
  case "getAssignmentMode":
    echo json_encode($user->getAssignmentMode());
    break;
  case "getScholar":
    echo json_encode($user->getScholar());
    break;
  case "getAcademicSession":
    echo json_encode($user->getAcademicSession());
    break;
  case "getscholarship_type":
    echo json_encode($user->getscholarship_type());
    break;
  case "getschoolyear":
    echo json_encode($user->getschoolyear());
    break;
  case "getStudStatus":
    echo json_encode($user->getStudStatus());
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
  case "getScholarlist":
    echo $user->getScholarlist();
    break;
  case "getDutyHours":
    echo json_encode($user->getDutyHours());
    break;
  case "getOfficeType":
    echo json_encode($user->getOfficeType());
    break;
  case "getSchoolYearList":
    echo $user->getSchoolYearList();
    break;
  case "getDepartment":
    echo json_encode($user->getDepartment());
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
  case "getJobType":
    echo json_encode($user->getJobType());
    break;
  case "getSubject":
    echo json_encode($user->getSubject());
    break;
  case "getPercentStype":
    echo json_encode($user->getPercentStype());
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
  case "getAssignedScholars":
    echo $user->getAssignedScholars($json);
    break;
  case "getAssignScholar":
    echo $user->getAssignScholar($json);
    break;

  default:
    echo "error";
    break;
}
