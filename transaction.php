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
        SELECT 
            a.stud_id, 
            a.stud_name AS StudentFullname, 
            e.sub_code, 
            e.sub_descriptive_title, 
            e.sub_section, 
            e.sub_time, 
            e.sub_room, 
            f.day_name AS F2F_Day, 
            n.day_name AS RC_Day, 
            m.day_name AS F2F_Day_Office,
            g.supM_name AS AdvisorFullname, 
            i.dutyH_name AS TotalDutyHours, 
            dept_name, 
            d.offT_time, 
            j.build_name, 
            h.learning_name, 
            i.dutyH_name - (
                SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out))
                FROM tbl_dtr AS k2
                WHERE k2.dtr_assign_id = b.assign_id
                AND k2.dtr_current_time_in <= k.dtr_current_time_in
            ) AS RemainingHours,
            (
                SELECT SUM(TIMESTAMPDIFF(HOUR, k2.dtr_current_time_in, k2.dtr_current_time_out))
                FROM tbl_dtr AS k2
                WHERE k2.dtr_assign_id = b.assign_id
            ) AS TotalRenderedHours,
            b.assign_render_status
        FROM tbl_scholars AS a
        LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
        LEFT JOIN tbl_office_master AS c ON c.off_id = b.assign_office_id
        LEFT JOIN tbl_office_type AS d ON d.offT_id = c.off_type_id
        LEFT JOIN tbl_subjects AS e ON e.sub_id = c.off_subject_id
        LEFT JOIN tbl_day AS f ON f.day_id = e.sub_day_f2f_id 
        LEFT JOIN tbl_supervisors_master AS g ON g.supM_id = e.sub_supM_id
        LEFT JOIN tbl_learning_modalities AS h ON h.learning_id = e.sub_learning_modalities_id
        LEFT JOIN tbl_duty_hours AS i ON i.dutyH_id = b.assign_duty_hours_id
        LEFT JOIN tbl_building AS j ON j.build_id = d.off_build_id
        LEFT JOIN tbl_dtr AS k ON k.dtr_assign_id = b.assign_id
        LEFT JOIN tbl_day AS m ON m.day_id = d.offT_day_id
        LEFT JOIN tbl_day AS n ON n.day_id = e.sub_day_rc_id 
        LEFT JOIN tbl_department AS o ON o.dept_id = d.offT_dept_id
        WHERE a.stud_id = :stud_id
        ORDER BY k.dtr_current_time_in DESC 
        LIMIT 1;
    ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':stud_id', $json['stud_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['RemainingHours'] <= 0) {
            $updateSql = "
            UPDATE tbl_assign_scholars
            SET assign_render_status = 1
            WHERE assign_stud_id = :stud_id
        ";

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(':stud_id', $json['stud_id']);
            $updateStmt->execute();
        }

        return json_encode($result);
    }



    function getStudentDtr($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "SELECT 
                a.stud_id, 
                a.stud_name AS StudentFullname, 
                c.dtr_date, 
                d.session_name, 
                TIME(c.dtr_current_time_in) AS dtr_time_in, 
                TIME(c.dtr_current_time_out) AS dtr_time_out,
                TIMEDIFF(TIME(c.dtr_current_time_out), TIME(c.dtr_current_time_in)) AS TotalRendered
            FROM 
                tbl_scholars AS a
            LEFT JOIN 
                tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
            LEFT JOIN 
                tbl_dtr AS c ON c.dtr_assign_id = b.assign_id
            LEFT JOIN 
                tbl_academic_session AS d ON d.session_id = b.assign_session_id
            WHERE 
                a.stud_id = :stud_id
            ORDER BY 
                c.dtr_date";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':stud_id', $json['stud_id']);
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
        $scannedID = $json['stud_id'];
        $currentDate = date('Y-m-d'); // Today's date

        // Step 1: Fetch the latest DTR data for the scanned student
        $sql = "
        SELECT a.stud_id, b.assign_id AS assign_id, c.dtr_id, c.dtr_date, c.dtr_current_time_in, c.dtr_current_time_out
        FROM tbl_scholars AS a
        INNER JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
        LEFT JOIN tbl_dtr AS c ON c.dtr_assign_id = b.assign_id AND c.dtr_date = :currentDate
        WHERE a.stud_id = :scannedID
        ORDER BY c.dtr_date DESC LIMIT 1
    ";
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
        $sqlSubSup = "SELECT a.stud_id, a.stud_name AS Fullname, a.stud_contactNumber, a.stud_email,
                              sub_code, sub_descriptive_title, h.sub_section, sub_time, sub_room,
                              g.dutyH_name
                       FROM tbl_scholars AS a
                       LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
                       LEFT JOIN tbl_office_master AS c ON c.off_id = b.assign_office_id
                       LEFT JOIN tbl_office_type AS d ON d.offT_id = c.off_type_id
                       LEFT JOIN tbl_duty_hours AS g ON g.dutyH_id = b.assign_duty_hours_id
                       LEFT JOIN tbl_subjects AS h ON h.sub_id = c.off_subject_id
                       WHERE h.sub_supM_id = :supM_id";

        $stmtSubSup = $conn->prepare($sqlSubSup);
        $stmtSubSup->bindParam(":supM_id", $supM_id);
        $stmtSubSup->execute();
        $resultSubSup = $stmtSubSup->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultSubSup as $row) {
            $filteredResult[] = [
                'stud_id' => $row['stud_id'],
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
        $sqlOffTSup = "SELECT a.stud_id, a.stud_name AS Fullname, a.stud_contactNumber, a.stud_email,
                              g.dutyH_name, e.build_name, f.day_name, b.assign_render_status,
                              dept_name
                       FROM tbl_scholars AS a
                       LEFT JOIN tbl_assign_scholars AS b ON b.assign_stud_id = a.stud_id
                       LEFT JOIN tbl_office_master AS c ON c.off_id = b.assign_office_id
                       LEFT JOIN tbl_office_type AS d ON d.offT_id = c.off_type_id
                       LEFT JOIN tbl_building AS e ON e.build_id = d.off_build_id
                       LEFT JOIN tbl_day AS f ON f.day_id = d.offT_day_id
                       LEFT JOIN tbl_duty_hours AS g ON g.dutyH_id = b.assign_duty_hours_id
                       LEFT JOIN tbl_department AS h ON h.dept_id = d.offT_dept_id
                       WHERE d.offT_supM_id = :supM_id";

        $stmtOffTSup = $conn->prepare($sqlOffTSup);
        $stmtOffTSup->bindParam(":supM_id", $supM_id);
        $stmtOffTSup->execute();
        $resultOffTSup = $stmtOffTSup->fetchAll(PDO::FETCH_ASSOC);

        foreach ($resultOffTSup as $row) {
            $filteredResult[] = [
                'stud_id' => $row['stud_id'],
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
}

$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$transaction = new Transaction();

switch ($operation) {
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
    case "submitStudentEvaluation":
        echo $transaction->submitStudentFacilitatorEvaluation($json);
        break;
    default:
        break;
}
