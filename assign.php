<?php
include "headers.php";

class User
{

    function addAssignment($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_stud_assignment(asgn_id, asgn_office_id, asgn_sy, asgn_sem)
        VALUES(:asgn_id, :asgn_office_id, :asgn_office_id, :asgn_sy, :asgn_sem)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("asgn_id", $json["asgn_id"]);
        $stmt->bindParam("asgn_office_id", $json["asgn_office_id"]);
        $stmt->bindParam("asgn_office_id", $json["asgn_office_id"]);
        $stmt->bindParam("asgn_sy", $json["asgn_sy"]);
        $stmt->bindParam("asgn_sem", $json["asgn_sem"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function addAssignment_Schedule($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_stud_assignment_schedule(sched_id, sched_assignment_id, sched_asgn_stud_id, sched_day, sched_start_time,
        sched_end_time, sched_id, sched_assignment_id, sched_asgn_stud_id, sched_day, sched_start_time, sched_end_time	)
        VALUES(:sched_id, :sched_assignment_id, :sched_asgn_stud_id, :sched_day, :sched_start_time, :sched_end_time, :sched_id, 
        :sched_assignment_id, :sched_asgn_stud_id, :sched_day, :sched_start_time, :sched_end_time	)";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddSemester($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_semester(sem_id, sem_sy, sem_sem, sem_status)
        VALUES(:sem_id, :sem_sy, :sem_sem, :sem_status)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("sem_id", $json["sem_id"]);
        $stmt->bindParam("sem_sy", $json["sem_sy"]);
        $stmt->bindParam("sem_sem", $json["sem_sem"]);
        $stmt->bindParam("sem_status", $json["sem_status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddOfficeType($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_office_type(offT_id, offT_name, off_building_id)
        VALUES(:offT_id, :offT_name, :off_building_id)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("offT_id", $json["offT_id"]);
        $stmt->bindParam("offT_name", $json["offT_name"]);
        $stmt->bindParam("off_building_id", $json["off_building_id"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddOfficeSchedule($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_office_schedule(offSched_id, offSched_office_id, offSched_day, offSched_start_time, offSched_end_time, offSched_f2f)
        VALUES(:offSched_id, :offSched_office_id, :offSched_day, :offSched_start_time, :offSched_end_timem, :offSched_f2f)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("offSched_id", $json["offSched_id"]);
        $stmt->bindParam("offSched_office_id", $json["offSched_office_id"]);
        $stmt->bindParam("offSched_day", $json["offSched_day"]);
        $stmt->bindParam("offSched_start_time", $json["offSched_start_time"]);
        $stmt->bindParam("offSched_end_time", $json["offSched_end_time"]);
        $stmt->bindParam("offSched_f2f", $json["offSched_f2f"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddOffencesChart($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_offences_chart(chart_id, chart_scholar_id, chart_offense_id, chart_complaint_id, chart_encoded_by, chart_remarks, chart_sy, chart_sem)
        VALUES(:chart_id, :chart_scholar_id, :chart_offense_id, :chart_complaint_id, :chart_encoded_by, :chart_remarks, :chart_sy, :chart_sem)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("chart_id", $json["chart_id"]);
        $stmt->bindParam("chart_scholar_id", $json["chart_scholar_id"]);
        $stmt->bindParam("chart_offense_id", $json["chart_offense_id"]);
        $stmt->bindParam("chart_complaint_id", $json["chart_complaint_id"]);
        $stmt->bindParam("chart_encoded_by", $json["chart_encoded_by"]);
        $stmt->bindParam("chart_remarks", $json["chart_remarks"]);
        $stmt->bindParam("chart_sy", $json["chart_sy"]);
        $stmt->bindParam("chart_sem", $json["chart_sem"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddJobType($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_job_type(job_id, job_name)
        VALUES(:job_id, :job_name)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("job_id", $json["job_id"]);
        $stmt->bindParam("job_name", $json["job_name"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddFreshmenRequirmentDetails($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_freshmen_requirement_details(req_id, req_scholar_id, req_requirementM_id, req_sy, req_sy)
        VALUES(:req_id, :req_scholar_id	, :req_requirementM_id, :req_sy, :req_sem)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("req_id", $json["req_id"]);
        $stmt->bindParam("req_scholar_id", $json["req_scholar_id"]);
        $stmt->bindParam("req_requirementM_id", $json["req_requirementM_id"]);
        $stmt->bindParam("req_sy", $json["req_sy"]);
        $stmt->bindParam("req_sem", $json["req_sem"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddFreshmenRequirmentDetailsMaster($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_freshmen_requirement_master(fresh_reqM_id, fresh_reqM_name, fresh_reqM_status)
        VALUES(:fresh_reqM_id, :fresh_reqM_name, :fresh_reqM_status)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("fresh_reqM_id", $json["fresh_reqM_id"]);
        $stmt->bindParam("fresh_reqM_name", $json["fresh_reqM_name"]);
        $stmt->bindParam("fresh_reqM_status", $json["fresh_reqM_status"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }
    function AddFreshmenReferral($json)
    {
        include "connection.php";
        $json = json_decode($json, true);
        $sql = "INSERT INTO tbl_freshmen_referral(freshmen_ref, freshmen_ref_scholar_id, freshmen_ref_last_name, freshmen_ref_first_name, ref_sem)
        VALUES(:freshmen_ref, :freshmen_ref_scholar_id, :freshmen_ref_last_name, :freshmen_ref_first_name, :ref_sem)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam("freshmen_ref", $json["freshmen_ref"]);
        $stmt->bindParam("freshmen_ref_scholar_id", $json["freshmen_ref_scholar_id"]);
        $stmt->bindParam("freshmen_ref_last_name", $json["freshmen_ref_last_name"]);
        $stmt->bindParam("freshmen_ref_first_name", $json["freshmen_ref_first_name"]);
        $stmt->bindParam("ref_sem", $json["ref_sem"]);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? 1 : 0;
    }

    function getScholarList()
    {
        include "connection.php";
        $sql = "SELECT a.stud_first_name, a.stud_last_name, b.supM_first_name, b.supM_last_name WHERE stype_id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    function getAssignmentList()
    {
        include "connection.php";
        $sql = "SELECT CONCAT(a.stud_first_name, ' ' , a.stud_last_name) AS Fullname, CONCAT(c.supM_first_name, ' ', c.supM_last_name) AS SupervisorFullname, b.assign_duty_hours, d.timeShed_name, e.time_out_name 
        FROM tbl_scholars a 
        INNER JOIN tbl_assign_scholars b ON b.assign_stud_id = a.stud_id
        INNER JOIN tbl_supervisor_master c ON c.supM_id = b.assign_supM_id
        INNER JOIN tbl_time_schedule_in d ON d.timeSched_id = b.assign_time_schedule_in
        INNER JOIN tbl_time_schedule_out e ON e.time_out_id = b.assign_time_schedule_out";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    function getListScholar()
    {
        include "connection.php";
        $sql = "SELECT CONCAT(a.stud_first_name, ' ' ,  a.stud_last_name) AS Fullname, b.type_name
        FROM tbl_scholars a
        INNER JOIN tbl_scholarship_type b ON b.type_id = a.stud_scholarship_type_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount() > 0 ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
}
$json = isset($_POST["json"]) ? $_POST["json"] : "0";
$operation = isset($_POST["operation"]) ? $_POST["operation"] : "0";
$user = new User();
switch ($operation) {
    case "addAssignment":
        echo $user->addAssignment($json);
        break;
    case "addAssignment_Schedule":
        echo $user->addAssignment_Schedule($json);
        break;
    case "AddSemester":
        echo $user->AddSemester($json);
        break;
    case "AddOfficeType":
        echo $user->AddOfficeType($json);
        break;
    case "AddOfficeSchedule":
        echo $user->AddOfficeSchedule($json);
        break;
    case "AddOffencesChart":
        echo $user->AddOffencesChart($json);
        break;
    case "AddJobType":
        echo $user->AddJobType($json);
        break;
    case "AddFreshmenRequirmentDetails":
        echo $user->AddFreshmenRequirmentDetails($json);
        break;
    case "AddFreshmenRequirmentDetailsMaster":
        echo $user->AddFreshmenRequirmentDetailsMaster($json);
        break;
    case "AddFreshmenReferral":
        echo $user->AddFreshmenReferral($json);
        break;
    case "getScholarList":
        echo $user->getScholarList();
        break;
    case "getAssignmentList":
        echo json_encode($user->getAssignmentList());
        break;
    case "getListScholar":
        echo json_encode($user->getListScholar());
        break;
    default:
        echo "WALAY " . $operation . " NGA OPERATION SA UBOS HAHHAHA BOBO NOYNAY";
        break;
}
