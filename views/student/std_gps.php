<?php
/**
 * View: Record GPS (std_gps.php)
 * UI for recording student home coordinates
 */
ob_start();
$student = $_SESSION['student_data'] ?? [];
$currentGps = $currentGps ?? null;
?>

<!-- Leaflet Assets (External) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<div class="space-y-6 md:space-y-8 animate-fadeIn">
    
    <!-- Hero Section (Dashboard Style) -->
    <div class="relative overflow-hidden rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 p-6 md:p-10 shadow-2xl">
        <!-- Decorative Blobs -->
        <div class="absolute top-0 right-0 w-72 h-72 bg-white/10 rounded-full -mr-36 -mt-36 blur-2xl"></div>
        <div class="absolute bottom-0 left-0 w-72 h-72 bg-white/10 rounded-full -ml-36 -mb-36 blur-2xl"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 md:gap-8 text-center md:text-left">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-[1.5rem] md:rounded-[2rem] bg-white/20 backdrop-blur-md border-4 border-white/30 flex items-center justify-center text-white shadow-2xl">
                <i class="fas fa-map-location-dot text-4xl md:text-5xl animate-bounce"></i>
            </div>
            <div>
                <p class="text-blue-200 text-sm font-black uppercase tracking-[0.3em] mb-2">GPS Location Tracking</p>
                <h1 class="text-2xl md:text-4xl font-black text-white mb-2 tracking-tight">บันทึกพิกัดบ้านนักเรียน</h1>
                <p class="text-blue-100/80 font-medium text-sm md:text-lg max-w-xl">
                    ปักหมุดตำแหน่งบ้านของคุณเพื่อให้คุณครูเดินทางไปเยี่ยมบ้านได้อย่างสะดวกและแม่นยำ
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Control Panel -->
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-location-crosshairs"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">สถานะตำแหน่ง</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">ความแม่นยำ (Accuracy)</p>
                        <p id="accuracyValue" class="text-xl font-black text-slate-700 dark:text-slate-200">รอการตรวจสอบ...</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Latitude</p>
                            <p id="latValue" class="text-md font-black text-blue-600 dark:text-blue-400"><?= $currentGps['latitude'] ?? '-' ?></p>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/40 border border-slate-100 dark:border-slate-800">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Longitude</p>
                            <p id="lngValue" class="text-md font-black text-blue-600 dark:text-blue-400"><?= $currentGps['longitude'] ?? '-' ?></p>
                        </div>
                    </div>

                    <?php if($currentGps): ?>
                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
                        <i class="fas fa-history text-emerald-500"></i>
                        <div>
                            <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">อัปเดตล่าสุด</p>
                            <p class="text-xs font-bold text-emerald-700 dark:text-emerald-300"><?= date('j M Y, H:i', strtotime($currentGps['updated_at'])) ?> น.</p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <button onclick="getLocation()" class="w-full mt-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-indigo-700 hover:to-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-500/30 transition-all flex items-center justify-center gap-3 active:scale-95 group">
                    <i class="fas fa-crosshairs text-lg group-hover:rotate-90 transition-transform"></i>
                    ดึงตำแหน่งปัจจุบัน
                </button>
            </div>

            <!-- Manual Coordinates & Google Maps Link Card -->
            <div class="glass-effect rounded-[2rem] p-6 border border-white/50 shadow-xl space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg">
                        <i class="fas fa-keyboard"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-tight">กรอกพิกัดด้วยตนเอง</h3>
                </div>

                <!-- Google Maps Link Input -->
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest block">วางลิงก์ Google Maps หรือ พิกัดที่คัดลอกมา</label>
                    <div class="relative">
                        <input type="text" id="gmapsLinkInput" placeholder="วางลิงก์ เช่น https://www.google.com/maps/..." class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-slate-400 pr-10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <i class="fab fa-google"></i>
                        </span>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs font-bold text-slate-400 dark:text-slate-500 my-2">
                    <hr class="w-full border-slate-200 dark:border-slate-700">
                    <span class="px-3">หรือ</span>
                    <hr class="w-full border-slate-200 dark:border-slate-700">
                </div>

                <!-- Manual Lat / Lng inputs -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest block">Latitude (ละติจูด)</label>
                        <input type="number" step="any" id="manualLat" placeholder="17.6582" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-slate-400 dark:text-slate-400 uppercase tracking-widest block">Longitude (ลองจิจูด)</label>
                        <input type="number" step="any" id="manualLng" placeholder="100.1415" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-bold text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all">
                    </div>
                </div>

                <button onclick="applyManualCoordinates()" class="w-full py-3.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-purple-600 hover:to-indigo-500 text-white font-black rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-2 active:scale-95">
                    <i class="fas fa-check-circle"></i>
                    นำไปใช้บนแผนที่
                </button>
            </div>

            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-[2rem] p-6 border border-amber-100 dark:border-amber-800/30">
                <h3 class="text-sm font-black text-amber-800 dark:text-amber-400 mb-4 flex items-center gap-2">
                    <i class="fas fa-circle-info"></i>
                    คำแนะนำการใช้งาน
                </h3>
                <ul class="space-y-3 text-xs font-bold text-amber-700/80 dark:text-amber-400/60 leading-relaxed">
                    <li class="flex gap-2"><span>1.</span><span>เปิด GPS ในมือถือก่อนกดปุ่มดึงตำแหน่ง</span></li>
                    <li class="flex gap-2"><span>2.</span><span>ลากหมุดบนแผนที่เพื่อปรับตำแหน่งบ้านให้ตรงที่สุด</span></li>
                    <li class="flex gap-2"><span>3.</span><span>กดบันทึกเมื่อเข็มหมุดอยู่ตรงหลังคาบ้านของคุณ</span></li>
                </ul>
            </div>
        </div>

        <!-- Map Container -->
        <div class="lg:col-span-2">
            <div class="glass-effect rounded-[2rem] p-4 border border-white/50 shadow-xl flex flex-col min-h-[600px] lg:h-full">
                <div id="map" class="rounded-[1.5rem] overflow-hidden border border-slate-200 dark:border-slate-700 w-full bg-slate-100 relative z-10" style="height: 500px;">
                    <!-- Loading Indicator -->
                    <div id="map-loading" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-50/50 backdrop-blur-sm z-50">
                        <div class="w-12 h-12 border-4 border-blue-500/20 border-t-blue-600 rounded-full animate-spin mb-4"></div>
                        <p class="text-xs font-black text-slate-500 animate-pulse uppercase tracking-widest">กำลังโหลดแผนที่...</p>
                    </div>
                </div>
                
                <div class="pt-6 pb-2 px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3 text-slate-400">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center animate-bounce">
                            <i class="fas fa-hand-pointer text-[10px]"></i>
                        </div>
                        <p class="text-[12px] font-bold">คุณสามารถลากหมุดเพื่อปรับตำแหน่งได้</p>
                    </div>
                    <button id="saveBtn" disabled onclick="saveGps()" class="w-full sm:w-auto px-12 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-teal-600 hover:to-emerald-500 disabled:from-slate-100 disabled:to-slate-100 dark:disabled:from-slate-800 dark:disabled:to-slate-800 disabled:text-slate-400 disabled:cursor-not-allowed text-white font-black rounded-2xl shadow-xl shadow-emerald-500/20 transition-all flex items-center justify-center gap-3">
                        <i class="fas fa-floppy-disk"></i>
                        บันทึกพิกัดตำแหน่งบ้าน
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let map;
    let marker;
    let currentLat = <?= $currentGps['latitude'] ?? '17.6582' ?>;
    let currentLng = <?= $currentGps['longitude'] ?? '100.1415' ?>;
    let currentAcc = 0;

    function initMap() {
        console.log("initMap starting...");
        const mapContainer = document.getElementById('map');
        if (!mapContainer) {
            console.error("Map container not found");
            return;
        }

        if (typeof L === 'undefined') {
            console.error("Leaflet (L) is undefined. Retrying in 1s...");
            setTimeout(initMap, 1000);
            return;
        }

        try {
            // Create map
            map = L.map('map', {
                center: [currentLat, currentLng],
                zoom: <?= $currentGps ? '18' : '13' ?>,
                zoomControl: true,
                layers: []
            });
            console.log("Map object created successfully");

            // Add Tile Layer (Standard OSM)
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            console.log("Tile layer added");

            // Add Marker
            marker = L.marker([currentLat, currentLng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateValues(pos.lat, pos.lng);
            });

            // Handle map ready
            map.whenReady(() => {
                console.log("Map is ready!");
                document.getElementById('map-loading').style.display = 'none';
                setTimeout(() => map.invalidateSize(), 300);
            });

            <?php if($currentGps): ?>
                updateValues(currentLat, currentLng);
                $('#saveBtn').prop('disabled', false);
            <?php endif; ?>

        } catch (e) {
            console.error("Map Error:", e);
            document.getElementById('map-loading').innerHTML = '<p class="text-red-500 font-bold">เกิดข้อผิดพลาดในการโหลดแผนที่</p>';
        }
    }

    function updateValues(lat, lng, acc = null) {
        currentLat = lat;
        currentLng = lng;
        $('#latValue').text(lat.toFixed(6));
        $('#lngValue').text(lng.toFixed(6));
        
        if (acc !== null) {
            currentAcc = acc;
            $('#accuracyValue').text('± ' + acc.toFixed(1) + ' เมตร');
            
            const indicator = $('#accuracyValue');
            indicator.removeClass('text-emerald-500 text-amber-500 text-rose-500 text-slate-700');
            if (acc < 50) indicator.addClass('text-emerald-500');
            else if (acc < 200) indicator.addClass('text-amber-500');
            else indicator.addClass('text-rose-500');
        }
        
        $('#saveBtn').prop('disabled', false);
    }

    function getLocation() {
        if (!navigator.geolocation) {
            Swal.fire('Oops!', 'เบราว์เซอร์ของคุณไม่รองรับการดึงพิกัด GPS', 'error');
            return;
        }

        Swal.fire({
            title: 'กำลังดึงพิกัด...',
            text: 'กรุณารอสักครู่เพื่อให้ระบบค้นหาตำแหน่งที่แม่นยำที่สุด',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                const acc = pos.coords.accuracy;

                if (map) {
                    map.setView([lat, lng], 18);
                    marker.setLatLng([lat, lng]);
                    map.invalidateSize();
                }
                updateValues(lat, lng, acc);
                
                Swal.fire({
                    icon: 'success',
                    title: 'พบตำแหน่งแล้ว!',
                    text: 'ตำแหน่งปัจจุบันของคุณแสดงบนแผนที่แล้ว',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            (err) => {
                Swal.fire('Error', 'ไม่สามารถเข้าถึงตำแหน่งได้: ' + err.message, 'error');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    function parseGmapsInput(input) {
        input = input.trim();
        if (!input) return null;

        // Try matching various coordinate patterns in URL or raw text
        const urlPatterns = [
            /@(-?\d+\.\d+),(-?\d+\.\d+)/,                  // @lat,lng
            /place\/(-?\d+\.\d+),(-?\d+\.\d+)/,            // place/lat,lng
            /[?&]q=(-?\d+\.\d+),(-?\d+\.\d+)/,             // q=lat,lng
            /query=(-?\d+\.\d+),(-?\d+\.\d+)/,             // query=lat,lng
            /(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/,             // raw lat,lng
            /(-?\d+\.\d+)\s+(-?\d+\.\d+)/                  // space-separated lat lng
        ];

        for (let pattern of urlPatterns) {
            const match = input.match(pattern);
            if (match) {
                const lat = parseFloat(match[1]);
                const lng = parseFloat(match[2]);
                if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    return { lat, lng };
                }
            }
        }
        return null;
    }

    function applyManualCoordinates() {
        const linkVal = $('#gmapsLinkInput').val().trim();
        let lat = parseFloat($('#manualLat').val());
        let lng = parseFloat($('#manualLng').val());
        
        if (linkVal) {
            const parsed = parseGmapsInput(linkVal);
            if (parsed) {
                lat = parsed.lat;
                lng = parsed.lng;
                $('#manualLat').val(lat.toFixed(6));
                $('#manualLng').val(lng.toFixed(6));
            } else if (linkVal.includes('maps.app.goo.gl') || linkVal.includes('goo.gl/maps')) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่สามารถแยกพิกัดจากลิงก์ย่อได้',
                    text: 'หากใช้ลิงก์ย่อ (เช่น maps.app.goo.gl) กรุณาเปิดลิงก์นั้นบนเบราว์เซอร์ก่อน แล้วคัดลอก URL ยาวที่มีตัวเลขพิกัด (เช่น @17.658234,100.141523) หรือคัดลอกเฉพาะพิกัดตัวเลขมาวางโดยตรง',
                    confirmButtonText: 'รับทราบ'
                });
                return;
            } else {
                Swal.fire('รูปแบบไม่ถูกต้อง', 'ไม่พบพิกัดในลิงก์หรือข้อความที่กรอก กรุณากรอกพิกัดเป็นตัวเลข เช่น 17.658234, 100.141523', 'error');
                return;
            }
        }
        
        if (isNaN(lat) || isNaN(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) {
            Swal.fire('พิกัดไม่ถูกต้อง', 'กรุณาระบุ Latitude (-90 ถึง 90) และ Longitude (-180 ถึง 180) ให้ถูกต้อง', 'error');
            return;
        }
        
        if (map) {
            map.setView([lat, lng], 18);
            marker.setLatLng([lat, lng]);
            map.invalidateSize();
        }
        
        updateValues(lat, lng, 0);
        $('#accuracyValue').text('ระบุตำแหน่งด้วยตนเอง');
        
        Swal.fire({
            icon: 'success',
            title: 'ปรับตำแหน่งบนแผนที่แล้ว!',
            text: 'ตำแหน่งหมุดถูกปรับตามพิกัดที่คุณระบุแล้ว',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function saveGps() {
        Swal.fire({
            title: 'ยืนยันการบันทึก?',
            text: 'พิกัดนี้จะถูกบันทึกเป็นที่ตั้งบ้านของคุณ',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10b981',
            confirmButtonText: 'ใช่, บันทึกเลย',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'api/save_gps.php',
                    method: 'POST',
                    data: {
                        latitude: currentLat,
                        longitude: currentLng,
                        accuracy: currentAcc
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('สำเร็จ!', 'บันทึกพิกัดบ้านเรียบร้อยแล้ว', 'success');
                        } else {
                            Swal.fire('ผิดพลาด', response.message || 'ไม่สามารถบันทึกได้', 'error');
                        }
                    },
                    error: () => Swal.fire('Error', 'เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error')
                });
            }
        });
    }
    // Initialize when everything is loaded
    $(document).ready(function() {
        setTimeout(() => {
            initMap();
            if (map) {
                map.invalidateSize();
                console.log("Map initialized and size invalidated");
            }
        }, 300);

        // Auto-parse Google Maps link/coordinates on input
        $('#gmapsLinkInput').on('input', function() {
            const val = $(this).val();
            const parsed = parseGmapsInput(val);
            if (parsed) {
                $('#manualLat').val(parsed.lat.toFixed(6));
                $('#manualLng').val(parsed.lng.toFixed(6));
                
                // Add highlight visual feedback
                $('#manualLat, #manualLng').addClass('border-indigo-500 ring-2 ring-indigo-500/20').removeClass('border-slate-200 dark:border-slate-700');
                setTimeout(() => {
                    $('#manualLat, #manualLng').removeClass('border-indigo-500 ring-2 ring-indigo-500/20').addClass('border-slate-200 dark:border-slate-700');
                }, 1000);
            }
        });
    });
</script>

<style>
    .leaflet-container { font-family: inherit; z-index: 10 !important; }
    .leaflet-bar a { background-color: white !important; border: none !important; color: #64748b !important; border-radius: 12px !important; margin-bottom: 5px !important; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important; }
    
    /* Fix Tailwind CSS conflict with Leaflet Images */
    .leaflet-container img {
        max-width: none !important;
        max-height: none !important;
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../views/layouts/student_app.php';
?>
