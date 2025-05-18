<div>
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        ⏰ <span class="ml-2">รายงานข้อมูลมาสาย</span>
    </h2>

    <form id="lateForm" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-blue-50 p-4 rounded-lg shadow">
        <div>
            <label class="block text-gray-700 mb-1" for="date_start">📅 วันที่เริ่มต้น</label>
            <input type="date" id="date_start" name="date_start" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div>
            <label class="block text-gray-700 mb-1" for="date_end">📅 วันที่สิ้นสุด</label>
            <input type="date" id="date_end" name="date_end" class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition flex items-center justify-center">
                🔍 <span class="ml-2">ค้นหา</span>
            </button>
        </div>
    </form>
    <div class="mb-4 flex justify-end">
        <button onclick="printLateTable()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow flex items-center">
            🖨️ <span class="ml-2">พิมพ์หน้านี้</span>
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow" id="lateTable">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-center bg-blue-100">#</th>
                    <th class="px-4 py-2 text-left bg-blue-100">🆔 เลขประจำตัว</th>
                    <th class="px-4 py-2 text-center bg-blue-100">👨‍🎓 ชื่อนักเรียน</th>
                    <th class="px-4 py-2 text-center bg-blue-100">🔢 เลขที่</th>
                    <th class="px-4 py-2 text-center bg-blue-100">🏫 ห้อง</th>
                    <th class="px-4 py-2 text-center bg-blue-100">📱 เบอร์ผู้ปกครอง</th>
                    <th class="px-4 py-2 text-center bg-blue-100">⏰ จำนวนครั้งที่มาสาย</th>
                </tr>
            </thead>
            <tbody id="lateTableBody">
                <!-- ข้อมูลจะถูกเติมโดย JS -->
            </tbody>
        </table>
        <div id="lateTableEmpty" class="text-gray-500 text-center py-4 hidden">กรุณาเลือกช่วงวันที่และกดค้นหา</div>
    </div>
    <script>
    // ฟังก์ชันแปลงวันที่ yyyy-mm-dd เป็นภาษาไทยเต็มรูปแบบ
    function thaiDateRange(start, end) {
        const months = [
            "", "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
            "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
        ];
        function toThai(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const day = d.getDate();
            const month = months[d.getMonth() + 1];
            const year = d.getFullYear() + 543;
            return `${day} ${month} พ.ศ. ${year}`;
        }
        return `ตั้งแต่ ${toThai(start)} ถึง ${toThai(end)}`;
    }

    function printLateTable() {
        const start = document.getElementById('date_start').value;
        const end = document.getElementById('date_end').value;
        const table = document.getElementById('lateTable');
        let tableHTML = table.outerHTML;
        let rangeText = '';
        if (start && end) {
            rangeText = `<div style="font-size:1.25rem;font-weight:bold;margin-bottom:1rem;text-align:center;">
                นักเรียนที่มาสายเกิน 3 ครั้ง<br>(${thaiDateRange(start, end)})
            </div>`;
        } else {
            rangeText = `<div style="font-size:1.25rem;font-weight:bold;margin-bottom:1rem;text-align:center;">
                นักเรียนที่มาสายเกิน 3 ครั้ง
            </div>`;
        }
        const style = `
            <style>
                body { font-family: 'TH SarabunPSK', 'Sarabun', sans-serif; }
                table { width: 100%; border-collapse: collapse; margin: 0 auto;}
                th, td { border: 1px solid #ccc; padding: 8px; font-size: 1rem;}
                th { background: #e0e7ff; }
                tr:nth-child(even) { background: #f1f5f9; }
                h2 { text-align: center; }
                /* จัดตำแหน่งคอลัมน์ */
                th, td { text-align: center; }
                th:nth-child(3), td:nth-child(3) { text-align: left !important; }
            </style>
        `;
        const win = window.open('', '', 'width=900,height=700');
        win.document.write(`<html><head><title>พิมพ์รายงานมาสาย</title>${style}</head><body>${rangeText}${tableHTML}</body></html>`);
        win.document.close();
        setTimeout(() => { win.print(); win.close(); }, 500);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('lateForm');
        const tbody = document.getElementById('lateTableBody');
        const emptyMsg = document.getElementById('lateTableEmpty');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            tbody.innerHTML = '';
            emptyMsg.classList.add('hidden');
            const start = document.getElementById('date_start').value;
            const end = document.getElementById('date_end').value;
            if (!start || !end) {
                emptyMsg.textContent = 'กรุณาเลือกวันที่เริ่มต้นและสิ้นสุด';
                emptyMsg.classList.remove('hidden');
                return;
            }
            fetch(`api/fetch_checklate.php?start_date=${start}&end_date=${end}`)
                .then(res => res.json())
                .then(data => {
                    tbody.innerHTML = '';
                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach((row, idx) => {
                            tbody.innerHTML += `
                                <tr class="hover:bg-blue-50">
                                    <td class="px-4 py-2 text-center">${idx + 1}</td>
                                    <td class="px-4 py-2 text-center">${row.Stu_id || '-'}</td>
                                    <td class="px-4 py-2">${row.name || '-'}</td>
                                    <td class="px-4 py-2 text-center">${row.Stu_no || '-'}</td>
                                    <td class="px-4 py-2 text-center">${row.classroom || '-'}</td>
                                    <td class="px-4 py-2 text-center">${row.parent_tel || '-'}</td>
                                    <td class="px-4 py-2 text-center">${row.count_late || '-'}</td>
                                </tr>
                            `;
                        });
                    } else {
                        emptyMsg.textContent = 'ไม่พบข้อมูลมาสายในช่วงวันที่ที่เลือก';
                        emptyMsg.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    emptyMsg.textContent = 'เกิดข้อผิดพลาดในการดึงข้อมูล';
                    emptyMsg.classList.remove('hidden');
                });
        });
    });
    </script>
</div>
