<?php
/**
 * View: Teacher GPS Visit Home
 * Displays a map with all student locations
 */
ob_start();
?>

<!-- Leaflet Assets (External) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row items-center justify-between gap-4 animate-fadeIn">
        <div class="flex items-center gap-4">
            <a href="visithome.php" class="w-12 h-12 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-blue-600 shadow-md transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-black text-slate-800 dark:text-white flex items-center gap-3">
                    <i class="fas fa-map-location-dot text-blue-600"></i> แผนที่บ้านนักเรียน
                </h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">ชั้นมัธยมศึกษาปีที่ <?= htmlspecialchars($class . "/" . $room); ?> • พบพิกัด <?= count($studentGpsList) ?> คน</p>
            </div>
        </div>
    </div>

    <!-- Map and List Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 h-[800px] max-h-[80vh]">
        <!-- Student List Sidebar -->
        <div class="lg:col-span-1 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 relative z-20">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2"><i class="fas fa-users text-blue-500"></i> รายชื่อ (มีพิกัด)</h3>
                    <button id="toggleRouteMode" class="text-xs font-bold px-3 py-1.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded-lg hover:bg-blue-200 transition-colors flex items-center gap-1.5 border border-blue-200 dark:border-blue-800">
                        <i class="fas fa-route"></i> จัดเส้นทาง
                    </button>
                </div>
                
                <div id="routeToolbar" class="hidden flex-col gap-3 pt-2 border-t border-slate-200 dark:border-slate-700 mt-2">
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-700 dark:text-slate-300 font-bold select-none hover:text-blue-600 transition-colors">
                            <input type="checkbox" id="selectAllStudents" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 transition-all">
                            เลือกทั้งหมด
                        </label>
                        <span class="text-xs font-bold text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded-md border border-slate-200 dark:border-slate-700">เลือก <span id="selectedCount" class="text-blue-600">0</span> คน</span>
                    </div>
                    <button id="calcRouteBtn" onclick="generateGoogleRoute()" class="w-full px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-500/30 hover:bg-emerald-700 transition-all opacity-50 cursor-not-allowed flex items-center justify-center gap-2" disabled>
                        <i class="fas fa-location-arrow"></i> นำทางกลุ่มที่เลือก
                    </button>
                    <p class="text-[10px] text-slate-400 text-center leading-tight">ระบบจะสร้างเส้นทาง Google Maps โดยแวะตามจุดที่เลือก (สูงสุด 9-10 จุดเพื่อความแม่นยำ)</p>
                </div>
            </div>
            
            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-slate-50/30 dark:bg-slate-900/20">
                <?php if (empty($studentGpsList)): ?>
                    <div class="text-center py-8 text-slate-400">
                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-map-marker-alt text-2xl opacity-50"></i>
                        </div>
                        <p class="font-medium text-sm">ยังไม่มีนักเรียนบันทึกพิกัด</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($studentGpsList as $std): ?>
                        <div class="student-item-container relative group">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 z-10 hidden checkbox-wrapper">
                                <input type="checkbox" class="student-checkbox rounded border-slate-300 text-emerald-500 focus:ring-emerald-500 w-5 h-5 cursor-pointer shadow-sm transition-all" value="<?= $std['Stu_id'] ?>" data-lat="<?= $std['latitude'] ?>" data-lng="<?= $std['longitude'] ?>">
                            </div>
                            
                            <button onclick="focusMap(<?= $std['latitude'] ?>, <?= $std['longitude'] ?>, '<?= $std['Stu_id'] ?>')" class="student-btn w-full text-left p-3 pl-4 rounded-2xl border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 hover:bg-white dark:hover:bg-slate-800 transition-all bg-white dark:bg-slate-800 shadow-sm active:scale-[0.98]">
                                <div class="flex items-center justify-between">
                                    <div class="student-info-content transition-transform duration-300">
                                        <p class="font-bold text-slate-800 dark:text-white text-sm group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                            <?= $std['Stu_pre'] . $std['Stu_name'] . " " . $std['Stu_sur'] ?>
                                        </p>
                                        <p class="text-[11px] font-bold text-slate-400 mt-1">เลขที่: <?= $std['Stu_no'] ?> • รหัส: <?= $std['Stu_id'] ?></p>
                                    </div>
                                    <i class="fas fa-map-marker-alt text-slate-300 group-hover:text-rose-500 transition-colors text-lg"></i>
                                </div>
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Map Container -->
        <div class="lg:col-span-3 bg-white dark:bg-slate-800 rounded-[2rem] border border-slate-100 dark:border-slate-700 shadow-xl overflow-hidden relative">
            <div id="visithomeMap" class="w-full h-full relative z-10" style="min-height: 500px;"></div>
            
            <!-- Floating Map Legend -->
            <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-4 rounded-2xl shadow-lg border border-slate-200 dark:border-slate-700">
                <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2">คำอธิบาย</p>
                <div class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                    <div class="flex items-center gap-2">
                        <img src="https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png" class="h-6 w-auto" alt="marker">
                        <span>ตำแหน่งบ้านนักเรียน</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let markers = {};

    function initMap() {
        const studentData = <?= json_encode($studentGpsList) ?>;
        
        if (studentData.length === 0) {
            // Default center to Thailand if no data
            map = L.map('visithomeMap').setView([15.8700, 100.9925], 6);
        } else {
            // Center to the first student initially
            map = L.map('visithomeMap').setView([studentData[0].latitude, studentData[0].longitude], 12);
        }

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

        const bounds = [];

        studentData.forEach(std => {
            const lat = parseFloat(std.latitude);
            const lng = parseFloat(std.longitude);
            
            if (!isNaN(lat) && !isNaN(lng)) {
                bounds.push([lat, lng]);
                
                const popupContent = `
                    <div class="text-center p-2">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-2 text-lg font-bold">
                            ${std.Stu_no}
                        </div>
                        <h4 class="font-bold text-sm text-slate-800 mb-1">${std.Stu_pre}${std.Stu_name} ${std.Stu_sur}</h4>
                        <p class="text-xs text-slate-500 mb-3">รหัส: ${std.Stu_id}</p>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}" target="_blank" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors shadow-md">
                            <i class="fas fa-location-arrow"></i> นำทาง
                        </a>
                    </div>
                `;

                // Default blue marker
                const defaultIcon = L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                });

                const marker = L.marker([lat, lng], {icon: defaultIcon}).bindPopup(popupContent).addTo(map);
                markers[std.Stu_id] = marker;
            }
        });

        // Fit map to show all markers
        if (bounds.length > 0) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }

    function focusMap(lat, lng, stuId) {
        if (map) {
            map.setView([lat, lng], 18, { animate: true, duration: 1 });
            if (markers[stuId]) {
                setTimeout(() => {
                    markers[stuId].openPopup();
                }, 500);
            }
        }
    }

    // --- Route Mode Logic ---
    let isRouteMode = false;
    
    function updateRouteUI() {
        const checkedBoxes = $('.student-checkbox:checked');
        const count = checkedBoxes.length;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#calcRouteBtn').removeClass('opacity-50 cursor-not-allowed').removeAttr('disabled');
        } else {
            $('#calcRouteBtn').addClass('opacity-50 cursor-not-allowed').attr('disabled', true);
        }

        // Highlight markers
        const greenIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });
        
        const blueIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        // Reset all to blue first
        Object.keys(markers).forEach(id => {
            markers[id].setIcon(blueIcon);
            markers[id].setOpacity(isRouteMode ? 0.4 : 1); // Dim unselected in route mode
        });

        // Set checked to green
        if (isRouteMode) {
            checkedBoxes.each(function() {
                const id = $(this).val();
                if (markers[id]) {
                    markers[id].setIcon(greenIcon);
                    markers[id].setOpacity(1);
                }
            });
        }
    }

    function generateGoogleRoute() {
        const checkedBoxes = $('.student-checkbox:checked');
        if (checkedBoxes.length === 0) return;

        const points = [];
        checkedBoxes.each(function() {
            points.push({
                lat: $(this).data('lat'),
                lng: $(this).data('lng')
            });
        });

        // Google Maps URL logic: Origin (current location ideally, but we'll leave it blank so Maps asks user), 
        // Destination (last point), Waypoints (middle points)
        const destination = points[points.length - 1];
        
        let url = `https://www.google.com/maps/dir/?api=1&destination=${destination.lat},${destination.lng}`;
        
        if (points.length > 1) {
            // Add waypoints
            const waypoints = points.slice(0, -1).map(p => `${p.lat},${p.lng}`).join('|');
            url += `&waypoints=${waypoints}`;
        }
        
        window.open(url, '_blank');
    }

    $(document).ready(function() {
        initMap();
        
        // Toggle Route Mode
        $('#toggleRouteMode').on('click', function() {
            isRouteMode = !isRouteMode;
            
            if (isRouteMode) {
                $(this).removeClass('bg-blue-100 text-blue-700').addClass('bg-emerald-600 text-white border-emerald-600');
                $('#routeToolbar').removeClass('hidden').addClass('flex');
                $('.checkbox-wrapper').removeClass('hidden');
                $('.student-info-content').addClass('translate-x-8');
            } else {
                $(this).addClass('bg-blue-100 text-blue-700').removeClass('bg-emerald-600 text-white border-emerald-600');
                $('#routeToolbar').addClass('hidden').removeClass('flex');
                $('.checkbox-wrapper').addClass('hidden');
                $('.student-info-content').removeClass('translate-x-8');
                
                // Uncheck all
                $('.student-checkbox').prop('checked', false);
                $('#selectAllStudents').prop('checked', false);
            }
            
            updateRouteUI();
        });

        // Checkbox events
        $('.student-checkbox').on('change', function() {
            updateRouteUI();
            
            // Check if all are checked
            const allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
            $('#selectAllStudents').prop('checked', allChecked);
        });

        // Select All event
        $('#selectAllStudents').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.student-checkbox').prop('checked', isChecked);
            updateRouteUI();
        });
        
        // Ensure map resizes correctly
        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 300);
    });
</script>

<style>
    /* Fix Tailwind CSS conflict with Leaflet Images */
    .leaflet-container img {
        max-width: none !important;
        max-height: none !important;
    }

    .leaflet-popup-content-wrapper {
        border-radius: 1rem;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        border: 1px solid #f1f5f9;
    }
    .leaflet-popup-content {
        margin: 0;
        min-width: 200px;
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/teacher_app.php';
?>
