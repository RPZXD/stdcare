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
        $query = "
            UPDATE {$this->table_visithome}
            SET 
                vh1 = :vh1, vh2 = :vh2, vh3 = :vh3, vh4 = :vh4, vh5 = :vh5,
                vh6 = :vh6, vh7 = :vh7, vh8 = :vh8, vh9 = :vh9, vh10 = :vh10,
                vh11 = :vh11, vh12 = :vh12, vh13 = :vh13, vh14 = :vh14, vh15 = :vh15,
                vh16 = :vh16, vh17 = :vh17, vh18 = :vh18, vh19 = :vh19, vh20 = :vh20,
                picture1 = :picture1, picture2 = :picture2, picture3 = :picture3, 
                picture4 = :picture4, picture5 = :picture5
            WHERE Stu_id = :stuId AND Term = :term
        ";
    
        $stmt = $this->conn->prepare($query);
    
        // Bind parameters
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
        $stmt->bindParam(':vh19', $data['vh19'], PDO::PARAM_INT);
        $stmt->bindParam(':vh20', $data['vh20'], PDO::PARAM_STR);
        $stmt->bindParam(':picture1', $data['picture1'], PDO::PARAM_STR);
        $stmt->bindParam(':picture2', $data['picture2'], PDO::PARAM_STR);
        $stmt->bindParam(':picture3', $data['picture3'], PDO::PARAM_STR);
        $stmt->bindParam(':picture4', $data['picture4'], PDO::PARAM_STR);
        $stmt->bindParam(':picture5', $data['picture5'], PDO::PARAM_STR);
        $stmt->bindParam(':stuId', $data['stuId'], PDO::PARAM_INT);
        $stmt->bindParam(':term', $data['term'], PDO::PARAM_INT);
    
        // Execute the query
        return $stmt->execute();
    }
}
?>