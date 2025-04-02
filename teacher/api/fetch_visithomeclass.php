<?php
session_start();
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$studentVisit = new StudentVisit($db);

$class = $_GET['class'];
$room = $_GET['room'];
$term = $_GET['term'];
$pee = $_GET['pee'];
try {

    // Fetch total student count
    $total = $studentVisit->getTotalVisitCount($class, $room, $term, $pee);

    // Fetch visit home data
    $vh = $studentVisit->fetchVisitHomeData($class, $room, $term, $pee);

    // Prepare data for JSON response
    $data = array();
    $no = 1;
    $current_question = ""; // Store the current question
    $color_map = array(); // Map to store generated colors for each item type
    
    // Get all questions information for reference
    $allQuestions = array();
    for ($i = 1; $i <= 18; $i++) {
        $qa = $studentVisit->getQuestionAnswer($i, 1); // Get first answer to extract the question
        $item_type = $qa['question'];
        $allQuestions[$i] = $item_type;
    }
    
    // Process each item type and include all possible answers
    foreach ($allQuestions as $i => $question) {
        // Generate or retrieve background color for this item type
        if (!isset($color_map[$question])) {
            $color_map[$question] = '#' . substr(md5(mt_rand()), 0, 6); // Random hex color
        }
        $bg_color = $color_map[$question];
        
        // Get all possible answers for this question
        $allAnswers = $studentVisit->getAllAnswersForQuestion($i);
        
        $isFirstItem = true; // Flag to track if this is the first item of the question group
        
        // Process each possible answer
        foreach ($allAnswers as $j => $answer) {
            // Check if we have data for this answer
            $count = isset($vh[$i][$j+1]) ? $vh[$i][$j+1] : 0;
            $percent = ($total > 0) ? round(($count / $total) * 100, 2) : 0;
            
            // Add to data array
            $data[] = array(
                'item_type' => $isFirstItem ? $question : $question, // Always include the item_type
                'item_list' => $answer,
                'Stu_total' => $count,
                'Persent' => $percent,
                'bg_color' => $bg_color,
                'display_header' => $isFirstItem // New field to indicate if header should be displayed
            );
            
            $isFirstItem = false; // All subsequent items are not first items
        }
    }

    // Output JSON response
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
