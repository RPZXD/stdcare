<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

if (isset($_POST['stuid'])) {
    $stuid = $_POST['stuid'];

    // Perform a simple SQL query (replace with your actual query)
    $select_stmt = $db->prepare("SELECT * FROM student WHERE Stu_id = :stu_id AND Stu_status = 1");
    $select_stmt->execute([
        ':stu_id' => $stuid,
    ]);
    $results = $select_stmt->fetch(PDO::FETCH_ASSOC);

    if ($results) {
        // Display the search results
        echo    "<div class='mb-3'>
                <img id='image-preview1' class='profile-user-img img-fluid img-rounded'
                src='https://student.phichai.ac.th/photo/".$results['Stu_picture']."'
                alt='".$results['Stu_pre'].$results['Stu_name']."&nbsp;&nbsp;".$results['Stu_sur']."' style='height:275px;width:auto;'>
                </div>
                "
        ;
        echo    " <div class='mb-3'>
                    <b> &nbsp;&nbsp;เลขประจำตัวนักเรียน :</b> ".$results['Stu_id']."
                </div>
                "  
        ;
        echo    " <div class='mb-3'>
                    <b> &nbsp;&nbsp;ชื่อ-นามสกุล :</b> ".$results['Stu_pre'].$results['Stu_name']."&nbsp;&nbsp;".$results['Stu_sur']."
                </div>
                "  
        ;
        echo    " <div class='mb-3'>
                    <b> &nbsp;&nbsp;ชั้น :</b> ม.".$results['Stu_major']."/".$results['Stu_room']."
                </div>
                "  
        ;
    } else {
        echo "<p>ไม่พบข้อมูล</p>";
    }
}
?>
