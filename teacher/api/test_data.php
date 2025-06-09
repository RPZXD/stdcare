<?php
// Test database connection and API functionality
header('Content-Type: application/json; charset=utf-8');

// Simple test without database for now
$testData = [
    'success' => true,
    'total' => 45,
    'data' => [
        [
            'no' => 1,
            'question' => 'การเยี่ยมบ้านนักเรียน',
            'student_name' => 'นายทดสอบ ระบบ',
            'student_id' => '001',
            'visit_count' => 3,
            'percentage' => 75,
            'color' => '#EF4444',
            'status' => 'ดี'
        ],
        [
            'no' => 2,
            'question' => 'การติดตามพฤติกรรม',
            'student_name' => 'นางสาวทดสอบ การทำงาน',
            'student_id' => '002',
            'visit_count' => 2,
            'percentage' => 50,
            'color' => '#3B82F6',
            'status' => 'ปานกลาง'
        ],
        [
            'no' => 3,
            'question' => 'การช่วยเหลือครอบครัว',
            'student_name' => 'นายตัวอย่าง ข้อมูล',
            'student_id' => '003',
            'visit_count' => 4,
            'percentage' => 100,
            'color' => '#10B981',
            'status' => 'ดีมาก'
        ]
    ],
    'summary' => [
        'total_students' => 45,
        'total_visits' => 9,
        'average_percentage' => 75,
        'categories' => [
            ['name' => 'ดีมาก', 'count' => 1, 'color' => '#10B981'],
            ['name' => 'ดี', 'count' => 1, 'color' => '#3B82F6'],
            ['name' => 'ปานกลาง', 'count' => 1, 'color' => '#F59E0B']
        ]
    ],
    'chart_data' => [
        'labels' => ['ดีมาก', 'ดี', 'ปานกลาง'],
        'data' => [1, 1, 1],
        'colors' => ['#10B981', '#3B82F6', '#F59E0B']
    ]
];

echo json_encode($testData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
