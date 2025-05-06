<?php

class AttendanceSummary
{
    public $students_all;
    public $class;
    public $room;
    public $date;
    public $term;
    public $pee;

    public $status_labels = [
        '1' => ['label' => 'มาเรียน', 'emoji' => '✅'],
        '2' => ['label' => 'ขาดเรียน', 'emoji' => '❌'],
        '3' => ['label' => 'มาสาย', 'emoji' => '🕒'],
        '4' => ['label' => 'ลาป่วย', 'emoji' => '🤒'],
        '5' => ['label' => 'ลากิจ', 'emoji' => '📝'],
        '6' => ['label' => 'กิจกรรม', 'emoji' => '🎉'],
    ];

    public $status_count = ['1'=>0,'2'=>0,'3'=>0,'4'=>0,'5'=>0,'6'=>0];
    public $status_names = ['1'=>[],'2'=>[],'3'=>[],'4'=>[],'5'=>[],'6'=>[]];

    public function __construct($students_all, $class, $room, $date, $term, $pee)
    {
        $this->students_all = $students_all;
        $this->class = $class;
        $this->room = $room;
        $this->date = $date;
        $this->term = $term;
        $this->pee = $pee;

        foreach ($students_all as $s) {
            $st = $s['attendance_status'] ?? null;
            if ($st && isset($this->status_count[$st])) {
                $this->status_count[$st]++;
                if ($st !== '1') {
                    $this->status_names[$st][] = $s['Stu_pre'].$s['Stu_name'].' '.$s['Stu_sur'].' ('.$s['Stu_no'].')';
                }
            }
        }
    }

    public function getTextSummary()
    {
        $total = count($this->students_all);
        $lines = [];
        $lines[] = "สรุปการมาเรียน ม.{$this->class}/{$this->room} วันที่ ".thaiDateShort($this->date);
        foreach ($this->status_labels as $key => $info) {
            $percent = $total ? round($this->status_count[$key]*100/$total,1) : 0;
            $lines[] = "{$info['emoji']} {$info['label']}: {$this->status_count[$key]} คน ($percent%)";
            if ($key !== '1' && !empty($this->status_names[$key])) {
                $lines[] = " - ".implode(", ", $this->status_names[$key]);
            }
        }
        return implode("\n", $lines);
    }

    public function getFlexMessage()
    {
        $total = count($this->students_all);
        $status_labels = $this->status_labels;
        $status_count = $this->status_count;
        $status_names = $this->status_names;
        $class = $this->class;
        $room = $this->room;
        $date = $this->date;
    
        $flex = [
            "type" => "bubble",
            "size" => "mega",
            "header" => [
                "type" => "box",
                "layout" => "vertical",
                "backgroundColor" => "#1B8F3A",
                "contents" => [[
                    "type" => "text",
                    "text" => "📚 สรุปการมาเรียน",
                    "weight" => "bold",
                    "size" => "xl",
                    "color" => "#ffffff",
                    "align" => "center"
                ]]
            ],
            "body" => [
                "type" => "box",
                "layout" => "vertical",
                "spacing" => "md",
                "contents" => [
                    [
                        "type" => "text",
                        "text" => "ชั้น ม.$class/$room",
                        "weight" => "bold",
                        "size" => "lg",
                        "color" => "#14532d",
                        "align" => "center"
                    ],
                    [
                        "type" => "text",
                        "text" => thaiDateShort($date),
                        "size" => "sm",
                        "color" => "#6b7280",
                        "align" => "center"
                    ],
                    [
                        "type" => "separator",
                        "margin" => "md"
                    ],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "spacing" => "sm",
                        "margin" => "md",
                        "contents" => array_map(function($key) use ($status_labels, $status_count, $total) {
                            $info = $status_labels[$key];
                            $percent = $total ? round($status_count[$key]*100/$total,1) : 0;
                            $colorMap = [
                                '1' => "#22c55e",
                                '2' => "#ef4444",
                                '3' => "#eab308",
                                '4' => "#3b82f6",
                                '5' => "#a21caf",
                                '6' => "#ec4899",
                            ];
                            $bgColor = $key === '1' ? "#f0fdf4" : "#f9fafb";
                            return [
                                "type" => "box",
                                "layout" => "horizontal",
                                "backgroundColor" => $bgColor,
                                "cornerRadius" => "md",
                                "paddingAll" => "md",
                                "contents" => [
                                    [
                                        "type" => "text",
                                        "text" => $info['emoji'],
                                        "size" => "xl",
                                        "flex" => 1,
                                        "align" => "center"
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => $info['label'],
                                        "size" => "md",
                                        "weight" => "bold",
                                        "color" => $colorMap[$key],
                                        "flex" => 3
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "{$status_count[$key]} คน",
                                        "size" => "md",
                                        "align" => "end",
                                        "color" => "#111827",
                                        "flex" => 2
                                    ],
                                    [
                                        "type" => "text",
                                        "text" => "($percent%)",
                                        "size" => "xs",
                                        "align" => "end",
                                        "color" => "#6b7280",
                                        "flex" => 2
                                    ]
                                ]
                            ];
                        }, array_keys($status_labels))
                    ],
                    [
                        "type" => "separator",
                        "margin" => "md"
                    ],
                    [
                        "type" => "text",
                        "text" => "🚨 รายชื่อที่ไม่มาเรียนปกติ",
                        "weight" => "bold",
                        "size" => "md",
                        "color" => "#b91c1c",
                        "margin" => "md"
                    ],
                    [
                        "type" => "box",
                        "layout" => "vertical",
                        "spacing" => "xs",
                        "margin" => "sm",
                        "contents" => array_reduce(array_keys($status_labels), function($carry, $key) use ($status_labels, $status_names) {
                            if ($key === '1') return $carry;
                            if (!empty($status_names[$key])) {
                                $carry[] = [
                                    "type" => "text",
                                    "text" => $status_labels[$key]['emoji']." ".$status_labels[$key]['label'].": ".implode(", ", $status_names[$key]),
                                    "size" => "sm",
                                    "color" => "#1f2937",
                                    "wrap" => true
                                ];
                            }
                            return $carry;
                        }, [])
                    ]
                ]
            ],
            "footer" => [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [[
                    "type" => "text",
                    "text" => "🔄 อัปเดตล่าสุดโดย STD Care by PhichaiSchool",
                    "size" => "xs",
                    "color" => "#9ca3af",
                    "align" => "center"
                ]]
            ]
        ];
        return $flex;
    }
    
}

// ต้องมีฟังก์ชัน thaiDateShort ให้ใช้งานในคลาสนี้ด้วย (หรือ require จากไฟล์หลัก)
if (!function_exists('thaiDateShort')) {
    function thaiDateShort($date) {
        $months = [
            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
        ];
        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $m)) {
            $year = (int)$m[1];
            $month = (int)$m[2];
            $day = (int)$m[3];
            if ($year < 2500) $year += 543;
            return $day . ' ' . $months[$month] . ' ' . $year;
        }
        return $date;
    }
}
