<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แอปพลิเคชันแผนการเยี่ยมบ้านนักเรียน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chosen Palette: Soft Harmony -->
    <!-- Application Structure Plan: The SPA is designed as an interactive dashboard with a tab-based navigation for each zone. This structure was chosen to provide a clear, uncluttered view for users, likely on a mobile device. Users can quickly switch between zones without scrolling. Each zone's view consolidates all necessary information: a visual map representation, an ordered list of students with individual route links, and a direct link to Google Maps for the full tour. This task-oriented design focuses on usability and efficiency for the teacher conducting the visits. -->
    <!-- Visualization & Content Choices: 
        - Report Info: Geographic zones, student lists, and travel order.
        - Goal: Organize and visualize visit plans for easy execution, allowing for both full-tour and individual-trip planning.
        - Viz/Presentation: Interactive scatter plot with line overlay (Chart.js) to simulate a map, and an interactive HTML list for the visit sequence. Each list item now includes a button for individual navigation.
        - Interaction: Users click zone tabs to update the view. They can click a main button for the full zone route or individual buttons for student-specific routes. Hovering over a student's name highlights their location on the map.
        - Justification: This approach adds flexibility. The teacher can follow the full optimized route or make individual trips as needed, directly from the same interface.
        - Library/Method: Chart.js for visualization, Vanilla JS for interactivity.
    -->
    <!-- CONFIRMATION: NO SVG graphics used. NO Mermaid JS used. -->
    <style>
        body {
            font-family: 'Sarabun', sans-serif;
            background-color: #F8F7F4;
        }
        .chart-container {
            position: relative;
            width: 100%;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            height: 300px;
            max-height: 40vh;
        }
        @media (min-width: 768px) {
            .chart-container {
                height: 400px;
                max-height: 50vh;
            }
        }
    </style>
</head>
<body class="text-gray-800">

    <div class="container mx-auto p-4 md:p-8 max-w-5xl">
        <header class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-700">แผนการเยี่ยมบ้านนักเรียน</h1>
            <p class="text-gray-500 mt-2">เลือกโซนเพื่อดูแผนภาพรวม หรือเลือกนักเรียนเพื่อดูเส้นทางรายบุคคล</p>
        </header>

        <main>
            <div id="zone-selector" class="flex flex-wrap justify-center gap-2 md:gap-4 mb-8">
            </div>

            <div id="zone-content" class="bg-white p-4 sm:p-6 rounded-2xl shadow-lg transition-opacity duration-500 ease-in-out">
                <div id="initial-message" class="text-center text-gray-500 py-16">
                    <p>กรุณาเลือกโซนจากด้านบนเพื่อเริ่ม</p>
                </div>
                <div id="content-container" class="hidden">
                     <div class="text-center mb-6">
                        <h2 id="zone-title" class="text-2xl font-bold text-teal-700"></h2>
                        <p id="zone-description" class="text-gray-600 mt-1"></p>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                        <div class="chart-container">
                            <canvas id="mapChart"></canvas>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold mb-3 text-gray-700">ลำดับการเยี่ยมบ้าน</h3>
                            <div id="student-list" class="space-y-2 max-h-80 overflow-y-auto pr-2 border-b pb-4"></div>
                             <a id="gmaps-link" href="#" target="_blank" class="mt-4 inline-block w-full text-center bg-teal-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-teal-700 transition-colors shadow-md">
                                📍 เปิดเส้นทางรวมทั้งโซน
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="text-center mt-12 text-gray-400 text-sm">
            <h4 class="font-semibold text-gray-500 mb-2">ข้อแนะนำเพิ่มเติม</h4>
            <ul class="list-disc list-inside inline-block text-left">
                <li>ลำดับที่จัดให้เป็นเพียงแนวทางเบื้องต้น สามารถปรับเปลี่ยนได้ตามความสะดวก</li>
                <li>ควรตรวจสอบสภาพการจราจรก่อนออกเดินทาง</li>
                <li>แนะนำให้แบ่งการเยี่ยมเป็น 2-3 วันเพื่อประสิทธิภาพสูงสุด</li>
            </ul>
        </footer>
    </div>

    <script>
        const visitData = {
            startPoint: { name: "จุดเริ่มต้น/สิ้นสุด", lat: 17.2845666, lon: 100.0851797 },
            zones: [
                {
                    name: "โซนที่ 1: ตะวันตก",
                    description: "พื้นที่ตำบลคุ้งตะเภา",
                    gmapsUrl: "https://www.google.com/maps/dir/17.2845666,100.0851797/17.3006995,100.0517357/17.312969,100.039055/17.2790612,99.9970784/17.2739113,99.9862619/17.2748486,99.9891592/17.2845666,100.0851797",
                    students: [
                        { name: "ด.ญ.พิชาภา คำพืช", lat: 17.3006995, lon: 100.0517357 },
                        { name: "ด.ญ.ณิชกานต์ ลอยคง", lat: 17.312969, lon: 100.039055 },
                        { name: "ด.ญ.วริศรา ผลดี", lat: 17.2790612, lon: 99.9970784 },
                        { name: "ด.ญ.วิราวรรณ ด้วงเฟื่อง", lat: 17.2739113, lon: 99.9862619 },
                        { name: "ด.ญ.พรสวรรค์ ตาลป๊อก", lat: 17.2748486, lon: 99.9891592 },
                    ]
                },
                {
                    name: "โซนที่ 2: กลางและใต้",
                    description: "พื้นที่ตำบลท่าอิฐและใกล้เคียง",
                    gmapsUrl: "https://www.google.com/maps/dir/17.2845666,100.0851797/17.2801736,100.0733488/17.284142,100.077378/17.2915641,100.0843687/17.2825296,100.0911521/17.284528,100.0934611/17.2602122,100.0918946/17.2530799,100.0857103/17.3034166,100.069515/17.2845666,100.0851797",
                    students: [
                        { name: "ด.ช.คมสันต์ ดีสาย", lat: 17.2801736, lon: 100.0733488 },
                        { name: "ด.ช.ชาญวิทย์ มีแหยม", lat: 17.284142, lon: 100.077378 },
                        { name: "ด.ช.อาลีฟไฮคาล อยู่เเสง", lat: 17.2915641, lon: 100.0843687 },
                        { name: "ด.ญ.พรหมญาดา ปานันต๊ะ", lat: 17.2825296, lon: 100.0911521 },
                        { name: "ด.ญ.มีณชญา สร้อยสุวรรณ์", lat: 17.284528, lon: 100.0934611 },
                        { name: "ด.ญ.ชนัญธิตา แตงแก้ว", lat: 17.2602122, lon: 100.0918946 },
                        { name: "ด.ญ.ณีรนุช มิ่งสืบดี", lat: 17.2530799, lon: 100.0857103 },
                        { name: "ด.ช.ธนากร ศรีมหาเลิศ", lat: 17.3034166, lon: 100.069515 },
                    ]
                },
                {
                    name: "โซนที่ 3: ทิศเหนือ",
                    description: "พื้นที่ตำบลบ้านเกาะและป่าเซ่า",
                    gmapsUrl: "https://www.google.com/maps/dir/17.2845666,100.0851797/17.3398888,100.0705154/17.3681595,100.0841248/17.377544,100.0773047/17.3844152,100.0937149/17.2845666,100.0851797",
                    students: [
                        { name: "ด.ญ.ธนกาญจน์ ชูแสงจันทร์", lat: 17.3398888, lon: 100.0705154 },
                        { name: "ด.ญ.สุพิชฌาย์ อุ่นวงค์", lat: 17.3681595, lon: 100.0841248 },
                        { name: "ด.ช.วีรพงษ์ อยู่ขำ", lat: 17.377544, lon: 100.0773047 },
                        { name: "ด.ช.ภานุวัฒน์ ดำสอน", lat: 17.3844152, lon: 100.0937149 },
                    ]
                }
            ]
        };

        const zoneSelector = document.getElementById('zone-selector');
        const zoneTitle = document.getElementById('zone-title');
        const zoneDescription = document.getElementById('zone-description');
        const studentList = document.getElementById('student-list');
        const gmapsLink = document.getElementById('gmaps-link');
        const initialMessage = document.getElementById('initial-message');
        const contentContainer = document.getElementById('content-container');
        
        const canvas = document.getElementById('mapChart');
        const ctx = canvas.getContext('2d');
        let mapChart;
        let activeZoneButton = null;

        function displayZone(zoneIndex) {
            initialMessage.classList.add('hidden');
            contentContainer.classList.remove('hidden');

            const zone = visitData.zones[zoneIndex];
            zoneTitle.textContent = zone.name;
            zoneDescription.textContent = zone.description;
            gmapsLink.href = zone.gmapsUrl;

            studentList.innerHTML = '';
            
            zone.students.forEach((student, index) => {
                const itemContainer = document.createElement('div');
                itemContainer.className = 'flex justify-between items-center p-2 rounded-md hover:bg-teal-50 cursor-pointer transition-colors';

                const studentName = document.createElement('span');
                studentName.textContent = `${index + 1}. ${student.name}`;
                studentName.className = 'text-gray-800';

                const routeButton = document.createElement('a');
                const startLat = visitData.startPoint.lat;
                const startLon = visitData.startPoint.lon;
                const studentLat = student.lat;
                const studentLon = student.lon;
                const individualGmapsUrl = `https://www.google.com/maps/dir/${startLat},${startLon}/${studentLat},${studentLon}/${startLat},${startLon}`;
                
                routeButton.href = individualGmapsUrl;
                routeButton.target = '_blank';
                routeButton.textContent = 'ดูเส้นทาง';
                routeButton.className = 'text-xs bg-gray-200 text-gray-700 font-semibold py-1 px-3 rounded-full hover:bg-gray-300 transition-colors no-underline';
                
                itemContainer.appendChild(studentName);
                itemContainer.appendChild(routeButton);
                
                itemContainer.onmouseenter = () => highlightPoint(index);
                itemContainer.onmouseleave = () => resetHighlight();

                studentList.appendChild(itemContainer);
            });


            const allPoints = [visitData.startPoint, ...zone.students, visitData.startPoint];
            const routeData = allPoints.map(p => ({ x: p.lon, y: p.lat }));
            const labels = [visitData.startPoint.name, ...zone.students.map(s => s.name)];
            const studentPoints = zone.students.map(p => ({ x: p.lon, y: p.lat }));
            
            if (mapChart) {
                mapChart.destroy();
            }

            mapChart = new Chart(ctx, {
                type: 'scatter',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'เส้นทาง',
                            data: routeData,
                            borderColor: 'rgba(20, 184, 166, 0.5)',
                            borderWidth: 2,
                            fill: false,
                            pointRadius: 0,
                            tension: 0.1
                        },
                        {
                            label: 'นักเรียน',
                            data: studentPoints,
                            backgroundColor: 'rgb(13, 148, 136)',
                            pointRadius: 6,
                            pointHoverRadius: 9,
                        },
                        {
                            label: 'จุดเริ่มต้น/สิ้นสุด',
                            data: [{x: visitData.startPoint.lon, y: visitData.startPoint.lat}],
                            backgroundColor: 'rgb(249, 115, 22)',
                            pointRadius: 8,
                            pointHoverRadius: 11,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.chart.data.labels[context.dataIndex + 1] || context.dataset.label || '';
                                }
                            }
                        }
                    },
                    scales: {
                        x: { display: false, grid: { display: false } },
                        y: { display: false, grid: { display: false } }
                    }
                }
            });
        }
        
        function highlightPoint(studentIndex) {
            if (!mapChart) return;
             mapChart.tooltip.setActiveElements([
                { datasetIndex: 1, index: studentIndex }
            ]);
            mapChart.update();
        }

        function resetHighlight() {
             if (!mapChart) return;
             mapChart.tooltip.setActiveElements([]);
             mapChart.update();
        }

        window.addEventListener('DOMContentLoaded', () => {
            visitData.zones.forEach((zone, index) => {
                const button = document.createElement('button');
                button.textContent = zone.name;
                button.className = 'px-4 py-2 text-base font-semibold text-gray-600 bg-white rounded-full shadow-sm hover:bg-teal-100 hover:text-teal-800 transition-all';
                button.onclick = () => {
                    displayZone(index);
                     if (activeZoneButton) {
                        activeZoneButton.classList.remove('bg-teal-600', 'text-white', 'shadow-lg');
                        activeZoneButton.classList.add('bg-white', 'text-gray-600');
                    }
                    button.classList.add('bg-teal-600', 'text-white', 'shadow-lg');
                    button.classList.remove('bg-white', 'text-gray-600');
                    activeZoneButton = button;
                };
                zoneSelector.appendChild(button);
            });
        });
    </script>
</body>
</html>
