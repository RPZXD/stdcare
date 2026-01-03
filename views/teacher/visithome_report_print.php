<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานการเยี่ยมบ้านปี <?php echo $pee; ?> เทอม <?php echo $term; ?> - ม.<?php echo $class; ?>/<?php echo $room; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background: white;
            color: black;
        }
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            .page-break-inside-avoid { page-break-inside: avoid; }
        }
        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .header-bg {
            background-color: #f8fafc !important;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>
<body class="p-4 md:p-8">

    <div class="no-print mb-8 flex justify-between items-center bg-slate-100 p-4 rounded-xl border border-slate-200">
        <div>
            <h1 class="font-bold text-lg">ฉบับสมบูรณ์สำหรับพิมพ์ (A4)</h1>
            <p class="text-xs text-slate-500">ข้อมูลทั้งหมดจะถูกจัดเรียงเพื่อการพิมพ์รายงาน</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.close()" class="px-4 py-2 bg-slate-500 text-white rounded-lg font-bold text-sm">ปิดหน้านี้</button>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm shadow-lg shadow-blue-500/30">พิมพ์รายงานตอนนี้</button>
        </div>
    </div>

    <!-- Official Header -->
    <div class="text-center mb-8 border-b-2 border-slate-800 pb-6">
        <h1 class="text-2xl font-bold mb-1">รายงานสรุปผลการเยี่ยมบ้านนักเรียน</h1>
        <p class="text-lg">โรงเรียนพิชัย | ปีการศึกษา <?php echo $pee; ?> | ภาคเรียนที่ <?php echo $term; ?></p>
        <div class="grid grid-cols-2 mt-4 text-left max-w-6xl mx-auto border p-4 rounded">
            <div>
                <span class="font-bold">ระดับชั้น:</span> มัธยมศึกษาปีที่ <?php echo $class; ?>/<?php echo $room; ?>
            </div>
            <div>
                <span class="font-bold">ครูที่ปรึกษา:</span> <?php echo implode(', ', array_map(function($t) { return $t['Teach_name']; }, $roomTeachers)); ?>
            </div>
            <div>
                <span class="font-bold">วันที่ออกรายงาน:</span> <?php echo date('d/m/Y H:i'); ?>
            </div>
            <div>
                <span class="font-bold">ความคืบหน้ารวม:</span> <span id="summary_percent">...</span>
            </div>
        </div>
    </div>

    <!-- Stats Summary Section -->
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
            1. สรุปภาพรวมความสำเร็จ
        </h2>
        <div class="grid grid-cols-2 gap-8 items-center border p-6 rounded-2xl bg-slate-50">
            <div class="w-full max-w-[280px] mx-auto">
                <canvas id="main_summary_chart"></canvas>
            </div>
            <div class="space-y-4" id="main_summary_legend">
                <!-- Legend will be here -->
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="mb-10">
        <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
            2. รายละเอียดสถิติรายข้อ
        </h2>
        <table class="w-full text-sm border-collapse">
            <thead class="header-bg">
                <tr>
                    <th class="w-2/5">รายการ / หัวข้อ</th>
                    <th class="w-2/5">คำตอบ</th>
                    <th class="w-[10%] text-center">จำนวน (คน)</th>
                    <th class="w-[10%] text-center">ร้อยละ</th>
                </tr>
            </thead>
            <tbody id="full_table_body">
                <!-- Data injected by JS -->
            </tbody>
        </table>
    </div>

    <!-- Charts Per Question (The heavy part) -->
    <div class="page-break pt-8">
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
            3. กราฟสถิติจำแนกตามหัวข้อ (18 หัวข้อ)
        </h2>
        
        <div id="charts_grid" class="flex flex-col gap-8">
            <!-- 18 charts will be rendered here -->
        </div>
    </div>

    <!-- Official Signatures -->
    <div class="mt-20 signature-section grid grid-cols-2 gap-y-16 gap-x-12 text-center">
        <?php foreach ($roomTeachers as $t): ?>
        <div>
            <p class="mb-2">ลงชื่อ...........................................................</p>
            <p class="font-bold">( <?php echo $t['Teach_name']; ?> )</p>
            <p class="text-sm">ครูที่ปรึกษา</p>
        </div>
        <?php endforeach; ?>
        
       
    </div>

    <script shadow>
        const classId = <?php echo $class; ?>;
        const roomId = <?php echo $room; ?>;
        const peeId = <?php echo $pee; ?>;
        const termId = <?php echo $term; ?>;

        document.addEventListener('DOMContentLoaded', async function() {
            try {
                const response = await fetch(`api/fetch_visithomeclass.php?class=${classId}&room=${roomId}&pee=${peeId}&term=${termId}`);
                const result = await response.json();
                
                if (result.success) {
                    renderFullReport(result);
                }
            } catch (error) {
                console.error("Failed to load report data:", error);
            }
        });

        function renderFullReport(result) {
            const data = result.data;
            const summary = result.summary;

            // Update Header Stats
            document.getElementById('summary_percent').textContent = summary.visited_photo + ' จาก ' + summary.total_students + ' คน (' + summary.visited_photo_percent + '%)';

            // 1. Render Main Summary Doughnut
            renderMainChart(data, summary);

            // 2. Render Full Table
            renderFullTable(data);

            // 3. Render All Individual Charts
            renderTopicCharts(data);

            // Auto print after a small delay to allow charts to render
            setTimeout(() => {
                // window.print();
            }, 1000);
        }

        function renderMainChart(data, summary) {
            const firstGroup = data.filter(i => i.item_type === data[0].item_type);
            const ctx = document.getElementById('main_summary_chart').getContext('2d');
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: firstGroup.map(i => i.item_list),
                    datasets: [{
                        data: firstGroup.map(i => i.Stu_total),
                        backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6'],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: { legend: { display: false } }
                }
            });

            const legend = document.getElementById('main_summary_legend');
            legend.innerHTML = firstGroup.map((i, idx) => `
                <div class="flex items-center gap-3">
                    <div class="w-4 h-4 rounded" style="background: ${['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6'][idx] || '#ccc'}"></div>
                    <div class="flex-1 font-bold text-sm">${i.item_list}</div>
                    <div class="font-bold text-blue-600">${i.Stu_total} คน (${i.Persent}%)</div>
                </div>
            `).join('');
        }

        function renderFullTable(data) {
            const tbody = document.getElementById('full_table_body');
            let html = '';
            
            data.forEach(item => {
                html += `
                    <tr class="${item.display_header ? 'header-bg' : ''}">
                        <td class="${item.display_header ? 'font-bold' : 'pl-6 text-slate-600'}">
                            ${item.display_header ? item.item_type : ''}
                        </td>
                        <td>${item.item_list}</td>
                        <td class="text-center font-bold">${item.Stu_total}</td>
                        <td class="text-center">${item.Persent}%</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        function renderTopicCharts(data) {
            const container = document.getElementById('charts_grid');
            const groups = [...new Set(data.map(i => i.item_type))];
            
            // Professional color palette
            const palette = ['#4F46E5', '#7C3AED', '#DB2777', '#EA580C', '#059669', '#2563EB'];

            groups.forEach((groupName, idx) => {
                const groupItems = data.filter(i => i.item_type === groupName);
                
                // Estimate height based on number of items (approx 45px per bar + header)
                const chartHeight = Math.max(180, groupItems.length * 45);

                // Create container element
                const chartBox = document.createElement('div');
                chartBox.className = 'flex flex-col border border-slate-200 p-6 rounded-2xl bg-white page-break-inside-avoid';
                chartBox.innerHTML = `
                    <h3 class="font-bold text-sm mb-4 text-slate-800 border-l-4 border-slate-800 pl-3">${groupName}</h3>
                    <div style="height: ${chartHeight}px; position: relative;">
                        <canvas id="chart_topic_${idx}"></canvas>
                    </div>
                `;
                container.appendChild(chartBox);

                // Render Chart
                const ctx = document.getElementById(`chart_topic_${idx}`).getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: groupItems.map(i => i.item_list),
                        datasets: [{
                            label: 'จำนวนนักเรียน (คน)',
                            data: groupItems.map(i => i.Stu_total),
                            backgroundColor: palette[idx % palette.length],
                            borderRadius: 4,
                            barThickness: 25
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { 
                            legend: { display: false },
                            tooltip: { enabled: true }
                        },
                        scales: {
                            x: { 
                                beginAtZero: true, 
                                ticks: { 
                                    stepSize: 1,
                                    font: { size: 10 } 
                                },
                                grid: { color: '#f1f5f9' }
                            },
                            y: { 
                                ticks: { 
                                    font: { size: 11, weight: 'bold' },
                                    autoSkip: false
                                },
                                grid: { display: false }
                            }
                        },
                        layout: {
                            padding: {
                                right: 30 // Add some space for labels
                            }
                        }
                    }
                });
            });
        }
    </script>

</body>
</html>
