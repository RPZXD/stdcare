<?php

class StudentVisit {
    private $conn;
    private $table_student = "student";
    private $table_visithome = "visithome";

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Fetch students with their visit status.
     * @param int $major The student's major (Stu_major).
     * @param int $room The student's room (Stu_room).
     * @param int $term The term for the visit (Term).
     * @param int $pee The year for the visit (Pee).
     * @return array The list of students with their visit status.
     */

    public function fetchStudentsWithVisitStatus($major, $room, $pee) {
        $query = "
            SELECT 
                s.Stu_no,
                s.Stu_id,
                CONCAT(s.Stu_pre, s.Stu_name, ' ', s.Stu_sur) AS FullName,
                s.Stu_major,
                s.Stu_room,
                s.Stu_status,
                CASE 
                    WHEN SUM(CASE WHEN v.Term = 1 THEN 1 ELSE 0 END) > 0 THEN 1
                    ELSE 0
                END AS visit_status1,
                CASE 
                    WHEN SUM(CASE WHEN v.Term = 2 THEN 1 ELSE 0 END) > 0 THEN 1
                    ELSE 0
                END AS visit_status2
            FROM {$this->table_student} s
            LEFT JOIN {$this->table_visithome} v
                ON s.Stu_id = v.Stu_id
                AND v.Pee = :pee
            WHERE s.Stu_major = :major
              AND s.Stu_room = :room
              AND s.Stu_status = 1
            GROUP BY s.Stu_no, s.Stu_id, s.Stu_pre, s.Stu_name, s.Stu_sur, s.Stu_major, s.Stu_room, s.Stu_status
            ORDER BY s.Stu_no ASC
        ";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind parameters
        $stmt->bindParam(':major', $major, PDO::PARAM_INT);
        $stmt->bindParam(':room', $room, PDO::PARAM_INT);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_INT);
    
        // Execute the query
        $stmt->execute();
    
        // Fetch all results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get visit data for a specific student, term, and year.
     * @param string $stuId The student ID.
     * @param int $term The term for the visit.
     * @param int $pee The year for the visit.
     * @return array|null The visit data or null if not found.
     */
    public function getVisitData($stuId, $term, $pee) {
        $query = "
            SELECT 
                v.visit_id,
                v.Stu_id,
                v.vh1,
                v.vh2,
                v.vh3,
                v.vh4,
                v.vh5,
                v.vh6,
                v.vh7,
                v.vh8,
                v.vh9,
                v.vh10,
                v.vh11,
                v.vh12,
                v.vh13,
                v.vh14,
                v.vh15,
                v.vh16,
                v.vh17,
                v.vh18,
                v.picture1,
                v.picture2,
                v.picture3,
                v.picture4,
                v.picture5,
                v.vh20,
                v.Term,
                v.Pee,
                s.Stu_id,
                s.Stu_no,
                s.Stu_pre,
                s.Stu_name,
                s.Stu_sur,
                s.Stu_major,
                s.Stu_room,
                s.Stu_status,
                s.Stu_addr,
                s.Stu_phone
            FROM {$this->table_visithome} v
            LEFT JOIN {$this->table_student} s
                ON v.Stu_id = s.Stu_id
            WHERE v.Stu_id = :stuId
              AND v.Term = :term
              AND v.Pee = :pee
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':stuId', $stuId, PDO::PARAM_STR);
        $stmt->bindParam(':term', $term, PDO::PARAM_INT);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Get student details by ID.
     * @param string $stuId The student ID.
     * @return array|null The student details or null if not found.
     */
    public function getStudentById($stuId) {
        $query = "
            SELECT 
                Stu_id,
                Stu_pre,
                Stu_name,
                Stu_sur,
                Stu_major,
                Stu_room,
                Stu_addr,
                Stu_phone
            FROM {$this->table_student}
            WHERE Stu_id = :stuId
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':stuId', $stuId, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    public function updateVisitData($data) {
        $fieldsToUpdate = [];
        $params = [];

        // Dynamically build the query based on non-null fields
        foreach ($data as $key => $value) {
            if ($value !== null && (strpos($key, 'vh') === 0 || strpos($key, 'picture') === 0 || $key === 'vh20')) {
                $fieldsToUpdate[] = "$key = :$key";
                $params[$key] = $value;
            }
        }

        if (empty($fieldsToUpdate)) {
            return false; // No fields to update
        }

        $query = "
            UPDATE {$this->table_visithome}
            SET " . implode(', ', $fieldsToUpdate) . "
            WHERE Stu_id = :stuId AND Term = :term
        ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        $stmt->bindValue(':stuId', $data['stuId'], PDO::PARAM_STR);
        $stmt->bindValue(':term', $data['term'], PDO::PARAM_INT);

        // Execute the query
        return $stmt->execute();
    }

    /**
     * Save visit data for a student.
     * @param array $data The visit data to save.
     * @return bool True if the data was saved successfully, false otherwise.
     */
    public function saveVisitData($data) {
        $query = "
            INSERT INTO {$this->table_visithome} (
                Stu_id, Term, Pee, vh1, vh2, vh3, vh4, vh5, vh6, vh7, vh8, vh9, vh10,
                vh11, vh12, vh13, vh14, vh15, vh16, vh17, vh18, vh20, picture1, picture2,
                picture3, picture4, picture5
            ) VALUES (
                :stuId, :term, :pee, :vh1, :vh2, :vh3, :vh4, :vh5, :vh6, :vh7, :vh8, :vh9, :vh10,
                :vh11, :vh12, :vh13, :vh14, :vh15, :vh16, :vh17, :vh18, :vh20, :picture1, :picture2,
                :picture3, :picture4, :picture5
            )
        ";

        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $stmt->bindParam(':stuId', $data['stuId'], PDO::PARAM_STR);
        $stmt->bindParam(':term', $data['term'], PDO::PARAM_INT);
        $stmt->bindParam(':pee', $data['pee'], PDO::PARAM_INT);
        $stmt->bindParam(':vh1', $data['vh1'], PDO::PARAM_INT);
        $stmt->bindParam(':vh2', $data['vh2'], PDO::PARAM_INT);
        $stmt->bindParam(':vh3', $data['vh3'], PDO::PARAM_INT);
        $stmt->bindParam(':vh4', $data['vh4'], PDO::PARAM_INT);
        $stmt->bindParam(':vh5', $data['vh5'], PDO::PARAM_INT);
        $stmt->bindParam(':vh6', $data['vh6'], PDO::PARAM_INT);
        $stmt->bindParam(':vh7', $data['vh7'], PDO::PARAM_INT);
        $stmt->bindParam(':vh8', $data['vh8'], PDO::PARAM_INT);
        $stmt->bindParam(':vh9', $data['vh9'], PDO::PARAM_INT);
        $stmt->bindParam(':vh10', $data['vh10'], PDO::PARAM_INT);
        $stmt->bindParam(':vh11', $data['vh11'], PDO::PARAM_INT);
        $stmt->bindParam(':vh12', $data['vh12'], PDO::PARAM_INT);
        $stmt->bindParam(':vh13', $data['vh13'], PDO::PARAM_INT);
        $stmt->bindParam(':vh14', $data['vh14'], PDO::PARAM_INT);
        $stmt->bindParam(':vh15', $data['vh15'], PDO::PARAM_INT);
        $stmt->bindParam(':vh16', $data['vh16'], PDO::PARAM_INT);
        $stmt->bindParam(':vh17', $data['vh17'], PDO::PARAM_INT);
        $stmt->bindParam(':vh18', $data['vh18'], PDO::PARAM_INT);
        $stmt->bindParam(':vh20', $data['vh20'], PDO::PARAM_STR);
        $stmt->bindParam(':picture1', $data['picture1'], PDO::PARAM_STR);
        $stmt->bindParam(':picture2', $data['picture2'], PDO::PARAM_STR);
        $stmt->bindParam(':picture3', $data['picture3'], PDO::PARAM_STR);
        $stmt->bindParam(':picture4', $data['picture4'], PDO::PARAM_STR);
        $stmt->bindParam(':picture5', $data['picture5'], PDO::PARAM_STR);

        // Execute the query
        return $stmt->execute();
    }


    /**
     * Get the total count of students visited.
     * @param string $class The class of the students.
     * @param string $room The room of the students.
     * @param string $term The term of the visit.
     * @param string $pee The year of the visit.
     * @return int The total count of students visited.
     */
    public function getTotalVisitCount($class, $room, $term, $pee) {
        $query = "
            SELECT DISTINCT COUNT(*) 
            FROM visithome, student 
            WHERE student.Stu_id = visithome.Stu_id 
              AND student.Stu_major = :class 
              AND student.Stu_room = :room
              AND student.Stu_status = 1 
              AND visithome.Term = :term
              AND visithome.Pee = :pee
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class', $class, PDO::PARAM_STR);
        $stmt->bindParam(':room', $room, PDO::PARAM_STR);
        $stmt->bindParam(':term', $term, PDO::PARAM_STR);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Fetch visit home data for students.
     * @param string $class The class of the students.
     * @param string $room The room of the students.
     * @param string $term The term of the visit.
     * @param string $pee The year of the visit.
     * @return array The visit home data.
     */
    public function fetchVisitHomeData($class, $room, $term, $pee) {
        $vh = array();
        for ($i = 1; $i <= 18; $i++) {
            for ($j = 1; $j <= 8; $j++) {
                $query = "
                    SELECT DISTINCT COUNT(*) 
                    FROM visithome, student 
                    WHERE student.Stu_id = visithome.Stu_id 
                      AND student.Stu_major = :class 
                      AND student.Stu_room = :room 
                      AND student.Stu_status = 1 
                      AND visithome.vh$i = :vh 
                      AND visithome.Term = :term 
                      AND visithome.Pee = :pee
                ";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':class', $class, PDO::PARAM_STR);
                $stmt->bindParam(':room', $room, PDO::PARAM_STR);
                $stmt->bindParam(':vh', $j, PDO::PARAM_INT);
                $stmt->bindParam(':term', $term, PDO::PARAM_STR);
                $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
                $stmt->execute();
                $vh[$i][$j] = $stmt->fetchColumn();
            }
        }

        return $vh;
    }

    /**
     * Get the question and answer for a specific item type and list.
     * @param int $item_type The item type.
     * @param int $item_list The item list.
     * @return array The question and answer.
     */
    public function getQuestionAnswer($item_type, $item_list) {
        $questions = array(
            1 => array("1. บ้านที่อยู่อาศัย" => array("บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น")),
            2 => array("2. ระยะทางระหว่างบ้านกับโรงเรียน" => array("1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กิโลเมตรขึ้นไป")),
            3 => array("3. การเดินทางไปโรงเรียนของนักเรียน" => array("เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่งรถโดยสาร", "อื่นๆ", "รถไฟ", "ผู้ปกครองรับ-ส่ง")),
            4 => array("4. สภาพแวดล้อมของบ้าน" => array("ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง")),
            5 => array("5. อาชีพของผู้ปกครอง" => array("เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ")),
            6 => array("6. สถานที่ทำงานของบิดามารดา" => array("ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ")),
            7 => array("7. สถานภาพของบิดามารดา" => array("บิดามารดาอยู่ด้วยกัน", "บิดามารดาหย่าร้างกัน", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "บิดาและมารดาถึงแก่กรรม")),
            8 => array("8. วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน" => array("เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ")),
            9 => array("9. โรคประจำตัวของนักเรียน" => array("ไม่มี", "มี")),
            10 => array("10. ความสัมพันธ์ของสมาชิกในครอบครัว" => array("อบอุ่น", "เฉยๆ", "ห่างเหิน")),
            11 => array("11. หน้าที่รับผิดชอบภายในบ้าน" => array("มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี")),
            12 => array("12. สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด" => array("พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ")),
            13 => array("13. รายได้กับการใช้จ่ายในครอบครัว" => array("เพียงพอ", "ไม่เพียงพอในบางครั้ง", "ขัดสน")),
            14 => array("14. ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น" => array("เพื่อนรุ่นเดียวกัน", "เพื่อนรุ่นน้อง", "เพื่อนรุ่นพี่", "เพื่อนทุกรุ่น")),
            15 => array("15. ความต้องการของผู้ปกครอง เมื่อนักเรียนจบชั้นสูงสุดของโรงเรียน" => array("ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ")),
            16 => array("16. เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร" => array("พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ")),
            17 => array("17. ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน" => array("พอใจ", "เฉยๆ", "ไม่พอใจ")),
            18 => array("18. ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน" => array("พอใจ", "เฉยๆ", "ไม่พอใจ"))
        );

        $questionKey = array_keys($questions[$item_type])[0];
        $question = $questionKey;
        $answer = $questions[$item_type][$questionKey][$item_list - 1];
        return array('question' => $question, 'answer' => $answer);
    }

    /**
     * Get all possible answers for a specific question type.
     * @param int $item_type The item type/question number.
     * @return array Array of all possible answers for this question.
     */
    public function getAllAnswersForQuestion($item_type) {
        $questions = array(
            1 => array("1. บ้านที่อยู่อาศัย" => array("บ้านของตนเอง", "บ้านเช่า", "อาศัยอยู่กับผู้อื่น")),
            2 => array("2. ระยะทางระหว่างบ้านกับโรงเรียน" => array("1-5 กิโลเมตร", "6-10 กิโลเมตร", "11-15 กิโลเมตร", "16-20 กิโลเมตร", "20 กิโลเมตรขึ้นไป")),
            3 => array("3. การเดินทางไปโรงเรียนของนักเรียน" => array("เดิน", "รถจักรยาน", "รถจักรยานยนต์", "รถยนต์ส่วนตัว", "รถรับส่งรถโดยสาร", "อื่นๆ", "รถไฟ", "ผู้ปกครองรับ-ส่ง")),
            4 => array("4. สภาพแวดล้อมของบ้าน" => array("ดี", "พอใช้", "ไม่ดี", "ควรปรับปรุง")),
            5 => array("5. อาชีพของผู้ปกครอง" => array("เกษตรกร", "ค้าขาย", "รับราชการ", "รับจ้าง", "อื่นๆ")),
            6 => array("6. สถานที่ทำงานของบิดามารดา" => array("ในอำเภอเดียวกัน", "ในจังหวัดเดียวกัน", "ต่างจังหวัด", "ต่างประเทศ")),
            7 => array("7. สถานภาพของบิดามารดา" => array("บิดามารดาอยู่ด้วยกัน", "บิดามารดาหย่าร้างกัน", "บิดาถึงแก่กรรม", "มารดาถึงแก่กรรม", "บิดาและมารดาถึงแก่กรรม")),
            8 => array("8. วิธีการที่ผู้ปกครองอบรมเลี้ยงดูนักเรียน" => array("เข้มงวดกวดขัน", "ตามใจ", "ใช้เหตุผล", "ปล่อยปละละเลย", "อื่นๆ")),
            9 => array("9. โรคประจำตัวของนักเรียน" => array("ไม่มี", "มี")),
            10 => array("10. ความสัมพันธ์ของสมาชิกในครอบครัว" => array("อบอุ่น", "เฉยๆ", "ห่างเหิน")),
            11 => array("11. หน้าที่รับผิดชอบภายในบ้าน" => array("มีหน้าที่ประจำ", "ทำเป็นครั้งคราว", "ไม่มี")),
            12 => array("12. สมาชิกในครอบครัวนักเรียนสนิทสนมกับใครมากที่สุด" => array("พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ")),
            13 => array("13. รายได้กับการใช้จ่ายในครอบครัว" => array("เพียงพอ", "ไม่เพียงพอในบางครั้ง", "ขัดสน")),
            14 => array("14. ลักษณะเพื่อนเล่นที่บ้านของนักเรียนโดยปกติเป็น" => array("เพื่อนรุ่นเดียวกัน", "เพื่อนรุ่นน้อง", "เพื่อนรุ่นพี่", "เพื่อนทุกรุ่น")),
            15 => array("15. ความต้องการของผู้ปกครอง เมื่อนักเรียนจบชั้นสูงสุดของโรงเรียน" => array("ศึกษาต่อ", "ประกอบอาชีพ", "อื่นๆ")),
            16 => array("16. เมื่อนักเรียนมีปัญหา นักเรียนจะปรึกษาใคร" => array("พ่อ", "แม่", "พี่สาว", "น้องสาว", "พี่ชาย", "น้องชาย", "อื่นๆ")),
            17 => array("17. ความรู้สึกของผู้ปกครองที่มีต่อครูที่มาเยี่ยมบ้าน" => array("พอใจ", "เฉยๆ", "ไม่พอใจ")),
            18 => array("18. ทัศนคติ/ความรู้สึกของผู้ปกครองที่มีต่อโรงเรียน" => array("พอใจ", "เฉยๆ", "ไม่พอใจ"))
        );

        if (isset($questions[$item_type])) {
            $questionKey = array_keys($questions[$item_type])[0];
            return $questions[$item_type][$questionKey];
        }
        
        return array(); // Return empty array if question type not found
    }
}