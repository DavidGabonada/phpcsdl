<?php
include "headers.php";

class User
{

  function AddAcademicSession($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT tbl_academic_session(session_name)
    VALUES(:seesion_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("session_name", $json["session_name"]);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? 1 : 0;
  }
  //UPDATED
  function addAdministrator($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    if (!isset($json["adm_name"], $json["adm_id"], $json["adm_email"])) {
      return json_encode(["success" => false, "message" => "Missing required fields"]);
    }

    // Trim inputs to avoid accidental spaces
    $adm_name = trim($json["adm_name"]);
    $adm_id = trim($json["adm_id"]);
    $adm_email = trim($json["adm_email"]);

    // Set image filename to NULL if not provided
    $adm_image_filename = isset($json["adm_image_filename"]) ? trim($json["adm_image_filename"]) : null;

    // Generate a password using the first two letters of adm_name + adm_id
    $password = substr($adm_name, 0, 2) . $adm_id;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Default user level is 3 if not set
    $user_level = isset($json["adm_user_level"]) ? $json["adm_user_level"] : 3;

    $sql = "INSERT INTO tbl_admin (adm_id, adm_name, adm_password, adm_email, adm_image_filename, adm_user_level) 
              VALUES (:adm_id, :adm_name, :adm_password, :adm_email, :adm_image_filename, :adm_user_level)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":adm_id", $adm_id);
    $stmt->bindParam(":adm_name", $adm_name);
    $stmt->bindParam(":adm_password", $hashedPassword);
    $stmt->bindParam(":adm_email", $adm_email);
    $stmt->bindParam(":adm_image_filename", $adm_image_filename);
    $stmt->bindParam(":adm_user_level", $user_level);

    if ($stmt->execute()) {
      return json_encode(["success" => true, "message" => "Administrator added successfully", "generated_password" => $password]);
    }

    return json_encode(["success" => false, "message" => "Failed to add administrator"]);
  }


  function addDepartment($json)
  {
    //{"dept_name":"bea"}
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "INSERT INTO tbl_department(dept_name, dept_build_id)
    VALUES(:dept_name, :dept_build_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("dept_name", $json["dept_name"]);
    $stmt->bindParam("dept_build_id", $json["dept_build_id"]);
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
    include "connection.php";
    $json = json_decode($json, true);

    if (!isset($json[0])) {
      // Convert single entry to an array for uniform processing
      $json = [$json];
    }

    $sql = "INSERT INTO tbl_scholarship_type(type_name, type_percent_id) 
              VALUES(:type_name, :type_percent_id)";
    $stmt = $conn->prepare($sql);

    $checkSql = "SELECT COUNT(*) FROM tbl_scholarship_type WHERE type_name = :type_name";
    $checkStmt = $conn->prepare($checkSql);

    $successCount = 0;

    foreach ($json as $item) {
      // Check if the type_name already exists
      $checkStmt->bindParam(":type_name", $item["type_name"]);
      $checkStmt->execute();
      $exists = $checkStmt->fetchColumn();

      if ($exists == 0) { // Insert only if it doesn't exist
        $stmt->bindParam(":type_name", $item["type_name"]);
        $stmt->bindParam(":type_percent_id", $item["type_percent_id"]);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
          $successCount++;
        }
      }
    }

    return $successCount;
  }


  function AddSubject($json)
  {
    include "connection.php";
    $subjects = json_decode($json, true);
    $conn->beginTransaction();

    try {
      foreach ($subjects as $subject) {
        // Check if the subject already exists
        $checkQuery = "SELECT * FROM tbl_subjects WHERE sub_code = :sub_code AND sub_section = :sub_section";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bindParam(":sub_code", $subject["sub_code"], PDO::PARAM_STR);
        $checkStmt->bindParam(":sub_section", $subject["sub_section"], PDO::PARAM_STR);
        $checkStmt->execute();
        $existingSubject = $checkStmt->fetch(PDO::FETCH_ASSOC);

        if ($existingSubject) {
          // Normalize values to avoid hidden spaces affecting comparison
          foreach ($existingSubject as $key => $value) {
            $existingSubject[$key] = trim((string) $value);
          }
          foreach ($subject as $key => $value) {
            $subject[$key] = trim((string) $value);
          }

          // Debugging: Show existing and new data
          echo "Existing Data: " . print_r($existingSubject, true);
          echo "New Data: " . print_r($subject, true);

          // Subject exists, update only if ANY field has changed
          if (
            $existingSubject["sub_time"] !== $subject["sub_time"] ||
            $existingSubject["sub_time_rc"] !== $subject["sub_time_rc"] ||
            $existingSubject["sub_descriptive_title"] !== $subject["sub_descriptive_title"] ||
            $existingSubject["sub_day_f2f_id"] !== $subject["sub_day_f2f_id"] ||
            $existingSubject["sub_day_rc_id"] !== $subject["sub_day_rc_id"] ||
            $existingSubject["sub_room"] !== $subject["sub_room"]
          ) {
            // Update all necessary fields
            $updateQuery = "UPDATE tbl_subjects SET 
                                    sub_descriptive_title = :sub_descriptive_title,
                                    sub_time = :sub_time, 
                                    sub_time_rc = :sub_time_rc,
                                    sub_day_f2f_id = :sub_day_f2f_id,
                                    sub_day_rc_id = :sub_day_rc_id,
                                    sub_room = :sub_room
                                    WHERE sub_code = :sub_code AND sub_section = :sub_section";

            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindParam(":sub_descriptive_title", $subject["sub_descriptive_title"], PDO::PARAM_STR);
            $updateStmt->bindParam(":sub_time", $subject["sub_time"], PDO::PARAM_STR);
            $updateStmt->bindParam(":sub_time_rc", $subject["sub_time_rc"], PDO::PARAM_STR);
            $updateStmt->bindParam(":sub_day_f2f_id", $subject["sub_day_f2f_id"], PDO::PARAM_INT);
            $updateStmt->bindParam(":sub_day_rc_id", $subject["sub_day_rc_id"], PDO::PARAM_INT);
            $updateStmt->bindParam(":sub_room", $subject["sub_room"], PDO::PARAM_STR);
            $updateStmt->bindParam(":sub_code", $subject["sub_code"], PDO::PARAM_STR);
            $updateStmt->bindParam(":sub_section", $subject["sub_section"], PDO::PARAM_STR);
            $updateStmt->execute();

            // Debugging: Check if update was successful
            if ($updateStmt->rowCount() > 0) {
              echo "Updated subject: " . $subject["sub_code"] . " - " . $subject["sub_section"] . "\n";
            } else {
              echo "No update was performed for subject: " . $subject["sub_code"] . "\n";
            }
          }
        } else {
          // Insert new subject
          $insertQuery = "INSERT INTO tbl_subjects(sub_code, sub_descriptive_title, sub_section, 
                              sub_day_f2f_id, sub_time, sub_day_rc_id, sub_time_rc, sub_room)
                              VALUES(:sub_code, :sub_descriptive_title, :sub_section, :sub_day_f2f_id, :sub_time,
                              :sub_day_rc_id, :sub_time_rc, :sub_room)";

          $insertStmt = $conn->prepare($insertQuery);
          $insertStmt->bindParam(":sub_code", $subject["sub_code"], PDO::PARAM_STR);
          $insertStmt->bindParam(":sub_descriptive_title", $subject["sub_descriptive_title"], PDO::PARAM_STR);
          $insertStmt->bindParam(":sub_section", $subject["sub_section"], PDO::PARAM_STR);
          $insertStmt->bindParam(":sub_day_f2f_id", $subject["sub_day_f2f_id"], PDO::PARAM_INT);
          $insertStmt->bindParam(":sub_time", $subject["sub_time"], PDO::PARAM_STR);
          $insertStmt->bindParam(":sub_day_rc_id", $subject["sub_day_rc_id"], PDO::PARAM_INT);
          $insertStmt->bindParam(":sub_time_rc", $subject["sub_time_rc"], PDO::PARAM_STR);
          $insertStmt->bindParam(":sub_room", $subject["sub_room"], PDO::PARAM_STR);
          $insertStmt->execute();

          echo "Inserted new subject: " . $subject["sub_code"] . "\n";
        }
      }

      $conn->commit();
      return 1;
    } catch (Exception $e) {
      $conn->rollBack();
      echo "Error: " . $e->getMessage();
      return 0;
    }
  }


  function AddSubjectOne($json)

  {
    include "connection.php";
    $subject = json_decode($json, true);
    $conn->beginTransaction();

    try {
      // Insert new subject, ignoring duplicates
      $insertQuery = "INSERT IGNORE INTO tbl_subjects (
                              sub_code, sub_descriptive_title, sub_section, 
                              sub_day_f2f_id, sub_time, sub_day_rc_id, sub_time_rc, sub_room, sub_supM_id
                          ) VALUES (
                              :sub_code, :sub_descriptive_title, :sub_section, 
                              :sub_day_f2f_id, :sub_time, :sub_day_rc_id, :sub_time_rc, :sub_room, :sub_supM_id
                          )";

      $insertStmt = $conn->prepare($insertQuery);
      $insertStmt->bindParam(":sub_code", $subject["sub_code"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_descriptive_title", $subject["sub_descriptive_title"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_section", $subject["sub_section"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_day_f2f_id", $subject["sub_day_f2f_id"], PDO::PARAM_INT);
      $insertStmt->bindParam(":sub_time", $subject["sub_time"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_day_rc_id", $subject["sub_day_rc_id"], PDO::PARAM_INT);
      $insertStmt->bindParam(":sub_time_rc", $subject["sub_time_rc"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_room", $subject["sub_room"], PDO::PARAM_STR);
      $insertStmt->bindParam(":sub_supM_id", $subject["sub_supM_id"], PDO::PARAM_STR);
      $insertStmt->execute();

      $conn->commit();
      return $insertStmt->rowCount() > 0 ? 1 : 0; // Success
    } catch (Exception $e) {
      $conn->rollBack();
      echo "Error: " . $e->getMessage();
      return -1; // Failure
    }
  }

  function AddScholar($json)
  {
    include "connection.php";

    $student = json_decode($json, true);
    $conn->beginTransaction();

    try {
      // 🔹 Fetch session_id from session_name
      $sqlSession = "SELECT session_id FROM tbl_academic_session WHERE session_name = :session_name";
      $stmtSession = $conn->prepare($sqlSession);
      $stmtSession->bindParam(":session_name", $student["stud_active_academic_session_id"]);
      $stmtSession->execute();
      $sessionRow = $stmtSession->fetch(PDO::FETCH_ASSOC);

      if (!$sessionRow) {
        throw new Exception("Invalid academic session: " . $student["stud_active_academic_session_id"]);
      }

      $session_id = $sessionRow["session_id"]; // Retrieved session_id

      // 🔹 Insert or update tbl_scholars
      $sql1 = "INSERT INTO tbl_scholars (
                        stud_id, stud_name, stud_scholarship_id, stud_department_id, stud_course_id, 
                        stud_password, stud_image_filename, stud_contactNumber, stud_email, stud_user_level
                    ) VALUES (
                        :stud_id, :stud_name, :stud_scholarship_id, :stud_department_id, :stud_course_id, 
                        :stud_password, NULL, :stud_contactNumber, :stud_email, 1
                    ) ON DUPLICATE KEY UPDATE 
                        stud_name = VALUES(stud_name),
                        stud_scholarship_id = VALUES(stud_scholarship_id),
                        stud_department_id = VALUES(stud_department_id),
                        stud_course_id = VALUES(stud_course_id),
                        stud_password = VALUES(stud_password),
                        stud_contactNumber = VALUES(stud_contactNumber),
                        stud_email = VALUES(stud_email)";

      $stmt1 = $conn->prepare($sql1);

      // Hash password
      $password = password_hash($student["stud_id"], PASSWORD_BCRYPT);

      // Bind scholar parameters
      $stmt1->execute([
        ':stud_id' => $student["stud_id"],
        ':stud_name' => $student["stud_name"],
        ':stud_scholarship_id' => $student["stud_scholarship_id"],
        ':stud_department_id' => $student["stud_department_id"],
        ':stud_course_id' => $student["stud_course_id"],
        ':stud_password' => $password,
        ':stud_contactNumber' => $student["stud_contactNumber"],
        ':stud_email' => $student["stud_email"]
      ]);

      // 🔹 Insert or update tbl_activescholars using retrieved session_id
      $sql2 = "INSERT INTO tbl_activescholars (
                        stud_active_id, stud_active_academic_session_id, stud_active_year_id, 
                        stud_active_status_id, stud_active_percent_id, stud_active_amount, 
                        stud_active_applied_on_tuition, stud_active_applied_on_misc, 
                        stud_date, stud_modified_by, stud_modified_date
                    ) VALUES (
                        :stud_active_id, :stud_active_academic_session_id, :stud_active_year_id,
                        :stud_active_status_id, :stud_active_percent_id, :stud_active_amount, 
                        :stud_active_applied_on_tuition, :stud_active_applied_on_misc, 
                        :stud_date, :stud_modified_by, :stud_modified_date
                    ) ON DUPLICATE KEY UPDATE 
                        stud_active_academic_session_id = VALUES(stud_active_academic_session_id),
                        stud_active_year_id = VALUES(stud_active_year_id),
                        stud_active_status_id = VALUES(stud_active_status_id),
                        stud_active_percent_id = VALUES(stud_active_percent_id),
                        stud_active_amount = VALUES(stud_active_amount),
                        stud_active_applied_on_tuition = VALUES(stud_active_applied_on_tuition),
                        stud_active_applied_on_misc = VALUES(stud_active_applied_on_misc),
                        stud_date = VALUES(stud_date),
                        stud_modified_by = VALUES(stud_modified_by),
                        stud_modified_date = VALUES(stud_modified_date)";

      $stmt2 = $conn->prepare($sql2);

      // Bind active scholar parameters with retrieved session_id
      $stmt2->execute([
        ':stud_active_id' => $student["stud_id"],
        ':stud_active_academic_session_id' => $session_id, // Use retrieved session_id
        ':stud_active_year_id' => $student["stud_active_year_id"],
        ':stud_active_status_id' => $student["stud_active_status_id"],
        ':stud_active_percent_id' => $student["stud_active_percent_id"],
        ':stud_active_amount' => $student["stud_active_amount"],
        ':stud_active_applied_on_tuition' => $student["stud_active_applied_on_tuition"],
        ':stud_active_applied_on_misc' => $student["stud_active_applied_on_misc"],
        ':stud_date' => $student["stud_date"],
        ':stud_modified_by' => $student["stud_modified_by"],
        ':stud_modified_date' => $student["stud_modified_date"]
      ]);

      $conn->commit();
      return $stmt2->rowCount() > 0 ? 1 : 0;
    } catch (Exception $e) {
      $conn->rollBack();
      return "Error: " . $e->getMessage();
    }
  }



  function AddScholarBatch($json)
  {
    include "connection.php";

    $json = json_decode($json, true);
    $conn->beginTransaction();

    try {
      // Insert or update scholars
      $sql1 = "INSERT INTO tbl_scholars (
                      stud_id, stud_name, stud_scholarship_id, stud_department_id, stud_course_id, 
                      stud_password, stud_image_filename, stud_contactNumber, stud_email, stud_user_level
                  ) VALUES (
                      :stud_id, :stud_name, :stud_scholarship_id, :stud_department_id, :stud_course_id, 
                      :stud_password, :stud_image_filename, :stud_contactNumber, :stud_email, 1
                  ) ON DUPLICATE KEY UPDATE 
                      stud_name = VALUES(stud_name),
                      stud_scholarship_id = VALUES(stud_scholarship_id),
                      stud_department_id = VALUES(stud_department_id),
                      stud_course_id = VALUES(stud_course_id),
                      stud_password = VALUES(stud_password),
                      stud_image_filename = VALUES(stud_image_filename),
                      stud_contactNumber = VALUES(stud_contactNumber),
                      stud_email = VALUES(stud_email)";

      $stmt1 = $conn->prepare($sql1);

      // Insert or update active scholars
      $sql2 = "INSERT INTO tbl_activescholars (
                      stud_active_id, stud_active_academic_session_id, stud_active_year_id, 
                      stud_active_status_id, stud_active_percent_id, stud_active_amount, 
                      stud_active_applied_on_tuition, stud_active_applied_on_misc, 
                      stud_date, stud_modified_by, stud_modified_date
                  ) VALUES (
                      :stud_active_id, :stud_active_academic_session_id, :stud_active_year_id,
                      :stud_active_status_id, :stud_active_percent_id, :stud_active_amount, 
                      :stud_active_applied_on_tuition, :stud_active_applied_on_misc, 
                      :stud_date, :stud_modified_by, :stud_modified_date
                  ) ON DUPLICATE KEY UPDATE 
                      stud_active_academic_session_id = VALUES(stud_active_academic_session_id),
                      stud_active_year_id = VALUES(stud_active_year_id),
                      stud_active_status_id = VALUES(stud_active_status_id),
                      stud_active_percent_id = VALUES(stud_active_percent_id),
                      stud_active_amount = VALUES(stud_active_amount),
                      stud_active_applied_on_tuition = VALUES(stud_active_applied_on_tuition),
                      stud_active_applied_on_misc = VALUES(stud_active_applied_on_misc),
                      stud_date = VALUES(stud_date),
                      stud_modified_by = VALUES(stud_modified_by),
                      stud_modified_date = VALUES(stud_modified_date)";

      $stmt2 = $conn->prepare($sql2);

      foreach ($json as $student) {
        // Generate or use plain-text password
        $password = $student["stud_id"]; // Change to `password_hash($student["stud_id"], PASSWORD_BCRYPT)` if hashing is needed.

        // Bind parameters for tbl_scholars
        $stmt1->bindParam(":stud_id", $student["stud_id"]);
        $stmt1->bindParam(":stud_name", $student["stud_name"]);
        $stmt1->bindParam(":stud_scholarship_id", $student["stud_scholarship_id"]);
        $stmt1->bindParam(":stud_department_id", $student["stud_department_id"]);
        $stmt1->bindParam(":stud_course_id", $student["stud_course_id"]);
        $stmt1->bindParam(":stud_password", $password);
        $stmt1->bindParam(":stud_image_filename", $student["stud_image_filename"]); // Fixed naming issue
        $stmt1->bindParam(":stud_contactNumber", $student["stud_contactNumber"]);
        $stmt1->bindParam(":stud_email", $student["stud_email"]);
        $stmt1->execute();

        // Bind parameters for tbl_activescholars
        $stmt2->bindParam(":stud_active_id", $student["stud_id"]);
        $stmt2->bindParam(":stud_active_academic_session_id", $student["stud_active_academic_session_id"]);
        $stmt2->bindParam(":stud_active_year_id", $student["stud_active_year_id"]);
        $stmt2->bindParam(":stud_active_status_id", $student["stud_active_status_id"]); // Fixed naming issue
        $stmt2->bindParam(":stud_active_percent_id", $student["stud_active_percent_id"]);
        $stmt2->bindParam(":stud_active_amount", $student["stud_active_amount"]);
        $stmt2->bindParam(":stud_active_applied_on_tuition", $student["stud_active_applied_on_tuition"]);
        $stmt2->bindParam(":stud_active_applied_on_misc", $student["stud_active_applied_on_misc"]);
        $stmt2->bindParam(":stud_date", $student["stud_date"]);
        $stmt2->bindParam(":stud_modified_by", $student["stud_modified_by"]);
        $stmt2->bindParam(":stud_modified_date", $student["stud_modified_date"]);
        $stmt2->execute();
      }

      $conn->commit();
      return 1;
    } catch (Exception $e) {
      $conn->rollBack();
      echo "Error: " . $e->getMessage();
      return 0;
    }
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
    $sql = "INSERT INTO tbl_room(room_name)
    VALUES(:room_name)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("room_name", $json["room_name"]);
    // $stmt->bindParam("room_build_id", $json["room_build_id"]);
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
  // function AddOfficeType($json)
  // {
  //   include "connection.php";
  //   $json = json_decode($json, true);
  //   $sql = "INSERT INTO tbl_office_type(off_name, off_building_id)
  //   VALUES(:off_name, :off_building_id)";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->bindParam("off_name", $json["off_name"]);
  //   $stmt->bindParam("off_building_id", $json["off_building_id"]);
  //   $stmt->execute();
  //   return $stmt->rowCount() > 0 ? 1 : 0;
  // }
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
    $password = password_hash($json["supM_id"], PASSWORD_DEFAULT);
    $sql = "INSERT INTO tbl_supervisors_master(supM_id, supM_name, supM_password, supM_email, supM_image_filename)
    VALUES (:supM_id, :supM_name, :supM_password, :supM_email, :supM_image_filename)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam("supM_id", $json["supM_id"]);
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
    $sql = "INSERT INTO tbl_assign_scholars(assign_stud_id, assign_duty_hours_id, assign_office_id, assign_mode_id, assign_session_id, assign_render_status) 
    VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_mode_id, :assign_session_id, :assign_render_status)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam("assign_stud_id", $json["assign_stud_id"]);
    $stmt->bindParam("assign_duty_hours_id", $json["assign_duty_hours_id"]);
    $stmt->bindParam("assign_office_id", $json["assign_office_id"]);
    $stmt->bindParam("assign_mode_id", $json["assign_mode_id"]);
    $stmt->bindParam("assign_session_id", $json["assign_session_id"]);
    $stmt->bindParam("assign_render_status", $json["assign_render_status"]);
    return $stmt->rowCount() > 0 ? 1 : 0;
  }

  function AddOfficeStudent($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    try {
      $conn->beginTransaction();

      // Get the offT_id based on offT_dept_id from tbl_office_type
      if (!isset($json["offT_dept_id"]) || empty($json["offT_dept_id"])) {
        throw new Exception("Missing or empty 'offT_dept_id' in input JSON.");
      }
      $sql = "SELECT offT_id FROM tbl_office_type WHERE offT_dept_id = :offT_dept_id";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":offT_dept_id", $json["offT_dept_id"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_office_type for offT_dept_id: " . $json["offT_dept_id"]);
      }
      $offTId = $result["offT_id"]; // Retrieve the offT_id

      // Insert into tbl_office_master with the retrieved offT_id
      $sql = "INSERT INTO tbl_office_master (off_type_id) VALUES (:off_type_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":off_type_id", $offTId); // Use the retrieved offT_id here
      $stmt->execute();
      $offId = $conn->lastInsertId(); // Retrieve the last inserted off_id

      if (!$offId) {
        throw new Exception("Failed to retrieve off_id after inserting into tbl_office_master.");
      }

      // Get the session_id from tbl_academic_session
      if (!isset($json["session_name"]) || empty($json["session_name"])) {
        throw new Exception("Missing or empty 'session_name' in input JSON.");
      }
      $sql = "SELECT session_id FROM tbl_academic_session WHERE session_name = :session_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":session_name", $json["session_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_academic_session for session_name: " . $json["session_name"]);
      }
      $assignSessionId = $result["session_id"];

      // Get the assignment_id from tbl_assignment_mode
      if (!isset($json["assignment_name"]) || empty($json["assignment_name"])) {
        throw new Exception("Missing or empty 'assignment_name' in input JSON.");
      }
      $sql = "SELECT assignment_id FROM tbl_assignment_mode WHERE assignment_name = :assignment_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":assignment_name", $json["assignment_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_assignment_mode for assignment_name: " . $json["assignment_name"]);
      }
      $assignModeId = $result["assignment_id"];

      // Get the dutyH_id from tbl_duty_hours
      if (!isset($json["dutyH_name"]) || empty($json["dutyH_name"])) {
        throw new Exception("Missing or empty 'dutyH_name' in input JSON.");
      }
      $sql = "SELECT dutyH_id FROM tbl_duty_hours WHERE dutyH_name = :dutyH_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":dutyH_name", $json["dutyH_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_duty_hours for dutyH_name: " . $json["dutyH_name"]);
      }
      $assignDutyHoursId = $result["dutyH_id"];

      // Insert into tbl_assign_scholars with retrieved off_id
      $sql = "INSERT INTO tbl_assign_scholars (assign_stud_id, assign_duty_hours_id, assign_office_id, assign_mode_id, assign_session_id)
                  VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_mode_id, :assign_session_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":assign_stud_id", $json["assign_stud_id"]);
      $stmt->bindParam(":assign_duty_hours_id", $assignDutyHoursId);
      $stmt->bindParam(":assign_office_id", $offId);  // Use the last inserted off_id
      $stmt->bindParam(":assign_mode_id", $assignModeId);
      $stmt->bindParam(":assign_session_id", $assignSessionId);
      $stmt->execute();

      // Commit the transaction
      $conn->commit();
      return 1; // Success
    } catch (Exception $e) {
      // Rollback the transaction if any error occurs
      $conn->rollBack();
      return 0; // Failure
    }
  }

  function AddStudentFacilitator($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    try {
      $conn->beginTransaction();

      // Insert into tbl_office_master and get the off_id
      $sql = "INSERT INTO tbl_office_master (off_subject_id) VALUES (:off_subject_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":off_subject_id", $json["off_subject_id"]);
      $stmt->execute();  // Make sure to execute the insert query here
      $result = $stmt->fetch(PDO::FETCH_ASSOC);

      // Retrieve the off_id from tbl_office_master (last inserted record)
      $offId = $conn->lastInsertId();

      if (!$offId) {
        throw new Exception("Failed to retrieve off_id after inserting into tbl_office_master.");
      }

      // Get the session_id from tbl_academic_session
      if (!isset($json["session_name"]) || empty($json["session_name"])) {
        throw new Exception("Missing or empty 'session_name' in input JSON.");
      }
      $sql = "SELECT session_id FROM tbl_academic_session WHERE session_name = :session_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":session_name", $json["session_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_academic_session for session_name: " . $json["session_name"]);
      }
      $assignSessionId = $result["session_id"];

      // Get the assignment_id from tbl_assignment_mode
      if (!isset($json["assignment_name"]) || empty($json["assignment_name"])) {
        throw new Exception("Missing or empty 'assignment_name' in input JSON.");
      }
      $sql = "SELECT assignment_id FROM tbl_assignment_mode WHERE assignment_name = :assignment_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":assignment_name", $json["assignment_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_assignment_mode for assignment_name: " . $json["assignment_name"]);
      }
      $assignModeId = $result["assignment_id"];

      // Get the dutyH_id from tbl_duty_hours
      if (!isset($json["dutyH_name"]) || empty($json["dutyH_name"])) {
        throw new Exception("Missing or empty 'dutyH_name' in input JSON.");
      }
      $sql = "SELECT dutyH_id FROM tbl_duty_hours WHERE dutyH_name = :dutyH_name";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":dutyH_name", $json["dutyH_name"]);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$result) {
        throw new Exception("No matching record found in tbl_duty_hours for dutyH_name: " . $json["dutyH_name"]);
      }
      $assignDutyHoursId = $result["dutyH_id"];

      // Insert into tbl_assign_scholars with retrieved off_id
      $sql = "INSERT INTO tbl_assign_scholars (assign_stud_id, assign_duty_hours_id, assign_office_id, assign_mode_id, assign_session_id)
                            VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_mode_id, :assign_session_id)";
      $stmt = $conn->prepare($sql);
      $stmt->bindParam(":assign_stud_id", $json["assign_stud_id"]);
      $stmt->bindParam(":assign_duty_hours_id", $assignDutyHoursId);
      $stmt->bindParam(":assign_office_id", $offId);  // Use the last inserted off_id
      $stmt->bindParam(":assign_mode_id", $assignModeId);
      $stmt->bindParam(":assign_session_id", $assignSessionId);
      $stmt->execute();

      // Commit the transaction
      $conn->commit();
      return 1; // Success
    } catch (Exception $e) {
      // Rollback the transaction if any error occurs
      $conn->rollBack();
      return "Error: " . $e->getMessage(); // Return the error message
    }
  }
  function getScholarSession()
  {
    include "connection.php";
    $sql = "SELECT b.stud_active_id, a.stud_name, c.session_name
              FROM tbl_scholars a
              INNER JOIN tbl_activescholars b ON b.stud_active_id = a.stud_id
              LEFT JOIN tbl_academic_session c ON b.stud_active_academic_session_id = c.session_id
              LEFT JOIN tbl_assign_scholars e ON e.assign_stud_id = b.stud_active_id
              WHERE e.assign_id IS NULL";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  function getAdmin()
  {
    include "connection.php";
    $stmt = "SELECT * FROM tbl_admin";
    $stmt = $conn->prepare($stmt);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }
  function getScholar()
  {
    include "connection.php";

    $stmt = "SELECT a.stud_active_id, b.stud_name, c.course_name, d.session_name
              FROM tbl_activescholars a
              INNER JOIN tbl_scholars b ON b.stud_id = a.stud_active_id
              INNER JOIN tbl_course c ON c.course_id = b.stud_course_id
              INNER JOIN tbl_academic_session d ON d.session_id = a.stud_active_academic_session_id
              LEFT JOIN tbl_assign_scholars e ON e.assign_stud_id = a.stud_active_id
              WHERE e.assign_id IS NULL";  // Exclude already assigned students

    $stmt = $conn->prepare($stmt);
    $stmt->execute();

    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  }


  function getscholarship_type()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_scholarship_type";
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
  // function getSubType()
  // {
  //   include "connection.php";
  //   $sql = "SELECT * FROM tbl_scholarship_sub_type";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->execute();
  //   return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : 0;
  // }
  function getScholarAllAvailableSchedule($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    // Ensure that $json is valid and contains the correct field
    if (!is_array($json) || !isset($json["ocr_studActive_id"])) {
      return json_encode(['error' => 'Invalid JSON data received or missing ocr_studActive_id']);
    }

    $sql = "SELECT * FROM tbl_ocr WHERE ocr_studActive_id = :ocr_studActive_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":ocr_studActive_id", $json["ocr_studActive_id"]);

    try {
      $stmt->execute();
      $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // If no records were found, return an empty array instead of a scalar value
      if (empty($result)) {
        return json_encode([]);
      }

      return json_encode($result);
    } catch (Exception $e) {
      return json_encode(['error' => 'Error executing query: ' . $e->getMessage()]);
    }
  }




  function getAllSubjects()
  {
    include "connection.php";
    $sql = "SELECT a.sub_id, a.sub_code, a.sub_descriptive_title, a.sub_section, a.sub_room, a.sub_time, b.day_name AS f2f_day, d.day_name AS rc_day, learning_name, a.sub_used
            FROM tbl_subjects AS a
            INNER JOIN tbl_day AS b ON b.day_id = a.sub_day_f2f_id
            INNER JOIN tbl_learning_modalities AS c ON c.learning_id = a.sub_learning_modalities_id
            INNER JOIN tbl_day AS d ON d.day_id = a.sub_day_rc_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($result);
  }

  function getschoolyear()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_year";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getSchoolYearLevel()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_year";
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
    $returnValue["subject"] = $this->getSubjectMaster();
    $returnValue["day"] = $this->getDays();
    $returnValue["supervisor"] = $this->getSupervisorMaster();
    $returnValue["room"] = $this->getRoom();

    return json_encode($returnValue);
  }

  function getDepartmentMaster()
  {
    include "connection.php";
    $sql = "SELECT a.dept_id, b.offT_dept_id, a.dept_name, c.day_name, b.offT_time, d.supM_name, b.off_limit, b.offT_supM_id 
              FROM tbl_department a
              INNER JOIN tbl_office_type b ON b.offT_dept_id = a.dept_id
              INNER JOIN tbl_day c ON c.day_id = b.offT_day_id
              LEFT JOIN tbl_supervisors_master d ON d.supM_id = b.offT_supM_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getSubjectMaster()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_subjects";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getAssignScholar()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getScholarSession"] = $this->getScholarSession();
    $returnValue["getDutyHours"] = $this->getDutyHours();
    $returnValue["getOfficeMaster"] = $this->getOfficeMaster();
    $returnValue["getOfficeType"] = $this->getOfficeType();
    $returnValue["getAssignmentMode"] = $this->getAssignmentMode();
    $returnValue["getDepartment"] = $this->getDepartment();
    $returnValue["getDepartmentMaster"] = $this->getDepartmentMaster();
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
    $returnValue["scholarshipSub"] = $this->getPercentStype();
    $returnValue["department"] = $this->getDepartment();
    $returnValue["year"] = $this->getschoolyear();
    $returnValue["status"] = $this->getStudStatus();
    // $returnValue["scholarshipSub"] = $this->getSubType();
    // $returnValue["modality"] = $this->getModality();
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
    $sql = "SELECT * FROM tbl_year";
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
  // function getScholarshipSubType()
  // {
  //   include "connection.php";
  //   $sql = "SELECT * FROM tbl_scholarship_sub_type WHERE stype_id = 1";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->execute();
  //   return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  // }
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
    $sql = "SELECT stud_name, session_name, sub_room
    FROM tbl_scholars a
    INNER JOIN tbl_activescholars b ON b.stud_active_id = a.stud_id
    LEFT JOIN tbl_academic_session c ON c.session_id = b.stud_active_academic_session_id
    LEFT JOIN tbl_assign_scholars d ON d.assign_stud_id = b.stud_active_id
    LEFT JOIN tbl_office_master e ON e.off_id = d.assign_office_id
    INNER JOIN tbl_subjects f ON f.sub_id = e.off_subject_id;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  function getSessionAcademic()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_academic_session";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }
  function getScholarStatus()
  {
    include "connection.php";
    $sql = "SELECT * FROM tbl_studstatus";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  }

  // function getModality()
  // {
  //   include "connection.php";
  //   $sql = "SELECT  * FROM tbl_stud_type_scholars";
  //   $stmt = $conn->prepare($sql);
  //   $stmt->execute();
  //   return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
  // }
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

  function getDutyAssign()
  {
    include "connection.php";
    $returnValue = [];
    $returnValue["getScholarSession"] = $this->getScholarSession();
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
    $returnValue["getAcademicSession"] = $this->getSessionAcademic();
    return json_encode($returnValue);
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
    // $returnValue["scholarsubtype"] = $this->getScholarshipSubType();
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

    $sql = "
      SELECT p.stud_active_id, a.stud_name AS StudentFullname, e.sub_code, e.sub_descriptive_title, e.sub_section, e.sub_time, e.sub_room, f.day_name AS F2F_Day, n.day_name AS RC_Day, m.day_name AS F2F_Day_Office, g.supM_name AS AdvisorFullname, i.dutyH_name AS TotalDutyHours, dept_name, d.offT_time, j.build_name, h.learning_name, i.dutyH_name - (SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out)) FROM tbl_dtr AS k2 WHERE k2.dtr_assign_id = b.assign_id AND k2.dtr_current_time_in <= k.dtr_current_time_in) AS RemainingHours,(SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out)) FROM tbl_dtr AS k2 WHERE k2.dtr_assign_id = b.assign_id) AS TotalRenderedHours, b.assign_render_status 
      FROM tbl_scholars AS a
      INNER JOIN tbl_activescholars AS p ON p.stud_active_id = a.stud_id
      LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = p.stud_active_id
      LEFT JOIN tbl_office_master AS c ON c.off_id = b.assign_office_id
      LEFT JOIN tbl_office_type AS d ON d.offT_id = c.off_type_id
      LEFT JOIN tbl_subjects AS e ON e.sub_id = c.off_subject_id
      LEFT JOIN tbl_day AS f ON f.day_id = e.sub_day_f2f_id 
      LEFT JOIN tbl_supervisors_master AS g ON g.supM_id = e.sub_supM_id
      LEFT JOIN tbl_learning_modalities AS h ON h.learning_id = e.sub_learning_modalities_id
      LEFT JOIN tbl_duty_hours AS i ON i.dutyH_id = b.assign_duty_hours_id
      LEFT JOIN tbl_department AS o ON o.dept_id = d.offT_dept_id
      LEFT JOIN tbl_building AS j ON j.build_id = o.dept_build_id
      LEFT JOIN tbl_dtr AS k ON k.dtr_assign_id = b.assign_id
      LEFT JOIN tbl_day AS m ON m.day_id = d.offT_day_id
      LEFT JOIN tbl_day AS n ON n.day_id = e.sub_day_rc_id 

      WHERE stud_active_id = :stud_active_id
      ORDER BY k.dtr_current_time_in DESC 
      LIMIT 1
          
          ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':stud_active_id', $json['stud_active_id']);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['RemainingHours'] <= 0) {
      $updateSql = "
          UPDATE tbl_assign_scholars
          SET assign_render_status = 1
          WHERE assign_stud_id = :stud_active_id
      ";

      $updateStmt = $conn->prepare($updateSql);
      $updateStmt->bindParam(':stud_active_id', $json['stud_active_id']);
      $updateStmt->execute();
    }

    return json_encode($result);
  }

  function getStudentDtr($json)
  {
    include "connection.php";
    $json = json_decode($json, true);
    $sql = "SELECT 
              a.stud_active_id, 
              b.stud_name AS StudentFullname, 
              d.dtr_date, 
              e.session_name, 
              TIME(d.dtr_current_time_in) AS dtr_time_in, 
              TIME(d.dtr_current_time_out) AS dtr_time_out,
              TIMEDIFF(TIME(d.dtr_current_time_out), TIME(d.dtr_current_time_in)) AS TotalRendered
          FROM 
              tbl_activescholars AS a
          INNER JOIN tbl_scholars AS b ON b.stud_id = a.stud_active_id
          LEFT JOIN 
              tbl_assign_scholars AS c ON c.assign_stud_id = a.stud_active_id
          LEFT JOIN 
              tbl_dtr AS d ON d.dtr_assign_id = c.assign_id
          LEFT JOIN 
              tbl_academic_session AS e ON e.session_id = c.assign_session_id
          WHERE 
              a.stud_active_id = :stud_active_id
          ORDER BY 
              d.dtr_date";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':stud_active_id', $json['stud_active_id']);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalRendered = 0;

    foreach ($result as $row) {
      // Validate TotalRendered
      if (!empty($row['TotalRendered']) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $row['TotalRendered'])) {
        list($hours, $minutes, $seconds) = explode(":", $row['TotalRendered']);
        $totalRendered += ($hours * 3600) + ($minutes * 60) + $seconds;
      }
    }

    // Convert total rendered time from seconds back to HH:MM:SS format
    $totalHours = floor($totalRendered / 3600);
    $totalMinutes = floor(($totalRendered % 3600) / 60);
    $totalSeconds = $totalRendered % 60;
    $totalRenderedFormatted = sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);

    // Append total rendered time to each student's data
    foreach ($result as $key => $row) {
      $result[$key]['TotalRenderedForStudent'] = $totalRenderedFormatted;
    }

    return json_encode($result);
  }


  function studentsAttendance($json)
  {
    include "connection.php";

    $json = json_decode($json, true);
    $scannedID = $json['stud_active_id'];
    $currentDate = date('Y-m-d'); // Today's date

    // Step 1: Fetch the latest DTR data for the scanned student
    $sql = "
      SELECT a.stud_active_id, b.assign_id AS assign_id, c.dtr_id, c.dtr_date, c.dtr_current_time_in, c.dtr_current_time_out
      FROM tbl_activescholars AS a
      INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_active_id
      LEFT JOIN tbl_dtr AS c ON c.dtr_assign_id = b.assign_id AND c.dtr_date = :currentDate
      WHERE a.stud_active_id = :scannedID
      ORDER BY c.dtr_date DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
    $stmt->bindValue(':currentDate', $currentDate, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Step 2: If no DTR record exists for today, insert new time_in
    if (!$result || empty($result['dtr_id'])) {
      $assignID = $result['assign_id']; // From tbl_assign_scholars
      $sqlInsertTimeIn = "
          INSERT INTO tbl_dtr (dtr_assign_id, dtr_date, dtr_current_time_in) 
          VALUES (:assignID, CURDATE(), NOW())
      ";
      $stmtInsert = $conn->prepare($sqlInsertTimeIn);
      $stmtInsert->bindValue(':assignID', $assignID, PDO::PARAM_INT);

      return $stmtInsert->execute() ? 1 : 0;
    }

    // Step 3: If time_in exists but time_out is empty, update time_out
    if (!empty($result['dtr_current_time_in']) && empty($result['dtr_current_time_out'])) {
      $sqlUpdateTimeOut = "
          UPDATE tbl_dtr 
          SET dtr_current_time_out = NOW()
          WHERE dtr_id = :dtr_id
      ";
      $stmtUpdate = $conn->prepare($sqlUpdateTimeOut);
      $stmtUpdate->bindValue(':dtr_id', $result['dtr_id'], PDO::PARAM_INT);

      return $stmtUpdate->execute() ? 1 : 0;
    }

    // Step 4: Default return if no other condition matches
    return 0;
  }




  function getAssignedScholars($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    $supM_id = $json['sub_supM_id'];

    $filteredResult = [];

    // Query for sub_supM_id
    $sqlSubSup = "SELECT b.stud_active_id, a.stud_name AS Fullname, a.stud_contactNumber, a.stud_email,
                            sub_code, sub_descriptive_title, g.sub_section, sub_time, sub_room,
                            f.dutyH_name
                     FROM tbl_scholars AS a
                     INNER JOIN tbl_activescholars AS b ON b.stud_active_id = a.stud_id
                     LEFT JOIN tbl_assign_scholars AS c ON c.assign_stud_id = b.stud_active_id
                     LEFT JOIN tbl_office_master AS d ON d.off_id = c.assign_office_id
                     LEFT JOIN tbl_office_type AS e ON e.offT_id = d.off_type_id
                     LEFT JOIN tbl_duty_hours AS f ON f.dutyH_id = c.assign_duty_hours_id
                     LEFT JOIN tbl_subjects AS g ON g.sub_id = d.off_subject_id
                     LEFT JOIN tbl_supervisors_master AS h ON h.supM_id = g.sub_supM_id
                     WHERE g.sub_supM_id = :supM_id";

    $stmtSubSup = $conn->prepare($sqlSubSup);
    $stmtSubSup->bindParam(":supM_id", $supM_id);
    $stmtSubSup->execute();
    $resultSubSup = $stmtSubSup->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultSubSup as $row) {
      $filteredResult[] = [
        'stud_active_id' => $row['stud_active_id'],
        'Fullname' => $row['Fullname'],
        'stud_contactNumber' => $row['stud_contactNumber'],
        'stud_email' => $row['stud_email'],
        'sub_code' => $row['sub_code'],
        'sub_descriptive_title' => $row['sub_descriptive_title'],
        'sub_section' => $row['sub_section'] ?? null, // Handle if null
        'sub_time' => $row['sub_time'],
        'sub_room' => $row['sub_room'],
        'dutyH_name' => $row['dutyH_name']
      ];
    }

    // Query for offT_supM_id
    $sqlOffTSup = "SELECT b.stud_active_id, a.stud_name AS Fullname, a.stud_contactNumber, a.stud_email,
                            h.dutyH_name, f.build_name, g.day_name, c.assign_render_status,
                            dept_name
                     FROM tbl_scholars AS a
                     INNER JOIN tbl_activescholars AS b ON b.stud_active_id = a.stud_id
                     LEFT JOIN tbl_assign_scholars AS c ON c.assign_stud_id = b.stud_active_id
                     LEFT JOIN tbl_office_master AS d ON d.off_id = c.assign_office_id
                     LEFT JOIN tbl_office_type AS e ON e.offT_id = d.off_type_id
                     LEFT JOIN tbl_day AS g ON g.day_id = e.offT_day_id
                     LEFT JOIN tbl_duty_hours AS h ON h.dutyH_id = c.assign_duty_hours_id
                     LEFT JOIN tbl_department AS j ON j.dept_id = e.offT_dept_id
                     LEFT JOIN tbl_building AS f ON f.build_id = j.dept_build_id
                     WHERE e.offT_supM_id = :supM_id";

    $stmtOffTSup = $conn->prepare($sqlOffTSup);
    $stmtOffTSup->bindParam(":supM_id", $supM_id);
    $stmtOffTSup->execute();
    $resultOffTSup = $stmtOffTSup->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultOffTSup as $row) {
      $filteredResult[] = [
        'stud_active_id' => $row['stud_active_id'],
        'Fullname' => $row['Fullname'],
        'stud_contactNumber' => $row['stud_contactNumber'],
        'stud_email' => $row['stud_email'],
        'dutyH_name' => $row['dutyH_name'],
        'build_name' => $row['build_name'],
        'day_name' => $row['day_name'],
        'assign_render_status' => $row['assign_render_status'],
        'dept_name' => $row['dept_name']
      ];
    }

    return json_encode($filteredResult);
  }

  function submitStudentFacilitatorEvaluation($json)
  {
    include "connection.php";

    $conn->beginTransaction();
    $json = json_decode($json, true);

    $sql = "INSERT INTO tbl_evaluation_sf (evaluation_sf_assign_stud_id, evaluation_sf_total_perfomance, evaluation_sf_total_general_attributes, evaluation_sf_attendance, evaluation_sf_overall_score, evaluation_sf_supM_id) 
          VALUES (:evaluation_sf_assign_stud_id, :evaluation_sf_total_perfomance, :evaluation_sf_total_general_attributes, :evaluation_sf_attendance, :evaluation_sf_overall_score, :evaluation_sf_supM_id)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":evaluation_sf_assign_stud_id", $json['evaluation_sf_assign_stud_id']);
    $stmt->bindParam(":evaluation_sf_total_perfomance", $json['evaluation_sf_total_perfomance']);
    $stmt->bindParam(":evaluation_sf_total_general_attributes", $json['evaluation_sf_total_general_attributes']);
    $stmt->bindParam(":evaluation_sf_attendance", $json['evaluation_sf_attendance']);
    $stmt->bindParam(":evaluation_sf_overall_score", $json['evaluation_sf_overall_score']);
    $stmt->bindParam(":evaluation_sf_supM_id", $json['evaluation_sf_supM_id']);

    if ($stmt->execute()) {
      $conn->commit();
      return 1;
    } else {
      $conn->rollBack();
      return 0;
    }
  }
  function updateScholarsProfile($json)
  {
    include "connection.php";
    $json = json_decode($json, true);

    // Hash the password securely using bcrypt
    $hashedPassword = password_hash($json["stud_password"], PASSWORD_DEFAULT);

    $sql = "UPDATE tbl_scholars 
              SET stud_contactNumber = :stud_contactNumber, 
                  stud_email = :stud_email, 
                  stud_password = :stud_password 
              WHERE stud_id = :stud_id";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":stud_contactNumber", $json["stud_contactNumber"]);
    $stmt->bindParam(":stud_email", $json["stud_email"]);
    $stmt->bindParam(":stud_password", $hashedPassword); // Store the hashed password
    $stmt->bindParam(":stud_id", $json["stud_id"]);

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
  case "AddAcademicSession":
    echo $user->AddAcademicSession($json);
    break;
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
    echo json_encode($user->AddScholar($json));
    break;
  case "AddScholarBatch":
    echo $user->AddScholarBatch($json);
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
  case "AddSubjectOne":
    echo json_encode($user->AddSubjectOne($json));
    break;
  case "AddSubject":
    echo $user->AddSubject($json);
    // case "AddOfficeType":
    //   echo $user->AddOfficeType($json);
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
  case "AddOfficeStudent":
    echo $user->AddOfficeStudent($json);
    break;
  case "AddStudentFacilitator":
    echo $user->AddStudentFacilitator($json);
    break;
  case "getAddScholarDropDown":
    echo $user->getAddScholarDropDown();
    break;
  case "getDepartmentMaster":
    echo json_encode($user->getDepartmentMaster());
    break;
  case "getSessionAcademic":
    echo json_encode($user->getSessionAcademic());
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
  // case "getScholarshipSubType":
  //   echo $user->getScholarshipSubType();
  //   break;
  case "getAdminList":
    echo $user->getadminList();
    break;
  case "getCourseList":
    echo json_encode($user->getCourseList());
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
  case "getScholarSession":
    echo json_encode($user->getScholarSession());
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
  // case "getSubType":
  //   echo $user->getSubType();
  //   break;
  case "getSchoolYearLevel":
    echo json_encode($user->getSchoolYearLevel());
    break;
  case "getDepartment":
    echo $user->getDepartment();
    break;
  case "getScholarStatus":
    echo $user->getScholarStatus();
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
  case "getScholarAllAvailableSchedule":
    echo $user->getScholarAllAvailableSchedule($json);
    break;
  case "getAllSubjects":
    echo $user->getAllSubjects();
    break;
  case "getDutyAssign":
    echo $user->getDutyAssign();
    break;
  // case "getModality":
  //   echo json_encode($user->getModality());
  //   break;
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
  case "submitStudentFacilitatorEvaluation":
    echo $user->submitStudentFacilitatorEvaluation($json);
    break;
  case "updateScholarsProfile":
    echo $user->updateScholarsProfile($json);
    break;

  default:
    echo "error";
    break;
}
