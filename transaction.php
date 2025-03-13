<?php
include "connection.php";
include "headers.php";

class Transaction
{
    function getStudentsDetailsAndStudentDutyAssign($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        $sql = "
        SELECT p.stud_active_id, a.stud_name AS StudentFullname, e.sub_code, e.sub_descriptive_title, e.sub_section, e.sub_time, e.sub_room, f.day_name AS F2F_Day, n.day_name AS RC_Day, m.day_name AS F2F_Day_Office, g.supM_name AS AdvisorFullname, i.dutyH_name AS TotalDutyHours, dept_name, d.offT_time, j.build_name, h.learning_name, i.dutyH_name - (SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out)) FROM tbl_dtr AS k2 WHERE k2.dtr_assign_stud_id = b.assign_id AND k2.dtr_current_time_in <= k.dtr_current_time_in) AS RemainingHours,(SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out)) FROM tbl_dtr AS k2 WHERE k2.dtr_assign_stud_id = b.assign_id) AS TotalRenderedHours, b.assign_render_status 
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
        LEFT JOIN tbl_dtr AS k ON k.dtr_assign_stud_id = b.assign_id
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
                tbl_dtr AS d ON d.dtr_assign_stud_id = c.assign_stud_id
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

        // Decode incoming JSON
        $json = json_decode($json, true);
        $scannedID = $json['stud_active_id'];
        $currentDate = date('Y-m-d'); // Today's date
        $currentTimestamp = date('Y-m-d H:i:s'); // Current timestamp

        // Step 1: Check if the student exists in tbl_assign_scholars
        $sqlCheckAssignment = "
        SELECT assign_stud_id FROM tbl_assign_scholars WHERE assign_stud_id = :scannedID
        ";
        $stmtCheckAssignment = $conn->prepare($sqlCheckAssignment);
        $stmtCheckAssignment->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
        $stmtCheckAssignment->execute();
        $assignment = $stmtCheckAssignment->fetch(PDO::FETCH_ASSOC);

        if (empty($assignment['assign_stud_id'])) {
            // The scanned student ID is not assigned, return error
            return json_encode(["error" => "Error: No valid assignment found for this student."]);
        }

        // Step 2: Check if a DTR record exists for the scanned student on today's date
        $sqlCheckDtr = "
        SELECT dtr_id, dtr_current_time_in, dtr_current_time_out 
        FROM tbl_dtr 
        WHERE dtr_assign_stud_id = :scannedID AND dtr_date = :currentDate
        ";
        $stmtCheckDtr = $conn->prepare($sqlCheckDtr);
        $stmtCheckDtr->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
        $stmtCheckDtr->bindValue(':currentDate', $currentDate, PDO::PARAM_STR);
        $stmtCheckDtr->execute();
        $dtrRecord = $stmtCheckDtr->fetch(PDO::FETCH_ASSOC);

        // Step 3: If no DTR record exists for today, insert a new time_in
        if (empty($dtrRecord)) {
            // Insert a new DTR record with time_in
            $sqlInsertTimeIn = "
            INSERT INTO tbl_dtr (dtr_assign_stud_id, dtr_date, dtr_current_time_in) 
            VALUES (:scannedID, :currentDate, :currentTimestamp)
            ";
            $stmtInsertTimeIn = $conn->prepare($sqlInsertTimeIn);
            $stmtInsertTimeIn->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
            $stmtInsertTimeIn->bindValue(':currentDate', $currentDate, PDO::PARAM_STR);
            $stmtInsertTimeIn->bindValue(':currentTimestamp', $currentTimestamp, PDO::PARAM_STR);

            // If insert is successful, return success (1)
            return $stmtInsertTimeIn->execute() ? json_encode([1]) : json_encode([0]);
        }

        // Step 4: If time_in exists but time_out is empty, update time_out
        if (!empty($dtrRecord['dtr_current_time_in']) && empty($dtrRecord['dtr_current_time_out'])) {
            $sqlUpdateTimeOut = "
            UPDATE tbl_dtr 
            SET dtr_current_time_out = :currentTimestamp
            WHERE dtr_id = :dtr_id
            ";
            $stmtUpdateTimeOut = $conn->prepare($sqlUpdateTimeOut);
            $stmtUpdateTimeOut->bindValue(':currentTimestamp', $currentTimestamp, PDO::PARAM_STR);
            $stmtUpdateTimeOut->bindValue(':dtr_id', $dtrRecord['dtr_id'], PDO::PARAM_INT);

            // If update is successful, return success (1)
            return $stmtUpdateTimeOut->execute() ? json_encode([1]) : json_encode([0]);
        }

        // Step 5: If both time_in and time_out are already filled for today, insert a new DTR record for the next day
        if (!empty($dtrRecord['dtr_current_time_in']) && !empty($dtrRecord['dtr_current_time_out'])) {
            // Insert a new DTR record for the next day (new time_in)
            $sqlInsertNextDay = "
            INSERT INTO tbl_dtr (dtr_assign_stud_id, dtr_date, dtr_current_time_in) 
            VALUES (:scannedID, CURDATE(), :currentTimestamp)
            ";
            $stmtInsertNextDay = $conn->prepare($sqlInsertNextDay);
            $stmtInsertNextDay->bindValue(':scannedID', $scannedID, PDO::PARAM_STR);
            $stmtInsertNextDay->bindValue(':currentTimestamp', $currentTimestamp, PDO::PARAM_STR);

            // If insert is successful, return success (1)
            return $stmtInsertNextDay->execute() ? json_encode([1]) : json_encode([0]);
        }

        // Default return if no other condition matches
        return json_encode([0]);
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
                              f.dutyH_name, c.assign_render_status, c.assign_evaluation_status
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
                'assign_render_status' => $row['assign_render_status'],
                'dutyH_name' => $row['dutyH_name']
            ];
        }

        // Query for offT_supM_id
        $sqlOffTSup = "SELECT b.stud_active_id, a.stud_name AS Fullname, a.stud_contactNumber, a.stud_email,
                              h.dutyH_name, f.build_name, g.day_name, c.assign_render_status,
                              dept_name, c.assign_evaluation_status
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

    function getScholarAllAvailableSchedule($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_ocr WHERE ocr_studActive_id = :ocr_studActive_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ocr_studActive_id", $json["ocr_studActive_id"], PDO::PARAM_STR);
        $stmt->execute();
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    function getAllSubjects()
    {
        include "connection.php";
        $sql = "SELECT a.sub_code, a.sub_descriptive_title, a.sub_section, a.sub_room, a.sub_time, b.day_name AS f2f_day, d.day_name AS rc_day, learning_name, a.sub_used
                FROM tbl_subjects AS a
                INNER JOIN tbl_day AS b ON b.day_id = a.sub_day_f2f_id
                INNER JOIN tbl_learning_modalities AS c ON c.learning_id = a.sub_learning_modalities_id
                INNER JOIN tbl_day AS d ON d.day_id = a.sub_day_rc_id
                WHERE a.sub_used = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    function updateScholarsProfile($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        // Hash the password before saving it to the database
        $hashedPassword = password_hash($json["stud_password"], PASSWORD_BCRYPT);

        $sql = "UPDATE tbl_scholars 
                SET stud_contactNumber = :stud_contactNumber, 
                    stud_email = :stud_email, 
                    stud_password = :stud_password
                WHERE stud_id = :stud_id";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_contactNumber", $json["stud_contactNumber"]);
        $stmt->bindParam(":stud_email", $json["stud_email"]);
        $stmt->bindParam(":stud_password", $hashedPassword); // Bind the hashed password
        $stmt->bindParam(":stud_id", $json["stud_id"]);

        if ($stmt->execute()) {
            return 1; // Successfully updated
        } else {
            return 0; // Failed to update
        }
    }



    function updatePassword($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        $username = $json["username"];
        $newPassword = $json["stud_password"]; // or supM_password if advisor
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Check if the user is a student or advisor
        if (isset($json["stud_id"])) {
            // Retrieve the current hashed password from the database
            $sql = "SELECT stud_password FROM tbl_scholars WHERE stud_id = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $existingPassword = $row["stud_password"];

                // **Fix: Use password_verify() to check if the current password is just the hashed version of `stud_id`**
                if (password_verify($username, $existingPassword)) {
                    // If the password is still default, force the user to change it
                    $updateSql = "UPDATE tbl_scholars SET stud_password = :stud_password WHERE stud_id = :username";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bindParam(":stud_password", $hashedPassword);
                    $updateStmt->bindParam(":username", $username);
                    $updateStmt->execute();
                    return $updateStmt->rowCount() > 0 ? 1 : 0;
                } else {
                    return 2; // Password is already changed
                }
            }
        } elseif (isset($json["supM_id"])) {
            // Retrieve the current hashed password from the database
            $sql = "SELECT supM_password FROM tbl_supervisors_master WHERE supM_id = :username LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(":username", $username, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $existingPassword = $row["supM_password"];

                if (password_verify($username, $existingPassword)) {
                    // If the password is still default, force the user to change it
                    $updateSql = "UPDATE tbl_supervisors_master SET supM_password = :supM_password WHERE supM_id = :username";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->bindParam(":supM_password", $hashedPassword);
                    $updateStmt->bindParam(":username", $username);
                    $updateStmt->execute();
                    return $updateStmt->rowCount() > 0 ? 1 : 0;
                } else {
                    return 2; // Password is already changed
                }
            }
        }

        return 0; // No valid user ID found or password not changed
    }

    function verifyAndUpdatePassword($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        // Fetch the stored password hash from the database
        $sql = "SELECT stud_password FROM tbl_scholars WHERE stud_id = :stud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_id", $json["stud_id"]);
        $stmt->execute();

        $storedPasswordHash = $stmt->fetchColumn();

        // Check if the current password matches the stored hash
        if ($storedPasswordHash && password_verify($json["stud_currentPassword"], $storedPasswordHash)) {
            // Hash the new password securely using bcrypt
            $hashedPassword = password_hash($json["stud_password"], PASSWORD_DEFAULT);

            // Update the password
            $updateSql = "UPDATE tbl_scholars SET stud_password = :stud_password WHERE stud_id = :stud_id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(":stud_password", $hashedPassword);
            $updateStmt->bindParam(":stud_id", $json["stud_id"]);

            $updateStmt->execute();
            return $updateStmt->rowCount() > 0 ? 1 : 0;
        } else {
            return 0; // Current password is incorrect
        }
    }

    function forgotPassword($json){
        include "connection.php";
        $json = json_decode($json, true);


    }


    function getRequestSchedule($json)
    {
        include("connection.php");

        // Ensure the data is decoded if it's raw JSON (assuming it's a POST request)
        if (is_string($json)) {
            $json = json_decode($json, true); // Decode to array if input is a JSON string
        }

        // Validate the JSON structure
        if (!isset($json['day_name']) || !isset($json['time_from']) || !isset($json['time_to'])) {
            return ['error' => 'Missing required fields'];
        }

        // Check types for day_name, time_from, and time_to
        if (!is_string($json['day_name']) || !is_string($json['time_from']) || !is_string($json['time_to'])) {
            return ['error' => 'Invalid input types'];
        }

        // Normalize the day name from Flutter (e.g., "Mon" -> "MON")
        $ocr_day = strtoupper($json["day_name"]);  // Convert to uppercase for DB comparison

        // Time sent from Flutter (e.g., "7:30 AM" and "9:00 AM")
        $time_from = $json["time_from"];  // Time as sent from Flutter
        $time_to = $json["time_to"];  // Time as sent from Flutter

        // Prepare the SQL query
        $sql = "SELECT sub_id, sub_code, sub_descriptive_title, sub_section, 
                       b.day_name AS F2F_Day, c.day_name AS RC_Day, sub_time, sub_room
                FROM tbl_subjects AS a
                INNER JOIN tbl_day AS b ON b.day_id = a.sub_day_f2f_id
                INNER JOIN tbl_day AS c ON c.day_id = a.sub_day_rc_id
                WHERE (b.day_name = :ocr_day OR c.day_name = :ocr_day) 
                  AND STR_TO_DATE(SUBSTRING_INDEX(sub_time, '-', 1), '%h:%i%p') <= STR_TO_DATE(:time_from, '%h:%i%p')
                  AND STR_TO_DATE(SUBSTRING_INDEX(sub_time, '-', -1), '%h:%i%p') >= STR_TO_DATE(:time_to, '%h:%i%p')
                  AND a.sub_used = 1";

        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ocr_day", $ocr_day, PDO::PARAM_STR);
        $stmt->bindParam(":time_from", $time_from, PDO::PARAM_STR);
        $stmt->bindParam(":time_to", $time_to, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return results or empty array if no results found
        return !empty($results) ? $results : 0;
    }

    function sendRequestSchedule($json)
    {
        include "connection.php";

        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_request_schedule (request_sched_stud_active_id, request_sched_subject_id) VALUES (:stud_active_id, :sub_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_active_id", $json["stud_active_id"], PDO::PARAM_STR);
        $stmt->bindParam(":sub_id", $json["sub_id"], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function scholarAssignedChecker($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT COUNT(*) FROM tbl_assign_scholars WHERE assign_stud_id = :stud_active_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_active_id", $json["stud_active_id"], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : 0;
    }

    function updateLoginAttempts($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        // Start a transaction
        $conn->beginTransaction();

        try {
            // Prepare the query to check for the user in both tbl_scholars and tbl_supervisors_master tables
            $sqlCheckUser = "SELECT * FROM tbl_scholars WHERE stud_id = :username OR stud_email = :username";
            $stmtCheckUser = $conn->prepare($sqlCheckUser);
            $stmtCheckUser->bindParam(":username", $json["username"], PDO::PARAM_STR);
            $stmtCheckUser->execute();

            // Check if the user is found in tbl_scholars
            if ($stmtCheckUser->rowCount() > 0) {
                // If found in scholars table, update the login_attempts
                $sql_update = "UPDATE tbl_scholars 
                               SET stud_login_attempts = :login_attempts 
                               WHERE stud_id = :username OR stud_email = :username";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bindParam(":login_attempts", $json["login_attempts"], PDO::PARAM_INT);
                $stmt_update->bindParam(":username", $json["username"], PDO::PARAM_STR);
                $stmt_update->execute();
            } else {
                // If not found in tbl_scholars, check in tbl_supervisors_master
                $sqlCheckSupervisor = "SELECT * FROM tbl_supervisors_master WHERE supM_id = :username OR supM_email = :username";
                $stmtCheckSupervisor = $conn->prepare($sqlCheckSupervisor);
                $stmtCheckSupervisor->bindParam(":username", $json["username"], PDO::PARAM_STR);
                $stmtCheckSupervisor->execute();

                // Check if the user is found in tbl_supervisors_master
                if ($stmtCheckSupervisor->rowCount() > 0) {
                    // If found in supervisors table, update the login_attempts
                    $sql_update = "UPDATE tbl_supervisors_master 
                                   SET supM_login_attempts = :login_attempts 
                                   WHERE supM_id = :username OR supM_email = :username";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(":login_attempts", $json["login_attempts"], PDO::PARAM_INT);
                    $stmt_update->bindParam(":username", $json["username"], PDO::PARAM_STR);
                    $stmt_update->execute();
                } else {
                    // If neither student nor supervisor is found
                    return json_encode(["error" => "User not found."]);
                }
            }

            // Commit the transaction if everything is successful
            $conn->commit();
            return json_encode(["success" => "Login attempts updated successfully."]);
        } catch (Exception $e) {
            // Rollback the transaction if an error occurs
            $conn->rollBack();
            return json_encode(["error" => "Error updating login attempts: " . $e->getMessage()]);
        }
    }

    function getStudentProfile($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_scholars WHERE stud_id = :stud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_id", $json["stud_id"], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
    }
    
    function getSupervisorProfile($json){
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT * FROM tbl_supervisors_master WHERE supM_id = :supM_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":supM_id", $json["supM_id"], PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    function updateAdvisorProfile($json){
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_supervisors_master SET supM_email = :supM_email, supM_contact = :supM_contact WHERE supM_id = :supM_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":supM_email", $json["supM_email"], PDO::PARAM_STR);
        $stmt->bindParam(":supM_contact", $json["supM_contact"], PDO::PARAM_STR);
        $stmt->bindParam(":supM_id", $json["supM_id"], PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->execute()) {
            return 1; // Successfully updated
        } else {
            return 0; // Failed to update
        }
    }

    function verfityAndUpdateAdvisorPassword($json){
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT supM_password FROM tbl_supervisors_master WHERE supM_id = :supM_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":supM_id", $json["supM_id"], PDO::PARAM_STR);
        $stmt->execute();

        $storedPasswordHash = $stmt->fetchColumn();

        // Check if the current password matches the stored hash
        if ($storedPasswordHash && password_verify($json["supM_currentPassword"], $storedPasswordHash)) {
            // Hash the new password securely using bcrypt
            $hashedPassword = password_hash($json["supM_password"], PASSWORD_DEFAULT);

            // Update the password
            $updateSql = "UPDATE tbl_scholars SET supM_password = :supM_password WHERE supM_id = :supM_id";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(":supM_password", $hashedPassword);
            $updateStmt->bindParam(":supM_id", $json["supM_id"]);

            $updateStmt->execute();
            return $updateStmt->rowCount() > 0 ? 1 : 0;
        } else {
            return 0; // Current password is incorrect
        }
    }

    function updateTwoFactorAuthenticationAdvisor($json){
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_supervisors_master SET supM_authentication_status = :supM_authentication_status WHERE supM_id = :supM_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":supM_authentication_status", $json["supM_authentication_status"], PDO::PARAM_STR);
        $stmt->bindParam(":supM_id", $json["supM_id"], PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->execute()) {
            return 1; // Successfully updated
        } else {
            return 0; // Failed to update
        }
    }
    // function sendOTP($json){
    //     include "connection.php";
    //     include "send_email.php";
    //     $json = json_decode($json, true);
    //     $sendEmail = new SendEmail();
    //     try {
    //         $emailSubject = "OTP Verification";
    //         $emailBody = "Your OTP is: " . $json["otp"];
    //         $emailToSent = $json["email"];
    //         $sendEmail->sendEmail($emailSubject, $emailBody, $emailToSent);
    //         return 1;
    //     } catch (\Throwable $th) {
    //         echo $th->getMessage();
    //         return 0;
    //     }
    // }

    function sendEmail($json)
    {
        include "send_email.php";
        // {"emailToSent":"xhifumine@gmail.com","emailSubject":"Kunwari MESSAGE","emailBody":"Kunwari message ni diri hehe <b>102345</b>"}
        $json = json_decode($json, true);

        // Set default email subject if not provided
        $emailSubject = isset($json['emailSubject']) ? $json['emailSubject'] : "Phinma-COC SMS";

        $sendEmail = new SendEmail();
        return $sendEmail->sendEmail($json['emailToSent'], $emailSubject, $json['emailBody']);
    }

    function TwoFactorAuthentication($json)
    {
        include "send_email.php";
        // {"emailToSent":"xhifumine@gmail.com","emailSubject":"Kunwari MESSAGE","emailBody":"Kunwari message ni diri hehe <b>102345</b>"}
        $json = json_decode($json, true);

        // Set default email subject if not provided
        $emailSubject = isset($json['emailSubject']) ? $json['emailSubject'] : "Phinma-COC SMS OTP Login Credentials";

        $sendEmail = new SendEmail();
        return $sendEmail->sendEmail($json['emailToSent'], $emailSubject, $json['emailBody']);
    }

    //2FA LINE

    function updateTwoFactorAuthenticationStudent($json){
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "UPDATE tbl_scholars SET stud_authentication_status = :stud_authentication_status WHERE stud_id = :stud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_authentication_status", $json["stud_authentication_status"], PDO::PARAM_STR);
        $stmt->bindParam(":stud_id", $json["stud_id"], PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function authenticationChecker($json) {
        include "connection.php";
        
        // Decode the JSON input
        $json = json_decode($json, true);
        
        // Prepare SQL query to fetch the scholar's authentication status
        $sql = "SELECT stud_authentication_status FROM tbl_scholars WHERE stud_id = :stud_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":stud_id", $json["stud_id"], PDO::PARAM_STR);
        $stmt->execute();
        
        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Return only the stud_authentication_status in JSON format
        return json_encode(["stud_authentication_status" => $result['stud_authentication_status']]);
    }
    

    //ADMIN SIDE

    function getStudentDutyData()
    {
        include "connection.php";

        $sql = "
            SELECT 
                a.stud_id, 
                a.stud_name, 
                c.dutyH_name, 
                SUM(TIMESTAMPDIFF(HOUR, d.dtr_current_time_in, d.dtr_current_time_out)) AS total_rendered_hours,
                (180 - SUM(TIMESTAMPDIFF(HOUR, d.dtr_current_time_in, d.dtr_current_time_out))) AS remaining_hours
            FROM tbl_scholars AS a
            INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
            INNER JOIN tbl_duty_hours AS c ON c.dutyH_id = b.assign_duty_hours_id
            INNER JOIN tbl_dtr AS d ON d.dtr_assign_id = b.assign_id
            GROUP BY a.stud_id, a.stud_name, c.dutyH_name
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    function getActiveAndAssignedScholars()
    {
        include "connection.php";

        // Query to count active and assigned scholars
        $sql = "
        SELECT 
            COUNT(CASE WHEN b.assign_stud_id IS NOT NULL THEN 1 END) AS assigned_count,
            COUNT(*) AS total_count
        FROM tbl_activescholars AS a
        LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_active_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate percentages
        $total = $result['total_count'];
        $assignedPercentage = $total > 0 ? ($result['assigned_count'] / $total) * 100 : 0;
        $activePercentage = $total > 0 ? (($total - $result['assigned_count']) / $total) * 100 : 0;

        // Ensure proper JSON encoding
        echo json_encode([
            'assigned' => round($assignedPercentage, 2),
            'active' => round($activePercentage, 2),
        ]);
    }

    function getActiveAndRenewedScholars()
    {
        include "connection.php";

        // Updated SQL query to count assigned and renewal scholars
        $sql = "
    SELECT 
        COUNT(CASE WHEN b.renewal_assign_stud_active_id IS NOT NULL THEN 1 END) AS renewal_count,
        COUNT(*) AS total_count
    FROM tbl_assign_scholars AS a
    LEFT JOIN tbl_renewal AS b ON b.renewal_assign_stud_active_id = a.assign_stud_id";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate percentages
        $total = $result['total_count'];
        $renewalPercentage = $total > 0 ? ($result['renewal_count'] / $total) * 100 : 0;
        $assignedPercentage = $total > 0 ? (($total - $result['renewal_count']) / $total) * 100 : 0;

        // Ensure proper JSON encoding
        echo json_encode([
            'renewal' => round($renewalPercentage, 2),  // Renewal scholars percentage
            'assigned' => round($assignedPercentage, 2), // Assigned scholars percentage
        ]);
    }
    function getNearlyFinishedandFinishedScholar()
    {
        include "connection.php";

        // SQL query to get the time_in and time_out values
        $sql = "
    SELECT 
        stud_active_id, 
        assign_stud_id, 
        dtr_assign_stud_id, 
        c.dtr_current_time_in, 
        c.dtr_current_time_out
    FROM tbl_activescholars AS a
    LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_active_id
    LEFT JOIN tbl_dtr AS c ON c.dtr_assign_stud_id = b.assign_stud_id";

        // Execute the query
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Variables to count the number of nearly finished and finished scholars
        $finishedCount = 0;
        $nearlyFinishedCount = 0;
        $totalCount = 0;

        // Loop through the results and calculate the time difference
        foreach ($results as $row) {
            $totalCount++;

            // Get the time_in and time_out as DateTime objects
            $timeIn = new DateTime($row['dtr_current_time_in']);
            $timeOut = new DateTime($row['dtr_current_time_out']);

            // Calculate the difference in minutes
            $interval = $timeIn->diff($timeOut);
            $workedMinutes = ($interval->h * 60) + $interval->i; // Convert hours to minutes and add the minutes

            // Check if the scholar is "Finished" or "Nearly Finished"
            if ($workedMinutes >= 180 && $workedMinutes >= 90) {
                $finishedCount++;
            } elseif ($workedMinutes < 180) {
                $nearlyFinishedCount++;
            }
        }

        // Calculate percentages for Finished and Nearly Finished scholars
        $finishedPercentage = $totalCount > 0 ? ($finishedCount / $totalCount) * 100 : 0;
        $nearlyFinishedPercentage = $totalCount > 0 ? ($nearlyFinishedCount / $totalCount) * 100 : 0;

        // Return the result as a JSON object
        echo json_encode([
            'finished' => round($finishedPercentage, 2),        // Percentage of finished scholars
            'nearlyFinished' => round($nearlyFinishedPercentage, 2), // Percentage of nearly finished scholars
        ]);
    }

    function getAllStudentFacilitator()
    {
        include "connection.php";

        $sql = "SELECT 
                c.assign_stud_id, 
                a.stud_name, 
                d.dutyH_name, 
                f.sub_room, 
                g.supM_name, 
                c.assign_render_status, 
                c.assign_evaluation_status, 
                SUM(TIMESTAMPDIFF(HOUR, h.dtr_current_time_in, h.dtr_current_time_out)) AS total_rendered_hours,
                (180 - SUM(TIMESTAMPDIFF(HOUR, h.dtr_current_time_in, h.dtr_current_time_out))) AS remaining_hours
            FROM 
                tbl_scholars AS a
            INNER JOIN 
                tbl_activescholars AS b ON b.stud_active_id = a.stud_id
            INNER JOIN 
                tbl_assign_scholars AS c ON c.assign_stud_id = b.stud_active_id
            INNER JOIN 
                tbl_duty_hours AS d ON d.dutyH_id = c.assign_duty_hours_id
            INNER JOIN 
                tbl_office_master AS e ON e.off_id = c.assign_office_id
            INNER JOIN 
                tbl_subjects AS f ON f.sub_id = e.off_subject_id
            INNER JOIN 
                tbl_supervisors_master AS g ON g.supM_id = f.sub_supM_id
            LEFT JOIN 
                tbl_dtr AS h ON h.dtr_assign_id = c.assign_id
            GROUP BY 
                c.assign_stud_id, 
                a.stud_name, 
                d.dutyH_name, 
                f.sub_room, 
                g.supM_name, 
                c.assign_render_status, 
                c.assign_evaluation_status;
            ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }
    function getAllOfficeScholar()
    {
        include "connection.php";

        $sql = "SELECT 
    a.assign_stud_id, 
    c.stud_name, 
    d.dutyH_name, 
    h.build_name, 
    i.supM_name, 
    a.assign_render_status, 
    a.assign_evaluation_status, 
    SUM(TIMESTAMPDIFF(HOUR, j.dtr_current_time_in, j.dtr_current_time_out)) AS total_rendered_hours,
    (180 - SUM(TIMESTAMPDIFF(HOUR, j.dtr_current_time_in, j.dtr_current_time_out))) AS remaining_hours
FROM 
    tbl_assign_scholars AS a
LEFT JOIN 
    tbl_activescholars AS b ON b.stud_active_id = a.assign_stud_id
INNER JOIN 
    tbl_scholars AS c ON c.stud_id = b.stud_active_id
INNER JOIN 
    tbl_duty_hours AS d ON d.dutyH_id = a.assign_duty_hours_id
INNER JOIN 
    tbl_office_master AS e ON e.off_id = a.assign_office_id
INNER JOIN 
    tbl_office_type AS f ON f.offT_id = e.off_type_id
INNER JOIN 
    tbl_department AS g ON g.dept_id = f.offT_dept_id
INNER JOIN 
    tbl_building AS h ON h.build_id = g.dept_build_id
INNER JOIN 
    tbl_supervisors_master AS i ON i.supM_id = f.offT_supM_id
INNER JOIN 
    tbl_dtr AS j ON j.dtr_assign_id = a.assign_id
GROUP BY 
    a.assign_stud_id, 
    c.stud_name, 
    d.dutyH_name, 
    h.build_name, 
    i.supM_name, 
    a.assign_render_status, 
    a.assign_evaluation_status;
";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return json_encode($result);
    }

    function getRequestedSchedule()
    {
        include "connection.php";
        $sql = "SELECT request_sched_id, request_sched_stud_active_id, sub_code, request_sched_status
                FROM tbl_request_schedule AS a
                INNER JOIN tbl_subjects AS b ON b.sub_id = a.request_sched_subject_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($result);
    }

    function updateRequestedScheduleStatusAndAddFacilitator($json)
    {
        include "connection.php";
        $json = json_decode($json, true);

        try {
            // Begin a transaction to ensure atomicity
            $conn->beginTransaction();

            // Update the request_sched_status
            $sql = "UPDATE tbl_request_schedule SET request_sched_status = :request_sched_status WHERE request_sched_id = :request_sched_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':request_sched_id', $json["request_sched_id"], PDO::PARAM_INT);
            $stmt->bindParam(":request_sched_status", $json["request_sched_status"], PDO::PARAM_INT);
            $stmt->execute();

            // If the status was updated to 1, proceed with adding the student facilitator
            if ($json["request_sched_status"] == 1) {
                // Step 1: Get the assign_stud_id and off_subject_id from tbl_request_schedule
                $sql = "SELECT request_sched_stud_active_id, request_sched_subject_id FROM tbl_request_schedule WHERE request_sched_id = :request_sched_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":request_sched_id", $json["request_sched_id"]);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result) {
                    throw new Exception("No matching record found in tbl_request_schedule for request_sched_id: " . $json["request_sched_id"]);
                }

                $assignStudId = $result["request_sched_stud_active_id"];
                $offSubjectId = $result["request_sched_subject_id"];

                // Step 2: Get session_name from tbl_academic_session and tbl_activescholars based on stud_active_id
                $sql = "
                    SELECT session_name
                    FROM tbl_academic_session AS a
                    INNER JOIN tbl_activescholars AS b ON b.stud_active_academic_session_id = a.session_id
                    WHERE b.stud_active_id = :request_sched_stud_active_id
                ";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":request_sched_stud_active_id", $assignStudId, PDO::PARAM_INT);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$result) {
                    throw new Exception("No matching session_name found for stud_active_id: " . $assignStudId);
                }

                $sessionName = $result["session_name"];

                // Step 3: Get the session_id from tbl_academic_session using session_name
                $sql = "SELECT session_id FROM tbl_academic_session WHERE session_name = :session_name";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":session_name", $sessionName);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    throw new Exception("No matching record found in tbl_academic_session for session_name: " . $sessionName);
                }
                $assignSessionId = $result["session_id"];

                // Step 4: Get the assignment_id from tbl_assignment_mode using the assignment_name passed from React
                $assignmentName = $json["assignment_name"];  // From React
                $sql = "SELECT assignment_id FROM tbl_assignment_mode WHERE assignment_name = :assignment_name";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":assignment_name", $assignmentName);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    throw new Exception("No matching record found in tbl_assignment_mode for assignment_name: " . $assignmentName);
                }
                $assignModeId = $result["assignment_id"];

                // Step 5: Get the dutyH_id from tbl_duty_hours using the dutyH_name passed from React
                $dutyHName = $json["dutyH_name"];  // From React
                $sql = "SELECT dutyH_id FROM tbl_duty_hours WHERE dutyH_name = :dutyH_name";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":dutyH_name", $dutyHName);
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$result) {
                    throw new Exception("No matching record found in tbl_duty_hours for dutyH_name: " . $dutyHName);
                }
                $assignDutyHoursId = $result["dutyH_id"];

                // Step 6: Insert into tbl_office_master and get the off_id (needed for assign_office_id)
                $sql = "INSERT INTO tbl_office_master (off_subject_id) VALUES (:off_subject_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":off_subject_id", $offSubjectId);
                $stmt->execute();
                $offId = $conn->lastInsertId();  // Get the last inserted off_id

                if (!$offId) {
                    throw new Exception("Failed to retrieve off_id after inserting into tbl_office_master.");
                }

                // Step 7: Insert into tbl_assign_scholars with retrieved values including assign_office_id
                $sql = "INSERT INTO tbl_assign_scholars (assign_stud_id, assign_duty_hours_id, assign_office_id, assign_mode_id, assign_session_id)
                        VALUES (:assign_stud_id, :assign_duty_hours_id, :assign_office_id, :assign_mode_id, :assign_session_id)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":assign_stud_id", $assignStudId);
                $stmt->bindParam(":assign_duty_hours_id", $assignDutyHoursId);
                $stmt->bindParam(":assign_office_id", $offId);  // This is the off_id from the last inserted record
                $stmt->bindParam(":assign_mode_id", $assignModeId);
                $stmt->bindParam(":assign_session_id", $assignSessionId);
                $stmt->execute();

                // Step 8: Update tbl_subjects to set sub_used = 1 for the given subject
                $sql = "UPDATE tbl_subjects SET sub_used = 0 WHERE sub_id = :sub_id";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(":sub_id", $offSubjectId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Commit the transaction
            $conn->commit();
            return 1; // Successful update and facilitator addition if applicable
        } catch (Exception $e) {
            // Rollback if any error occurs
            $conn->rollBack();
            return "Error: " . $e->getMessage(); // Return the error message
        }
    }
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$transaction = new Transaction();

switch ($operation) {
    case "sendEmail":
        echo $transaction->sendEmail($json);
        break;
    case "TwoFactorAuthentication":
        echo $transaction->TwoFactorAuthentication($json);
        break;
    case "getStudentsDetailsAndStudentDutyAssign":
        echo $transaction->getStudentsDetailsAndStudentDutyAssign($json);
        break;
    case "getAssignedScholars":
        echo $transaction->getAssignedScholars($json);
        break;
    case "getStudentDtr":
        echo $transaction->getStudentDtr($json);
        break;
    case "studentsAttendance":
        echo $transaction->studentsAttendance($json);
        break;
    case "submitStudentFacilitatorEvaluation":
        echo $transaction->submitStudentFacilitatorEvaluation($json);
        break;
    case "getStudentDutyData":
        echo $transaction->getStudentDutyData();
        break;
    case "getActiveAndAssignedScholars":
        echo $transaction->getActiveAndAssignedScholars();
        break;
    case "getActiveAndRenewedScholars":
        echo $transaction->getActiveAndRenewedScholars();
        break;
    case "getNearlyFinishedandFinishedScholar":
        echo $transaction->getNearlyFinishedandFinishedScholar();
        break;
    case "getAllStudentFacilitator":
        echo $transaction->getAllStudentFacilitator();
        break;
    case "getAllOfficeScholar":
        echo $transaction->getAllOfficeScholar();
        break;
    case "getScholarAllAvailableSchedule":
        echo $transaction->getScholarAllAvailableSchedule($json);
        break;
    case "getAllSubjects":
        echo $transaction->getAllSubjects();
        break;
    case "updateScholarsProfile":
        echo $transaction->updateScholarsProfile($json);
        break;
    case "updatePassword":
        echo $transaction->updatePassword($json);
        break;
    case "getRequestSchedule":
        echo json_encode($transaction->getRequestSchedule($json));
        break;
    case "sendRequestSchedule":
        echo $transaction->sendRequestSchedule($json);
        break;
    case "getRequestedSchedule":
        echo $transaction->getRequestedSchedule();
        break;
    case "updateRequestedScheduleStatusAndAddFacilitator":
        echo $transaction->updateRequestedScheduleStatusAndAddFacilitator($json);
        break;
    case "scholarAssignedChecker":
        echo json_encode($transaction->scholarAssignedChecker($json));
        break;
    case "updateLoginAttempts":
        echo $transaction->updateLoginAttempts($json);
        break;
    case "getStudentProfile":
        echo $transaction->getStudentProfile($json);
        break;
    case "getSupervisorProfile":
        echo $transaction->getSupervisorProfile($json);
        break;
    case "verifyAndUpdatePassword":
        echo $transaction->verifyAndUpdatePassword($json);
        break;
    case "updateTwoFactorAuthenticationStudent":
        echo $transaction->updateTwoFactorAuthenticationStudent($json);
        break;
    case "authenticationChecker":
        echo $transaction->authenticationChecker($json);
        break;
    case "updateAdvisorProfile":
        echo $transaction->updateAdvisorProfile($json);
        break;
    case "updateTwoFactorAuthenticationAdvisor":
        echo $transaction->updateTwoFactorAuthenticationAdvisor($json);
        break;
    case "verfityAndUpdateAdvisorPassword":
        echo $transaction->verfityAndUpdateAdvisorPassword($json);
        break;
    default:
        echo json_encode(["error" => "Invalid operation"]);
        break;
}
