<?php
include_once("../config/Database.php");
include_once("../class/UserLogin.php");
include_once("../class/Student.php");
include_once("../class/Utils.php");
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
$user = new UserLogin($db);
$student = new Student($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

if (isset($_SESSION['Officer_login'])) {
    $userid = $_SESSION['Officer_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php' // Redirect URL
    );
    $sw2->renderAlert();
    exit;
}

require_once('header.php');

?>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
    <?php require_once('wrapper.php'); ?>

    <div class="content-wrapper bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent flex items-center">
                            <span class="text-4xl mr-3 animate-bounce">🎯</span>
                            Officer Dashboard
                        </h1>
                        <p class="text-gray-600 mt-2 flex items-center">
                            <span class="text-lg mr-2">📅</span>
                            เทอม <?=$term?> ปีการศึกษา <?=$pee?>
                        </p>
                    </div>
                    <div class="col-sm-6 text-right">
                        <div class="bg-white bg-opacity-70 backdrop-blur-lg rounded-2xl p-4 shadow-xl border border-white border-opacity-20">
                            <div class="flex items-center justify-end space-x-2">
                                <span class="text-2xl">👋</span>
                                <div>
                                    <p class="text-sm text-gray-600">ยินดีต้อนรับ</p>
                                    <p class="font-semibold text-gray-800"><?=$userData['name'] ?? 'Officer'?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="content">
<?php
// ดึงข้อมูลจำนวนนักเรียน
$studentCount = $db->query("SELECT COUNT(*) as total FROM student WHERE Stu_status=1")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูลจำนวนบุคลากร
$teacherCount = $db->query("SELECT COUNT(*) as total FROM teacher WHERE Teach_status=1")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูลจำนวนพฤติกรรมทั้งหมด
$behaviorCount = $db->query("SELECT COUNT(*) as total FROM behavior WHERE behavior_term = $term AND behavior_pee = $pee")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
// ดึงข้อมูล score พฤติกรรมรวม
$behaviorScore = $db->query("SELECT COALESCE(SUM(behavior_score),0) as total FROM behavior WHERE behavior_term = $term AND behavior_pee = $pee")->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

// ดึงคะแนนรวมของแต่ละ Stu_id ก่อน แล้วค่อยจัดกลุ่ม
$scoreGroups = [
    'เข้าค่ายปรับพฤติกรรม (<50)' => 0,
    'บำเพ็ญประโยชน์ 20 ชม. (50-70)' => 0,
    'บำเพ็ญประโยชน์ 10 ชม. (71-99)' => 0,
    'ไม่มีคะแนน' => 0
];
$totalStudentsForGraph = 0;
$scoreStmt = $db->query("
    SELECT s.Stu_id, COALESCE(SUM(b.behavior_score),0) AS total_score
    FROM student s
    LEFT JOIN behavior b ON s.Stu_id = b.stu_id
    WHERE s.Stu_status=1 AND b.behavior_term = $term AND b.behavior_pee = $pee
    GROUP BY s.Stu_id
");
while ($row = $scoreStmt->fetch(PDO::FETCH_ASSOC)) {
    $score = (int)($row['total_score'] ?? 0);
    if ($score === 0) {
        $scoreGroups['ไม่มีคะแนน']++;
    } elseif ($score < 50) {
        $scoreGroups['เข้าค่ายปรับพฤติกรรม (<50)']++;
    } elseif ($score <= 70) {
        $scoreGroups['บำเพ็ญประโยชน์ 20 ชม. (50-70)']++;
    } elseif ($score <= 99) {
        $scoreGroups['บำเพ็ญประโยชน์ 10 ชม. (71-99)']++;
    }
    $totalStudentsForGraph++;
}
// ปรับยอดรวมในกราฟให้เท่ากับจำนวนนักเรียน (กันกรณีข้อมูลผิดพลาด)
if ($totalStudentsForGraph != $studentCount) {
    // กรณีมีนักเรียนที่ไม่มีในผล query (เช่น ไม่มี behavior เลย)
    $diff = $studentCount - $totalStudentsForGraph;
    if ($diff > 0) {
        $scoreGroups['ไม่มีคะแนน'] += $diff;
    }
}
?>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Loading animation and entrance effects
document.addEventListener('DOMContentLoaded', function() {
    // Hide all cards initially
    const cards = document.querySelectorAll('.grid > div');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(50px)';
    });

    // Animate cards entrance
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 200);
    });

    // Animate chart entrance
    const chartContainer = document.querySelector('.bg-white.bg-opacity-80');
    if (chartContainer) {
        chartContainer.style.opacity = '0';
        chartContainer.style.transform = 'translateY(30px)';
        setTimeout(() => {
            chartContainer.style.transition = 'all 1s cubic-bezier(0.4, 0, 0.2, 1)';
            chartContainer.style.opacity = '1';
            chartContainer.style.transform = 'translateY(0)';
        }, 800);
    }

    // Add sparkle effect to emojis
    const emojis = document.querySelectorAll('span[class*="text-4xl"], span[class*="text-5xl"]');
    emojis.forEach(emoji => {
        emoji.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2) rotate(5deg)';
            this.style.transition = 'transform 0.3s ease';
        });

        emoji.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    // Update last update time
    const updateTime = () => {
        const now = new Date();
        const timeString = now.toLocaleTimeString('th-TH', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        document.getElementById('lastUpdateTime').textContent = timeString;
    };

    updateTime();
    setInterval(updateTime, 1000);

    // Add click effects to stat cards
    const statCards = document.querySelectorAll('.grid.grid-cols-2.md\\:grid-cols-4 .text-center');
    statCards.forEach(card => {
        card.addEventListener('click', function() {
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
</script>

<div class="container mx-auto py-8 px-4">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Students Card -->
        <div class="group relative bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 rounded-3xl shadow-2xl hover:shadow-3xl hover:shadow-blue-500/50 hover:scale-105 transition-all duration-500 transform overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-8 flex flex-col items-center text-white">
                <div class="bg-white/20 backdrop-blur-lg rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-5xl animate-pulse">👨‍🎓</span>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2 group-hover:scale-110 transition-transform duration-300"><?= $studentCount ?></div>
                    <div class="text-blue-100 text-lg font-medium">นักเรียนทั้งหมด</div>
                    <div class="mt-3 flex items-center justify-center space-x-1">
                        <span class="text-xs bg-white/20 px-2 py-1 rounded-full">📚 กำลังศึกษา</span>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-2 -right-2 w-20 h-20 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-colors duration-500"></div>
        </div>

        <!-- Teachers Card -->
        <div class="group relative bg-gradient-to-br from-emerald-500 via-green-600 to-teal-700 rounded-3xl shadow-2xl hover:shadow-3xl hover:shadow-emerald-500/50 hover:scale-105 transition-all duration-500 transform overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-8 flex flex-col items-center text-white">
                <div class="bg-white/20 backdrop-blur-lg rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-5xl animate-pulse">👩‍🏫</span>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2 group-hover:scale-110 transition-transform duration-300"><?= $teacherCount ?></div>
                    <div class="text-emerald-100 text-lg font-medium">บุคลากรทั้งหมด</div>
                    <div class="mt-3 flex items-center justify-center space-x-1">
                        <span class="text-xs bg-white/20 px-2 py-1 rounded-full">🎓 ครูผู้สอน</span>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-2 -right-2 w-20 h-20 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-colors duration-500"></div>
        </div>

        <!-- Behaviors Card -->
        <div class="group relative bg-gradient-to-br from-orange-500 via-red-500 to-pink-600 rounded-3xl shadow-2xl hover:shadow-3xl hover:shadow-orange-500/50 hover:scale-105 transition-all duration-500 transform overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative p-8 flex flex-col items-center text-white">
                <div class="bg-white/20 backdrop-blur-lg rounded-full p-4 mb-4 group-hover:scale-110 transition-transform duration-300">
                    <span class="text-5xl animate-pulse">📋</span>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2 group-hover:scale-110 transition-transform duration-300"><?= $behaviorCount ?></div>
                    <div class="text-orange-100 text-lg font-medium">พฤติกรรมทั้งหมด</div>
                    <div class="mt-3 flex items-center justify-center space-x-1">
                        <span class="text-xs bg-white/20 px-2 py-1 rounded-full">⚠️ รอติดตาม</span>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-2 -right-2 w-20 h-20 bg-white/10 rounded-full blur-xl group-hover:bg-white/20 transition-colors duration-500"></div>
        </div>
    </div>

        <!-- Enhanced Chart Section -->
        <div class="bg-white bg-opacity-80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white border-opacity-20 p-8 md:col-span-3 hover:shadow-3xl hover:shadow-purple-500/20 transition-all duration-500">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent flex items-center">
                    <span class="text-4xl mr-3 animate-bounce">📊</span>
                    สรุปกลุ่มคะแนนพฤติกรรม
                </h2>
                <div class="flex items-center space-x-2 bg-gradient-to-r from-purple-100 to-pink-100 px-4 py-2 rounded-full">
                    <span class="text-sm font-medium text-purple-700">เทอม <?=$term?></span>
                    <span class="text-purple-400">•</span>
                    <span class="text-sm font-medium text-purple-700">ปี <?=$pee?></span>
                </div>
            </div>
            <div class="relative">
                <canvas id="scoreChart" height="450" class="rounded-2xl"></canvas>
                
            </div>
           
            </div>
        </div>
    </div>

    <!-- Quick Stats Summary -->

<script>
const ctx = document.getElementById('scoreChart').getContext('2d');// Create gradient backgrounds for bars
const gradients = [
    ctx.createLinearGradient(0, 0, 0, 400), // Red gradient
    ctx.createLinearGradient(0, 0, 0, 400), // Yellow gradient
    ctx.createLinearGradient(0, 0, 0, 400), // Blue gradient
    ctx.createLinearGradient(0, 0, 0, 400)  // Green gradient
];

// Red gradient
gradients[0].addColorStop(0, '#f87171');
gradients[0].addColorStop(1, '#dc2626');

// Yellow gradient
gradients[1].addColorStop(0, '#fbbf24');
gradients[1].addColorStop(1, '#d97706');

// Blue gradient
gradients[2].addColorStop(0, '#60a5fa');
gradients[2].addColorStop(1, '#2563eb');

// Green gradient
gradients[3].addColorStop(0, '#34d399');
gradients[3].addColorStop(1, '#059669');

const scoreChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_keys($scoreGroups)) ?>,
        datasets: [{
            label: 'จำนวนนักเรียน',
            data: <?= json_encode(array_values($scoreGroups)) ?>,
            backgroundColor: gradients,
            borderColor: [
                '#dc2626',
                '#d97706',
                '#2563eb',
                '#059669'
            ],
            borderWidth: 2,
            borderRadius: 12,
            borderSkipped: false,
            hoverBackgroundColor: [
                '#fca5a5',
                '#fcd34d',
                '#93c5fd',
                '#6ee7b7'
            ],
            hoverBorderColor: [
                '#b91c1c',
                '#b45309',
                '#1d4ed8',
                '#047857'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            duration: 2000,
            easing: 'easeInOutQuart',
            onComplete: function() {
                // Add sparkle effect after animation
                const canvas = this.canvas;
                const ctx = canvas.getContext('2d');
                ctx.save();
                ctx.globalCompositeOperation = 'destination-over';
                ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                ctx.restore();
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#fff',
                borderWidth: 1,
                cornerRadius: 8,
                displayColors: false,
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        return `จำนวน: ${context.parsed.y} คน`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)',
                    borderDash: [5, 5]
                },
                ticks: {
                    color: '#6b7280',
                    font: {
                        size: 12,
                        weight: '500'
                    },
                    padding: 10
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#6b7280',
                    font: {
                        size: 11,
                        weight: '500'
                    },
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        },
        elements: {
            bar: {
                borderRadius: 8
            }
        },
        onHover: (event, activeElements) => {
            event.native.target.style.cursor = activeElements.length > 0 ? 'pointer' : 'default';
        }
    }
});
</script>
        </section>
    </div>

    <!-- Floating Background Elements -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden">
        <div class="absolute top-20 left-10 w-20 h-20 bg-blue-400 rounded-full opacity-10 animate-bounce" style="animation-delay: 0s; animation-duration: 3s;"></div>
        <div class="absolute top-40 right-20 w-16 h-16 bg-purple-400 rounded-full opacity-10 animate-bounce" style="animation-delay: 1s; animation-duration: 4s;"></div>
        <div class="absolute bottom-32 left-1/4 w-12 h-12 bg-pink-400 rounded-full opacity-10 animate-bounce" style="animation-delay: 2s; animation-duration: 3.5s;"></div>
        <div class="absolute bottom-20 right-1/3 w-24 h-24 bg-green-400 rounded-full opacity-10 animate-bounce" style="animation-delay: 0.5s; animation-duration: 4.5s;"></div>
        <div class="absolute top-1/2 left-20 w-8 h-8 bg-yellow-400 rounded-full opacity-10 animate-bounce" style="animation-delay: 1.5s; animation-duration: 3s;"></div>
    </div>

    <!-- Custom CSS for additional effects -->
    <style>
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.5); }
            50% { box-shadow: 0 0 40px rgba(59, 130, 246, 0.8); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-glow {
            animation: glow 2s ease-in-out infinite;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #3b82f6, #8b5cf6);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #2563eb, #7c3aed);
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .grid-cols-1.md\\:grid-cols-3 {
                grid-template-columns: repeat(1, minmax(0, 1fr));
                gap: 1rem;
            }

            .md\\:col-span-3 {
                grid-column: span 1;
            }

            .text-3xl {
                font-size: 1.5rem;
            }

            .text-4xl {
                font-size: 2rem;
            }

            .text-5xl {
                font-size: 2.5rem;
            }

            .p-8 {
                padding: 1.5rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .from-blue-50 {
                --tw-gradient-from: rgb(30 58 138 / var(--tw-bg-opacity));
            }
            .via-indigo-50 {
                --tw-gradient-via: rgb(67 56 202 / var(--tw-bg-opacity));
            }
            .to-purple-50 {
                --tw-gradient-to: rgb(88 28 135 / var(--tw-bg-opacity));
            }
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .bg-gradient-to-br {
                background: white !important;
                color: black !important;
            }

            .shadow-2xl {
                box-shadow: none !important;
            }
        }
    </style>

    <?php require_once('../footer.php'); ?>
</div>
<?php require_once('script.php'); ?>
</body>
</html>
