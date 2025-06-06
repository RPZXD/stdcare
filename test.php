<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตารางสอน</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Mali:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap');
        
        body {
            font-family: 'Mali', 'Sarabun', sans-serif;
            background-color: #f0f4f8;
        }
        
        .schedule-cell {
            min-height: 80px;
            transition: all 0.3s cubic-bezier(.4,2,.6,1);
            border: 2px solid #e0e7ff;
            box-shadow: 0 2px 8px 0 rgba(80,80,180,0.07);
            position: relative;
            z-index: 1;
        }
        
        .schedule-cell:hover {
            transform: scale(1.04) translateY(-2px);
            box-shadow: 0 8px 24px 0 rgba(80,80,180,0.18), 0 0 0 4px #a5b4fc55;
            animation: bounce 0.4s;
            border-color: #6366f1;
            z-index: 2;
        }
        
        @keyframes bounce {
            0%   { transform: scale(1.04) translateY(-2px);}
            30%  { transform: scale(1.08) translateY(-8px);}
            60%  { transform: scale(0.98) translateY(2px);}
            100% { transform: scale(1.04) translateY(-2px);}
        }
        
        .time-cell {
            transition: all 0.3s ease;
        }
        
        .time-cell:hover {
            background-color: #e5edff;
        }
        
        .day-header {
            transition: all 0.3s ease;
        }
        
        .day-header:hover {
            background-color: #dbeafe;
        }
        
        @media (max-width: 768px) {
            .schedule-container {
                overflow-x: auto;
            }
            
            .schedule-table {
                min-width: 768px;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-indigo-800">ตารางสอน</h1>
                <p class="text-gray-600 mt-2">ของครูทิวา  เรืองศรี</p>
            </div>
            
            <div class="bg-white rounded-xl shadow-lg overflow-hidden schedule-container">
                <div class="p-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white flex justify-between items-center">
                    <h2 class="text-xl font-semibold">ตารางเวลาของครูทิวา  เรืองศรี</h2>
                    <div class="flex space-x-2">
                        <button id="print-btn" class="bg-white text-indigo-600 px-4 py-1 rounded-md text-sm font-medium hover:bg-indigo-50 transition">
                            🖨️ พิมพ์
                        </button>
                        <button id="theme-toggle" class="bg-white text-indigo-600 px-4 py-1 rounded-md text-sm font-medium hover:bg-indigo-50 transition">
                            🌙 โหมดมืด
                        </button>
                    </div>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full schedule-table">
                        <thead>
                            <tr class="bg-indigo-50">
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium">เวลา</th>
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium day-header">จันทร์</th>
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium day-header">อังคาร</th>
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium day-header">พุธ</th>
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium day-header">พฤหัสบดี</th>
                                <th class="py-4 px-2 border-b border-r border-indigo-100 text-indigo-800 font-medium day-header">ศุกร์</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Row 1: 08:30-09:25 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">08:30–09:25</td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-blue-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว23104 (3/12)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-blue-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว23104 (3/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 2: 09:25-10:20 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">09:25–10:20</td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-blue-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว23104 (3/12)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-blue-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว23104 (3/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 3: 10:20-11:15 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">10:20–11:15</td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-yellow-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว33281 (6/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-rose-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว32281 (5/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                            </tr>
                            
                            <!-- Row 4: 11:15-12:10 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">11:15–12:10</td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-yellow-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว33281 (6/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-rose-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว32281 (5/3)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                            </tr>
                            
                            <!-- Row 5: 12:10-13:05 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">12:10–13:05</td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-cyan-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว22104 (2/6)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-pink-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว22104 (2/11)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                            </tr>
                            
                            <!-- Row 6: 13:05-14:00 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">13:05–14:00</td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-cyan-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว22104 (2/6)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200"></td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-pink-50 rounded-lg m-1">
                                        <div class="font-medium text-blue-800">ว22104 (2/11)</div>
                                        <div class="text-sm text-blue-600">ห้อง 414</div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200"></td>
                            </tr>
                            
                            <!-- Row 7: 14:00-14:55 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">14:00–14:55</td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-cyan-100 to-blue-200 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-indigo-200">
                                        <span class="text-2xl animate-pulse">🔬</span>
                                        <div>
                                            <div class="font-bold text-blue-900 drop-shadow">ว23104 (3/8)</div>
                                            <div class="text-sm text-blue-700 italic">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-cyan-100 to-blue-200 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-indigo-200">
                                        <span class="text-2xl animate-pulse">🔬</span>
                                        <div>
                                            <div class="font-bold text-blue-900 drop-shadow">ว23104 (3/5)</div>
                                            <div class="text-sm text-blue-700 italic">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-yellow-100 to-yellow-300 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-yellow-200">
                                        <span class="text-2xl animate-bounce">👩‍🏫</span>
                                        <div>
                                            <div class="font-bold text-yellow-900 drop-shadow">ประชุมกลุ่มสาระ</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-pink-100 to-rose-200 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-rose-200">
                                        <span class="text-2xl animate-bounce">🛡️</span>
                                        <div>
                                            <div class="font-bold text-rose-900 drop-shadow">ส20245</div>
                                            <div class="text-sm text-rose-700 italic">ป้องกันทุจริต</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-amber-100 to-amber-300 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-amber-200">
                                        <span class="text-2xl animate-pulse">🧪</span>
                                        <div>
                                            <div class="font-bold text-amber-900 drop-shadow">ว20295 (3/3)</div>
                                            <div class="text-sm text-amber-700 italic">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 8: 14:55-15:50 -->
                            <tr>
                                <td class="py-2 px-3 border-b border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">14:55–15:50</td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-cyan-100 to-blue-200 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-indigo-200">
                                        <span class="text-2xl animate-pulse">🔬</span>
                                        <div>
                                            <div class="font-bold text-blue-900 drop-shadow">ว23104 (3/8)</div>
                                            <div class="text-sm text-blue-700 italic">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-cyan-100 to-blue-200 rounded-xl m-1 flex items-center gap-2 shadow-lg hover:ring-4 hover:ring-indigo-200">
                                        <span class="text-2xl animate-pulse">🔬</span>
                                        <div>
                                            <div class="font-bold text-blue-900 drop-shadow">ว23104 (3/5)</div>
                                            <div class="text-sm text-blue-700 italic">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-pink-100 to-pink-300 rounded-lg m-1 flex items-center gap-2 shadow">
                                        <span class="text-xl">🎨</span>
                                        <div>
                                            <div class="font-medium text-pink-900">ชุมนุม</div>
                                            <div class="text-sm text-pink-700">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-r border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-lime-100 to-lime-300 rounded-lg m-1 flex items-center gap-2 shadow">
                                        <span class="text-xl">📚</span>
                                        <div>
                                            <div class="font-medium text-lime-900">ประชุมระดับชั้น</div>
                                            <div class="text-sm text-lime-700">ม.3</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-gray-200">
                                    <div class="schedule-cell p-2 bg-gradient-to-r from-amber-100 to-amber-300 rounded-lg m-1 flex items-center gap-2 shadow">
                                        <span class="text-xl">🧪</span>
                                        <div>
                                            <div class="font-medium text-amber-900">ว20295 (3/3)</div>
                                            <div class="text-sm text-amber-700">ห้อง 414</div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Row 9: 15:50-16:45 -->
                            <tr>
                                <td class="py-2 px-3 border-r border-gray-200 bg-gray-50 text-gray-700 font-medium time-cell">15:50–16:45</td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td class="border-r border-gray-200"></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 bg-indigo-50">
                    <div class="flex flex-wrap gap-3">
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-blue-50 border border-blue-200 mr-2"></div>
                            <span class="text-sm text-gray-700">ว32281/ว33281</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-green-50 border border-green-200 mr-2"></div>
                            <span class="text-sm text-gray-700">ว32104</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-teal-50 border border-teal-200 mr-2"></div>
                            <span class="text-sm text-gray-700">ว22104</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-purple-50 border border-purple-200 mr-2"></div>
                            <span class="text-sm text-gray-700">เสริมทักษะ</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-yellow-50 border border-yellow-200 mr-2"></div>
                            <span class="text-sm text-gray-700">ประชุม/PLC</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-amber-50 border border-amber-200 mr-2"></div>
                            <span class="text-sm text-gray-700">ว20295</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-red-50 border border-red-200 mr-2"></div>
                            <span class="text-sm text-gray-700">สอว.0245</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-4 h-4 rounded bg-pink-50 border border-pink-200 mr-2"></div>
                            <span class="text-sm text-gray-700">กิจกรรมชุมนุม</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center text-gray-500 text-sm">
                <p>คลิกที่ช่องตารางเพื่อดูรายละเอียดเพิ่มเติม</p>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="schedule-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
        <div class="bg-white rounded-xl shadow-2xl max-w-xs w-full p-6 relative animate-fade-in" style="font-family: 'Mali', 'Sarabun', sans-serif;">
            <button id="modal-close" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-2xl font-bold">&times;</button>
            <div class="mb-2 text-2xl font-bold text-indigo-700 flex items-center gap-2" id="modal-subject"></div>
            <div class="text-gray-700 text-lg" id="modal-room"></div>
        </div>
    </div>
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: scale(0.95);}
            to   { opacity: 1; transform: scale(1);}
        }
        .animate-fade-in { animation: fade-in 0.2s;}
    </style>
    
    <script>
        // Dark mode toggle functionality
        const themeToggle = document.getElementById('theme-toggle');
        let darkMode = false;
        
        themeToggle.addEventListener('click', () => {
            darkMode = !darkMode;
            const body = document.body;
            
            if (darkMode) {
                body.classList.add('bg-gray-900');
                body.classList.remove('bg-gray-100');
                themeToggle.innerHTML = '☀️ โหมดสว่าง';
                
                // Update table styles for dark mode
                document.querySelectorAll('table th').forEach(th => {
                    th.classList.add('text-gray-200');
                    th.classList.remove('text-indigo-800');
                });
                
                document.querySelectorAll('.time-cell').forEach(cell => {
                    cell.classList.add('text-gray-300');
                    cell.classList.remove('text-gray-700');
                });
                
                document.querySelector('.bg-white').classList.add('bg-gray-800');
                document.querySelector('.bg-white').classList.remove('bg-white');
                
            } else {
                body.classList.remove('bg-gray-900');
                body.classList.add('bg-gray-100');
                themeToggle.innerHTML = '🌙 โหมดมืด';
                
                // Update table styles for light mode
                document.querySelectorAll('table th').forEach(th => {
                    th.classList.remove('text-gray-200');
                    th.classList.add('text-indigo-800');
                });
                
                document.querySelectorAll('.time-cell').forEach(cell => {
                    cell.classList.remove('text-gray-300');
                    cell.classList.add('text-gray-700');
                });
                
                document.querySelector('.bg-gray-800').classList.remove('bg-gray-800');
                document.querySelector('.schedule-container > div:first-child').classList.add('bg-white');
            }
        });
        
        // Print functionality
        document.getElementById('print-btn').addEventListener('click', () => {
            window.print();
        });
        
        // Modal functionality
        const modal = document.getElementById('schedule-modal');
        const modalSubject = document.getElementById('modal-subject');
        const modalRoom = document.getElementById('modal-room');
        const modalClose = document.getElementById('modal-close');

        document.querySelectorAll('.schedule-cell').forEach(cell => {
            cell.addEventListener('click', () => {
                // หาชื่อวิชาและห้อง
                const subjectDiv = cell.querySelector('div.font-bold, div.font-medium');
                const roomDiv = cell.querySelector('div.text-sm');
                modalSubject.innerHTML = subjectDiv ? subjectDiv.innerHTML : '';
                modalRoom.innerHTML = roomDiv ? roomDiv.innerHTML : '';
                modal.classList.remove('hidden');
            });
        });

        modalClose.addEventListener('click', () => {
            modal.classList.add('hidden');
        });

        // ปิด modal เมื่อคลิกนอกกล่อง
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    </script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'93b6e54f57aa7997',t:'MTc0NjUxNzkyMS4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>