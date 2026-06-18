<?php
/**
 * View: Route Planner Page
 * Renders the route optimizer dashboard
 */
ob_start();

// Group subdistricts to help user visualize
$subdistricts = [];
foreach ($studentGpsList as $std) {
    if (!empty($std['subdistrict']) && $std['subdistrict'] !== 'ไม่ระบุตำบล') {
        $subdistricts[] = $std['subdistrict'];
    }
}
$subdistricts = array_unique($subdistricts);
sort($subdistricts);
?>

<!-- Leaflet Assets (External) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4 animate-fadeIn">
        <div class="flex items-center gap-4">
            <a href="gps_visithome.php" class="w-12 h-12 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-md transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-route text-blue-600 animate-pulse"></i> จัดเส้นทางเยี่ยมบ้านอัจฉริยะ (Route Planner)
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">ชั้นมัธยมศึกษาปีที่ <?= htmlspecialchars($class . "/" . $room); ?> • จัดเส้นทางและเวลารวดเร็วเชิงพื้นที่</p>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Route Settings Panel -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl p-6 flex flex-col gap-5">
            <h3 class="font-extrabold text-slate-800 dark:text-white flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-3">
                <i class="fas fa-sliders text-blue-500"></i> ตั้งค่าพารามิเตอร์
            </h3>

            <!-- 1. Teacher Filter -->
            <div>
                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">ครูผู้เยี่ยมบ้าน</label>
                <select id="paramTeacher" class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium">
                    <option value="all">ทั้งหมด (ทุกคนในห้อง)</option>
                    <?php if ($teachers): ?>
                        <?php foreach ($teachers as $t): ?>
                            <?php 
                                $selected = ($t['Teach_name'] === $teacher_name) ? 'selected' : '';
                            ?>
                            <option value="<?= htmlspecialchars($t['Teach_name']) ?>" <?= $selected ?>><?= htmlspecialchars($t['Teach_name']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="<?= htmlspecialchars($teacher_name) ?>" selected><?= htmlspecialchars($teacher_name) ?></option>
                    <?php endif; ?>
                    <option value="unassigned">ยังไม่ได้ระบุครู</option>
                </select>
            </div>

            <!-- 2. Starting Point coordinates -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">จุดเริ่มต้นเดินทาง</label>
                    <button onclick="resetToSchool()" class="text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline">ใช้โรงเรียนพิชัย</button>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 block mb-0.5">Latitude</span>
                        <input type="number" id="startLat" step="any" value="17.28437" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium font-mono">
                    </div>
                    <div>
                        <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 block mb-0.5">Longitude</span>
                        <input type="number" id="startLng" step="any" value="100.08746" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium font-mono">
                    </div>
                </div>
                <p class="text-[9px] text-slate-400 dark:text-slate-500 italic">*คลิกบนแผนที่หรือลากหมุดดาวสีแดงเพื่อเปลี่ยนจุดเริ่มต้น</p>
            </div>

            <!-- 3. Dynamic constraints -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ระยะเวลาเยี่ยม/หลัง</label>
                    <div class="relative flex items-center">
                        <input type="number" id="paramVisitTime" value="15" min="5" max="60" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium font-mono">
                        <span class="text-[10px] font-bold text-slate-400 absolute right-3">นาที</span>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">ความเร็วรถเฉลี่ย</label>
                    <div class="relative flex items-center">
                        <input type="number" id="paramSpeed" value="40" min="10" max="100" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium font-mono">
                        <span class="text-[10px] font-bold text-slate-400 absolute right-3">km/h</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">เวลาเริ่มปฏิบัติงาน</label>
                    <input type="time" id="paramStartTime" value="16:30" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium font-mono">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">จำนวนวันที่จัดกลุ่ม</label>
                    <select id="paramDays" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-xs outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 dark:text-slate-200 font-medium">
                        <option value="1">1 วัน</option>
                        <option value="2">2 วัน</option>
                        <option value="3">3 วัน</option>
                        <option value="4" selected>4 วัน</option>
                        <option value="5">5 วัน</option>
                        <option value="6">6 วัน</option>
                        <option value="7">7 วัน</option>
                    </select>
                </div>
            </div>

            <!-- Action Button -->
            <button onclick="calculateAndRenderRoutes()" class="w-full py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-extrabold rounded-2xl shadow-lg shadow-blue-500/20 hover:shadow-indigo-500/30 transition-all flex items-center justify-center gap-2 active:scale-[0.98]">
                <i class="fas fa-wand-magic-sparkles"></i> คำนวณและจัดเส้นทาง
            </button>
        </div>

        <!-- Map & Timelines Panel -->
        <div class="lg:col-span-3 flex flex-col gap-6">
            <!-- Map Container -->
            <div class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden relative h-[420px]">
                <div id="routeMap" class="w-full h-full relative z-10" style="min-height: 420px;"></div>
                
                <!-- Legend -->
                <div class="absolute bottom-6 left-6 z-[1000] bg-white/95 dark:bg-slate-800/95 backdrop-blur-md p-3.5 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700">
                    <p class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5">คำอธิบายแผนที่</p>
                    <div class="flex flex-col gap-1.5 text-xs text-slate-700 dark:text-slate-300">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded-full flex items-center justify-center bg-rose-500 text-white text-[9px] font-black">★</span>
                            <span class="font-bold">จุดเริ่มต้น (เริ่มต้น-ขากลับ)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white inline-block"></span>
                            <span>นักเรียนที่ร่วมคำนวณ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dynamic Timelines Results -->
            <div id="timelineCard" class="bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl p-6 hidden">
                <div class="flex items-center justify-between mb-4 border-b border-slate-100 dark:border-slate-700 pb-3 flex-wrap gap-2">
                    <div>
                        <h3 class="font-extrabold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-indigo-500"></i> แผนที่สรุปการเดินทางรายวัน
                        </h3>
                        <p class="text-[11px] text-slate-400 font-medium">ผลการคำนวณแบ่งกลุ่มเส้นทางและจัดลำดับเวลา</p>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="printTimeline()" class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs font-bold rounded-xl transition-all flex items-center gap-1.5">
                            <i class="fas fa-print"></i> พิมพ์แผนการจัดส่ง
                        </button>
                    </div>
                </div>

                <!-- Daily Tabs Toggle -->
                <div id="dayTabButtons" class="flex gap-1.5 border-b border-slate-100 dark:border-slate-700 pb-3 flex-wrap">
                    <!-- Injected by JS -->
                </div>

                <!-- Timelines Container -->
                <div id="dayTabContents" class="mt-4">
                    <!-- Injected by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let markers = {};
    let startMarker;
    const studentsData = <?= json_encode($studentGpsList) ?>;
    const schoolCoords = [17.28437, 100.08746];
    let routePolylines = [];

    // Path colors for distinct days
    const pathColors = [
        '#3b82f6', // Bright Blue
        '#10b981', // Emerald Green
        '#f59e0b', // Amber Orange
        '#ec4899', // Pink
        '#8b5cf6', // Indigo Violet
        '#06b6d4', // Cyan
        '#ef4444'  // Rose Red
    ];

    function initMap() {
        // Init map center to school
        map = L.map('routeMap').setView(schoolCoords, 12);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        // Draggable Star/Home marker representing Starting Coordinate
        const startIcon = L.divIcon({
            html: `<div class="w-8 h-8 rounded-full bg-rose-500 border-2 border-white flex items-center justify-center text-white shadow-lg cursor-pointer transform hover:scale-110 transition-transform">★</div>`,
            className: '',
            iconSize: [32, 32],
            iconAnchor: [16, 16]
        });

        startMarker = L.marker(schoolCoords, { icon: startIcon, draggable: true }).addTo(map);

        // Update inputs on drag end
        startMarker.on('dragend', function(e) {
            const position = startMarker.getLatLng();
            $('#startLat').val(position.lat.toFixed(6));
            $('#startLng').val(position.lng.toFixed(6));
            calculateAndRenderRoutes(); // Recalculate routes dynamically on marker move
        });

        // Click map to set start point
        map.on('click', function(e) {
            startMarker.setLatLng(e.latlng);
            $('#startLat').val(e.latlng.lat.toFixed(6));
            $('#startLng').val(e.latlng.lng.toFixed(6));
            calculateAndRenderRoutes(); // Recalculate routes dynamically on click
        });

        // Plot student markers
        studentsData.forEach(std => {
            const lat = parseFloat(std.latitude);
            const lng = parseFloat(std.longitude);
            if (!isNaN(lat) && !isNaN(lng)) {
                const marker = L.marker([lat, lng]).bindPopup(`
                    <div class="text-xs p-1">
                        <strong>เลขที่ ${std.Stu_no}: ${std.Stu_pre}${std.Stu_name}</strong><br/>
                        ชื่อเล่น: ${std.Stu_nick || '-'}<br/>
                        ตำบล: ${std.subdistrict || '-'}<br/>
                        ครูผู้เยี่ยม: ${std.assigned_teacher || 'ยังไม่ระบุ'}
                    </div>
                `);
                markers[std.Stu_id] = marker;
            }
        });

        // Adjust input listeners
        $('#startLat, #startLng').on('change input', function() {
            const lat = parseFloat($('#startLat').val());
            const lng = parseFloat($('#startLng').val());
            if (!isNaN(lat) && !isNaN(lng)) {
                startMarker.setLatLng([lat, lng]);
                map.setView([lat, lng], map.getZoom());
            }
        });

        // Initial Route calculation
        calculateAndRenderRoutes();
    }

    function resetToSchool() {
        $('#startLat').val(schoolCoords[0]);
        $('#startLng').val(schoolCoords[1]);
        startMarker.setLatLng(schoolCoords);
        map.setView(schoolCoords, 13);
        calculateAndRenderRoutes();
    }

    // Haversine distance in km
    function haversineDistance(coords1, coords2) {
        const lat1 = coords1[0];
        const lon1 = coords1[1];
        const lat2 = coords2[0];
        const lon2 = coords2[1];

        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    // K-Means Clustering for Coordinates
    function kMeansClustering(points, k) {
        if (points.length === 0) return [];
        if (points.length <= k) {
            return points.map(p => [p]);
        }

        // Initialize centroids
        let centroids = [];
        let usedIndices = new Set();
        while (centroids.length < k) {
            let idx = Math.floor(Math.random() * points.length);
            if (!usedIndices.has(idx)) {
                centroids.push({ lat: points[idx].lat, lng: points[idx].lng });
                usedIndices.add(idx);
            }
        }

        let assignments = new Array(points.length).fill(-1);
        let changed = true;
        let iterations = 0;

        while (changed && iterations < 100) {
            changed = false;
            iterations++;

            // Assign
            for (let i = 0; i < points.length; i++) {
                let minDist = Infinity;
                let bestCentroid = -1;
                for (let j = 0; j < k; j++) {
                    let d = Math.pow(points[i].lat - centroids[j].lat, 2) + Math.pow(points[i].lng - centroids[j].lng, 2);
                    if (d < minDist) {
                        minDist = d;
                        bestCentroid = j;
                    }
                }
                if (assignments[i] !== bestCentroid) {
                    assignments[i] = bestCentroid;
                    changed = true;
                }
            }

            // Update
            let newCentroids = Array.from({ length: k }, () => ({ lat: 0, lng: 0, count: 0 }));
            for (let i = 0; i < points.length; i++) {
                let c = assignments[i];
                newCentroids[c].lat += points[i].lat;
                newCentroids[c].lng += points[i].lng;
                newCentroids[c].count++;
            }

            for (let j = 0; j < k; j++) {
                if (newCentroids[j].count > 0) {
                    centroids[j].lat = newCentroids[j].lat / newCentroids[j].count;
                    centroids[j].lng = newCentroids[j].lng / newCentroids[j].count;
                }
            }
        }

        let clusters = Array.from({ length: k }, () => []);
        for (let i = 0; i < points.length; i++) {
            clusters[assignments[i]].push(points[i]);
        }

        return clusters.filter(c => c.length > 0);
    }

    // TSP route using Greedy Nearest Neighbor
    function solveGreedyTSP(startLat, startLng, clusterPoints) {
        let route = [];
        let unvisited = [...clusterPoints];
        let currentLat = startLat;
        let currentLng = startLng;

        while (unvisited.length > 0) {
            let nearestIdx = -1;
            let minDist = Infinity;
            for (let i = 0; i < unvisited.length; i++) {
                let d = haversineDistance([currentLat, currentLng], [unvisited[i].lat, unvisited[i].lng]);
                if (d < minDist) {
                    minDist = d;
                    nearestIdx = i;
                }
            }
            route.push({
                point: unvisited[nearestIdx],
                dist: minDist
            });
            currentLat = unvisited[nearestIdx].lat;
            currentLng = unvisited[nearestIdx].lng;
            unvisited.splice(nearestIdx, 1);
        }

        const returnDist = haversineDistance([currentLat, currentLng], [startLat, startLng]);

        return { route, returnDist };
    }

    function calculateAndRenderRoutes() {
        const startLat = parseFloat($('#startLat').val());
        const startLng = parseFloat($('#startLng').val());
        const teacherFilter = $('#paramTeacher').val();
        const numDays = parseInt($('#paramDays').val());
        const visitTime = parseInt($('#paramVisitTime').val());
        const speed = parseFloat($('#paramSpeed').val());
        const startTimeStr = $('#paramStartTime').val();

        if (isNaN(startLat) || isNaN(startLng)) {
            alert('กรุณากรอกพิกัดจุดเริ่มต้นให้ถูกต้อง');
            return;
        }

        // 1. Filter students
        let activeStudents = [];
        studentsData.forEach(std => {
            const lat = parseFloat(std.latitude);
            const lng = parseFloat(std.longitude);
            if (isNaN(lat) || isNaN(lng)) return;

            let match = false;
            if (teacherFilter === 'all') {
                match = true;
            } else if (teacherFilter === 'unassigned') {
                match = !std.assigned_teacher;
            } else {
                match = (std.assigned_teacher === teacherFilter);
            }

            if (match) {
                activeStudents.push({
                    id: std.Stu_id,
                    no: std.Stu_no,
                    name: `${std.Stu_pre}${std.Stu_name} ${std.Stu_sur}`,
                    nick: std.Stu_nick,
                    addr: std.Stu_addr,
                    subdistrict: std.subdistrict,
                    village: std.village,
                    lat: lat,
                    lng: lng
                });
            }
        });

        // Reset all map markers state
        Object.keys(markers).forEach(id => {
            map.removeLayer(markers[id]);
        });
        routePolylines.forEach(p => map.removeLayer(p));
        routePolylines = [];

        if (activeStudents.length === 0) {
            $('#timelineCard').addClass('hidden');
            alert('ไม่พบนัดเยี่ยมบ้านนักเรียนตามตัวกรองครูที่เลือก');
            return;
        }

        // Show matching student markers on map
        const bounds = [[startLat, startLng]];
        activeStudents.forEach(s => {
            if (markers[s.id]) {
                markers[s.id].addTo(map);
                bounds.push([s.lat, s.lng]);
            }
        });
        map.fitBounds(bounds, { padding: [50, 50] });

        // 2. Cluster using K-Means
        const clusters = kMeansClustering(activeStudents, numDays);

        // 3. Solve TSP and generate schedules
        const plannedDaysData = [];
        clusters.forEach((cluster, index) => {
            if (cluster.length === 0) return;
            const tspResult = solveGreedyTSP(startLat, startLng, cluster);
            
            // Build polyline path coordinates
            const pathCoords = [[startLat, startLng]];
            tspResult.route.forEach(step => {
                pathCoords.push([step.point.lat, step.point.lng]);
            });
            pathCoords.push([startLat, startLng]); // Loop back

            // Render polyline
            const color = pathColors[index % pathColors.length];
            const polyline = L.polyline(pathCoords, {
                color: color,
                weight: 4,
                opacity: 0.8,
                dashArray: '5, 10'
            }).addTo(map);
            routePolylines.push(polyline);

            // Time calculation
            let currentTime = new Date(`2026-01-01T${startTimeStr}:00`);
            const timeline = [];
            let totalDist = 0;

            // Start
            timeline.push({
                time: formatTime(currentTime),
                icon: 'fa-map-marker-alt text-rose-500',
                title: 'เริ่มต้นการเดินทาง',
                desc: `ออกจากจุดเริ่มต้น (${startLat.toFixed(5)}, ${startLng.toFixed(5)})`
            });

            tspResult.route.forEach((step, idx) => {
                totalDist += step.dist;
                
                // Travel time in minutes
                const travelMins = Math.round(step.dist * (60 / speed) + 2); // adding 2 mins start/stop buffer
                currentTime = new Date(currentTime.getTime() + travelMins * 60 * 1000);

                timeline.push({
                    time: formatTime(currentTime),
                    icon: 'fa-house text-blue-500',
                    title: `บ้านคนที่ ${idx + 1}: ${step.point.name} (เลขที่ ${step.point.no})`,
                    desc: `ถึงเวลา ${formatTime(currentTime)} น. • ระยะทางห่างจุดก่อนหน้า ${step.dist.toFixed(2)} กม. • ที่อยู่: ${step.point.addr}`
                });

                // Visit time
                currentTime = new Date(currentTime.getTime() + visitTime * 60 * 1000);
                timeline.push({
                    time: formatTime(currentTime),
                    icon: 'fa-check-circle text-emerald-500',
                    title: `เยี่ยมบ้านเสร็จสิ้น`,
                    desc: `เสร็จเวลา ${formatTime(currentTime)} น. (ใช้เวลาประเมิน ${visitTime} นาที) และออกเดินทางต่อ`
                });
            });

            // Return to start
            totalDist += tspResult.returnDist;
            const returnMins = Math.round(tspResult.returnDist * (60 / speed) + 2);
            currentTime = new Date(currentTime.getTime() + returnMins * 60 * 1000);

            timeline.push({
                time: formatTime(currentTime),
                icon: 'fa-undo text-slate-500',
                title: 'เดินทางกลับถึงจุดเริ่มต้น',
                desc: `ถึงเวลาประมาณ ${formatTime(currentTime)} น. (ระยะทางขากลับ ${tspResult.returnDist.toFixed(2)} กม.)`
            });

            // Find walk alerts (adjacent students less than 400m apart)
            const notes = [];
            for (let i = 0; i < tspResult.route.length - 1; i++) {
                const s1 = tspResult.route[i].point;
                const s2 = tspResult.route[i+1].point;
                const gap = haversineDistance([s1.lat, s1.lng], [s2.lat, s2.lng]);
                if (gap < 0.4) {
                    notes.push(`💡 บ้านของ <strong>${s1.nick || s1.name}</strong> และ <strong>${s2.nick || s2.name}</strong> ห่างกันเพียง ${Math.round(gap * 1000)} เมตร แนะนำจอดรถครั้งเดียวแล้วเดินต่อกันได้เลย`);
                }
            }

            // Determine zone summary based on the most common subdistrict in this day
            const subs = cluster.map(p => p.subdistrict || 'ไม่ระบุตำบล');
            const modeSub = mostFrequent(subs);

            plannedDaysData.push({
                dayIndex: index + 1,
                color: color,
                zone: modeSub,
                totalDistance: totalDist,
                studentCount: cluster.length,
                timeline: timeline,
                notes: notes
            });
        });

        // 4. Render timeline tabs & contents
        renderTimelinesUI(plannedDaysData);
    }

    function mostFrequent(arr) {
        if (arr.length === 0) return '';
        const counts = {};
        let maxCount = 0;
        let mode = '';
        arr.forEach(val => {
            counts[val] = (counts[val] || 0) + 1;
            if (counts[val] > maxCount) {
                maxCount = counts[val];
                mode = val;
            }
        });
        return mode;
    }

    function formatTime(date) {
        return date.toTimeString().substring(0, 5);
    }

    function renderTimelinesUI(days) {
        $('#timelineCard').removeClass('hidden');

        let tabButtonsHtml = '';
        let tabContentsHtml = '';

        days.forEach((day, idx) => {
            const isActive = idx === 0;
            const btnClass = isActive 
                ? 'bg-blue-600 text-white shadow-md' 
                : 'bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600';
            
            tabButtonsHtml += `
                <button onclick="switchTab(${day.dayIndex})" id="tab-btn-${day.dayIndex}" class="tab-btn px-4 py-2 text-xs font-black rounded-xl border border-slate-200 dark:border-slate-600 transition-all ${btnClass}">
                    <span class="inline-block w-2.5 h-2.5 rounded-full mr-1.5" style="background-color: ${day.color};"></span>
                    วันทีี่ ${day.dayIndex} (ตำบล ${day.zone.replace('ต.', '')} • ${day.studentCount} คน)
                </button>
            `;

            const activeContentClass = isActive ? '' : 'hidden';

            let timelineStepsHtml = '';
            day.timeline.forEach(step => {
                timelineStepsHtml += `
                    <div class="relative pl-8 pb-6 border-l-2 border-slate-100 dark:border-slate-700 last:pb-0 last:border-l-0">
                        <div class="absolute -left-[11px] top-0.5 w-5 h-5 rounded-full bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 flex items-center justify-center text-[10px]">
                            <i class="fas ${step.icon}"></i>
                        </div>
                        <div class="flex items-center justify-between gap-4 flex-wrap">
                            <h4 class="font-bold text-xs text-slate-800 dark:text-white">${step.title}</h4>
                            <span class="text-[11px] font-black text-slate-500 bg-slate-100 dark:bg-slate-900 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-800 font-mono">${step.time} น.</span>
                        </div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1 leading-normal">${step.desc}</p>
                    </div>
                `;
            });

            let notesHtml = '';
            if (day.notes.length > 0) {
                notesHtml += `
                    <div class="mt-4 p-4 bg-amber-50/50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/50 rounded-2xl space-y-1.5">
                        <h5 class="text-[11px] font-extrabold text-amber-700 dark:text-amber-400 uppercase tracking-wider flex items-center gap-1.5"><i class="fas fa-lightbulb"></i> ข้อเสนอแนะสำหรับการเดินทาง</h5>
                        <ul class="text-[10px] text-amber-600 dark:text-amber-300 space-y-1 list-none p-0 m-0">
                            ${day.notes.map(n => `<li>${n}</li>`).join('')}
                        </ul>
                    </div>
                `;
            }

            tabContentsHtml += `
                <div id="tab-content-${day.dayIndex}" class="tab-content ${activeContentClass} animate-fadeIn">
                    <div class="flex items-center justify-between text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-900/50 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 mb-6">
                        <span>ระยะทางเดินทางรวมวันนี้: <strong class="text-blue-600 dark:text-blue-400">${day.totalDistance.toFixed(2)} กม.</strong></span>
                        <span>จำนวนนักเรียนที่เยี่ยม: <strong class="text-indigo-600">${day.studentCount} คน</strong></span>
                    </div>

                    <!-- Steps Timeline -->
                    <div class="space-y-0.5">
                        ${timelineStepsHtml}
                    </div>

                    <!-- Notes -->
                    ${notesHtml}
                </div>
            `;
        });

        $('#dayTabButtons').html(tabButtonsHtml);
        $('#dayTabContents').html(tabContentsHtml);
    }

    function switchTab(dayIndex) {
        $('.tab-btn').removeClass('bg-blue-600 text-white shadow-md')
                     .addClass('bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600');
        $(`#tab-btn-${dayIndex}`).removeClass('bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600')
                                 .addClass('bg-blue-600 text-white shadow-md');
        
        $('.tab-content').addClass('hidden');
        $(`#tab-content-${dayIndex}`).removeClass('hidden');
    }

    function printTimeline() {
        const printContent = document.getElementById('dayTabContents').innerHTML;
        const paramTeacherName = $('#paramTeacher option:selected').text();
        const paramDaysNum = $('#paramDays').val();
        
        const win = window.open('', '_blank');
        win.document.write(`
            <html>
                <head>
                    <title>พิมพ์แผนสรุปเส้นทางเยี่ยมบ้าน</title>
                    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                    <style>
                        body { font-family: 'Sarabun', sans-serif; padding: 20px; font-size: 13px; color: #333; }
                        h1, h2 { text-align: center; margin-bottom: 2px; }
                        h2 { font-size: 15px; font-weight: normal; color: #666; margin-bottom: 20px; }
                        .tab-content { display: block !important; margin-bottom: 40px; page-break-after: always; }
                        .relative { position: relative; padding-left: 30px; margin-bottom: 15px; border-left: 2px solid #ddd; }
                        .absolute { position: absolute; left: -11px; top: 0px; background: white; width: 20px; height: 20px; text-align: center; line-height: 20px; border: 1px solid #aaa; border-radius: 50%; font-size: 9px; }
                        .flex { display: flex; justify-content: space-between; font-weight: bold; }
                        .font-mono { font-family: monospace; background: #eee; padding: 2px 6px; border-radius: 4px; }
                        .text-slate-400 { font-size: 11px; color: #777; margin-top: 4px; }
                        .p-4 { padding: 12px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 20px; }
                        .mt-4 { margin-top: 15px; padding: 10px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; }
                        ul { padding-left: 20px; margin: 5px 0 0 0; font-size: 12px; color: #b45309; }
                    </style>
                </head>
                <body>
                    <h1>แผนการเดินทางเยี่ยมบ้านนักเรียน ม.${<?= json_encode($class) ?>}/${<?= json_encode($room) ?>}</h1>
                    <h2>ครูผู้เยี่ยมบ้าน: ${paramTeacherName} • จัดกลุ่ม ${paramDaysNum} วัน</h2>
                    ${printContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            window.close();
                        }
                    <\/script>
                </body>
            </html>
        `);
        win.document.close();
    }

    $(document).ready(function() {
        initMap();

        $('#paramTeacher, #paramDays').on('change', function() {
            calculateAndRenderRoutes();
        });
    });
</script>

<style>
    .leaflet-container img {
        max-width: none !important;
        max-height: none !important;
    }
    /* Simple dynamic fade-in animation for tab transitions */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
