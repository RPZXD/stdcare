<h6 class="mb-4 font-semibold text-lg">สถิติการเยี่ยมบ้านนักเรียน (แบ่งตามระดับชั้น) 🏠</h6>
<div id="homevisit-report" class="overflow-x-auto">
    <div class="flex justify-center items-center py-8">
        <span class="text-gray-400 animate-spin mr-2">⏳</span>
        <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
    </div>
</div>

<!-- Modal รายห้อง (เดิม) -->
<div id="modal-room-detail" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl md:w-4/5 p-6 relative">
        <button id="close-modal-btn" type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-2xl z-50">&times;</button>
        <h4 class="text-lg font-bold mb-4">รายละเอียดรายห้อง <span id="modal-class-label"></span> 🏫</h4>
        <div id="modal-room-content" class="max-h-[70vh] overflow-y-auto">
            <div class="flex justify-center items-center py-8">
                <span class="text-gray-400 animate-spin mr-2">⏳</span>
                <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal รายชื่อนักเรียนในห้อง -->
<div id="modal-student-list" class="fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-6xl md:w-3/5 p-6 relative">
        <button id="close-student-modal-btn" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
        <h4 class="text-lg font-bold mb-4">การเยี่ยมบ้านนักเรียนของ ชั้น <span id="modal-room-label"></span></h4>
        <div id="modal-student-content" class="max-h-[70vh] overflow-y-auto">
            <div class="flex justify-center items-center py-8">
                <span class="text-gray-400 animate-spin mr-2">⏳</span>
                <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/api_report_homevisit.php')
        .then(res => res.json())
        .then(res => {
            if (!res.success) throw new Error('ไม่สามารถโหลดข้อมูลได้');
            const data = res.data;
            let html = `
            <table class="table-auto w-full border border-gray-200 shadow">
                <thead>
                    <tr class="bg-blue-50">
                        <th class="px-4 py-2 border text-blue-700 text-lg text-center align-middle" rowspan="2">🏫 ระดับชั้น</th>
                        <th class="px-4 py-2 border text-green-700 text-lg text-center" colspan="2">✅ ภาคเรียนที่ 1<br><span class="text-xs text-gray-500">(เป้าหมาย 100%)</span></th>
                        <th class="px-4 py-2 border text-green-700 text-lg text-center" colspan="2">✅ ภาคเรียนที่ 2<br><span class="text-xs text-gray-500">(เป้าหมาย 25%)</span></th>
                        <th class="px-4 py-2 border text-center text-gray-700 text-lg  align-middle" rowspan="2">👥 นักเรียนทั้งหมด</th>
                        <th class="px-4 py-2 border text-center text-blue-700" rowspan="2">👁️ ดูรายชื่อ</th>
                        <th class="px-4 py-2 border text-center text-yellow-700" rowspan="2">📊 ดูสรุป</th>
                    </tr>
                    <tr class="bg-blue-50">
                        <th class="px-4 py-2 border text-center text-green-700 text-lg text-center">จำนวน</th>
                        <th class="px-4 py-2 border text-center text-pink-700 text-lg text-center">%</th>
                        <th class="px-4 py-2 border text-center text-green-700 text-lg text-center">จำนวน</th>
                        <th class="px-4 py-2 border text-center text-pink-700 text-lg text-center">%</th>
                    </tr>
                </thead>
                <tbody>
            `;
            // ตรวจสอบว่ามีข้อมูลครบหรือไม่ ถ้าไม่มีให้เติม 0 หรือ "-"
            const levels = [
                { label: 'ม.1', major: 1 },
                { label: 'ม.2', major: 2 },
                { label: 'ม.3', major: 3 },
                { label: 'ม.4', major: 4 },
                { label: 'ม.5', major: 5 },
                { label: 'ม.6', major: 6 }
            ];
            levels.forEach(level => {
                const row = data.find(r => r.class === level.label) || {
                    class: level.label,
                    major: level.major,
                    visited_term1: 0,
                    percent_term1: 0,
                    visited_term2: 0,
                    percent_term2: 0,
                    total: 0
                };
                html += `
                    <tr class="hover:bg-blue-50 transition">
                        <td class="px-4 py-2 border font-semibold">${row.class}</td>
                        <td class="px-4 py-2 border text-center text-green-600 font-bold">${row.visited_term1 ?? 0}</td>
                        <td class="px-4 py-2 border text-center text-pink-600 font-bold">${row.percent_term1 ?? 0}%</td>
                        <td class="px-4 py-2 border text-center text-green-600 font-bold">${row.visited_term2 ?? 0}</td>
                        <td class="px-4 py-2 border text-center text-pink-600 font-bold">${row.percent_term2 ?? 0}%</td>
                        <td class="px-4 py-2 border text-center">${row.total ?? 0}</td>
                        <td class="px-4 py-2 border text-center">
                            <button class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded shadow text-sm view-room-btn" data-major="${row.major}" data-label="${row.class}">ดูเพิ่มเติม</button>
                        </td>
                        <td class="px-4 py-2 border text-center">
                            <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded shadow text-xs view-summary-btn" data-major="${row.major}" data-room="all" data-label="${row.class}">ดูสรุปรวมทั้งระดับชั้น</button>
                        </td>
                    </tr>
                `;
            });
            html += `</tbody></table>`;
            document.getElementById('homevisit-report').innerHTML = html;

            // Attach event listeners for "ดูเพิ่มเติม"
            document.querySelectorAll('.view-room-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const major = this.getAttribute('data-major');
                    const label = this.getAttribute('data-label');
                    showRoomModal(major, label);
                });
            });
            // Attach event listeners for "ดูสรุป" (ระดับชั้น)
            document.querySelectorAll('.view-summary-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const major = this.getAttribute('data-major');
                    const room = this.getAttribute('data-room');
                    const label = this.getAttribute('data-label');
                    showSummaryModal(major, room, label);
                });
            });
        })
        .catch(err => {
            document.getElementById('homevisit-report').innerHTML = `<div class="text-red-500 text-center py-8">❌ ${err.message}</div>`;
        });

    // Modal logic
    function showRoomModal(major, label) {
        document.getElementById('modal-class-label').textContent = label;
        document.getElementById('modal-room-detail').classList.remove('hidden');
        document.getElementById('modal-room-content').innerHTML = `
            <div class="flex justify-center items-center py-8">
                <span class="text-gray-400 animate-spin mr-2">⏳</span>
                <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
            </div>
        `;
        fetch('api/api_report_homevisit_room.php?major=' + encodeURIComponent(major))
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error('ไม่สามารถโหลดข้อมูลรายห้องได้');
                const data = res.data;
                let html = `
                <table class="table-auto w-full border border-gray-200 shadow">
                    <thead>
                        <tr class="bg-indigo-50">
                            <th class="px-4 py-2 border  text-indigo-700">🚪 ห้อง</th>
                            <th class="px-4 py-2 border  text-green-700">✅ ภาคเรียนที่ 1<br><span class="text-xs text-gray-500">(เป้าหมาย 100%)</span></th>
                            <th class="px-4 py-2 border  text-pink-700">📈 ภาคเรียนที่ 1 (%)</th>
                            <th class="px-4 py-2 border  text-green-700">✅ ภาคเรียนที่ 2<br><span class="text-xs text-gray-500">(เป้าหมาย 25%)</span></th>
                            <th class="px-4 py-2 border  text-pink-700">📈 ภาคเรียนที่ 2 (%)</th>
                            <th class="px-4 py-2 border  text-gray-700">👥 นักเรียนทั้งหมด</th>
                            <th class="px-4 py-2 border  text-blue-700">👁️ ดูรายชื่อ</th>
                            <th class="px-4 py-2 border  text-yellow-700">📊 ดูสรุป</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach(row => {
                    html += `
                        <tr>
                            <td class="px-4 py-2 border text-center font-semibold">${row.room}</td>
                            <td class="px-4 py-2 border text-center text-green-600 font-bold">${row.visited_term1}</td>
                            <td class="px-4 py-2 border text-center text-pink-600 font-bold">${row.percent_term1}%</td>
                            <td class="px-4 py-2 border text-center text-green-600 font-bold">${row.visited_term2}</td>
                            <td class="px-4 py-2 border text-center text-pink-600 font-bold">${row.percent_term2}%</td>
                            <td class="px-4 py-2 border text-center">${row.total}</td>
                            <td class="px-4 py-2 border text-center">
                                <button class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded shadow text-xs  view-student-btn" data-major="${major}" data-room="${row.room}" data-label="${label}/${row.room}">ดูรายชื่อ</button>
                            </td>
                            <td class="px-4 py-2 border text-center">
                                <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-2 py-1 rounded shadow text-xs  view-summary-btn" data-major="${major}" data-room="${row.room}" data-label="${label}/${row.room}">ดูสรุป</button>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                document.getElementById('modal-room-content').innerHTML = html;

                // Attach event listeners for "ดูรายชื่อ"
                document.querySelectorAll('.view-student-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const major = this.getAttribute('data-major');
                        const room = this.getAttribute('data-room');
                        const label = this.getAttribute('data-label');
                        showStudentListModal(major, room, label);
                    });
                });
                // Add print button for student list modal (if not exists)
                if (!document.getElementById('print-student-btn')) {
                    const btn = document.createElement('button');
                    btn.id = 'print-student-btn';
                    btn.textContent = '🖨️ พิมพ์รายงาน';
                    btn.className = 'bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3 ml-2';
                    btn.onclick = function() {
                        // Print student table with header
                        printTable('modal-student-content', [], 'การเยี่ยมบ้านนักเรียนของ ชั้น ' + document.getElementById('modal-room-label').textContent);
                    };
                    document.getElementById('modal-student-content').insertAdjacentElement('beforebegin', btn);
                }
            })
            .catch(err => {
                document.getElementById('modal-room-content').innerHTML = `<div class="text-red-500 text-center py-8">❌ ${err.message}</div>`;
            });
    }

    // Modal รายชื่อนักเรียน
    function showStudentListModal(major, room, label) {
        document.getElementById('modal-room-label').textContent = label;
        document.getElementById('modal-student-list').classList.remove('hidden');
        document.getElementById('modal-student-content').innerHTML = `
            <div class="flex justify-center items-center py-8">
                <span class="text-gray-400 animate-spin mr-2">⏳</span>
                <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
            </div>
        `;
        fetch('api/api_report_homevisit_students.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
            .then(res => res.json())
            .then(res => {
                if (!res.success) throw new Error('ไม่สามารถโหลดรายชื่อนักเรียนได้');
                const data = res.data;
                let html = `
                <table class="table-auto w-full border border-gray-200 shadow">
                    <thead>
                        <tr class="bg-blue-100">
                            <th class="px-4 py-2 border">เลขที่</th>
                            <th class="px-4 py-2 border">ชื่อ-สกุล</th>
                            <th class="px-4 py-2 border">เยี่ยมบ้าน ภาคเรียนที่ 1</th>
                            <th class="px-4 py-2 border">เยี่ยมบ้าน ภาคเรียนที่ 2</th>
                        </tr>
                    </thead>
                    <tbody>
                `;
                data.forEach(row => {
                    html += `
                        <tr>
                            <td class="px-4 py-2 border text-center">${row.Stu_no}</td>
                            <td class="px-4 py-2 border text-left">${row.FullName}</td>
                            <td class="px-4 py-2 border text-center">${row.visit_status1 == 1 ? '✅' : '❌'}</td>
                            <td class="px-4 py-2 border text-center">${row.visit_status2 == 1 ? '✅' : '❌'}</td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                document.getElementById('modal-student-content').innerHTML = html;
            })
            .catch(err => {
                document.getElementById('modal-student-content').innerHTML = `<div class="text-red-500 text-center py-8">❌ ${err.message}</div>`;
            });
    }

    // ปิด modal รายชื่อนักเรียน
    document.getElementById('close-student-modal-btn').onclick = function() {
        document.getElementById('modal-student-list').classList.add('hidden');
    };
    // ปิด modal รายห้อง
    document.getElementById('close-modal-btn').onclick = function() {
        document.getElementById('modal-room-detail').classList.add('hidden');
    };
    document.getElementById('modal-room-detail').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
    document.getElementById('modal-student-list').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });

    // Add print button for main table
    const printMainBtn = document.createElement('button');
    printMainBtn.textContent = '🖨️ พิมพ์ตารางหลัก';
    printMainBtn.className = 'bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3 ml-2';
    printMainBtn.onclick = function() {
        // Hide only the header columns for "ดูเพิ่มเติม" และ "ดูสรุป" AND the last 2 columns in tbody
        printTable('homevisit-report', [], 'สถิติการเยี่ยมบ้านนักเรียน (แบ่งตามระดับชั้น)', true);
    };
    document.getElementById('homevisit-report').insertAdjacentElement('beforebegin', printMainBtn);

    // Print button for modal-room-detail
    function addPrintBtnToModalRoom() {
        if (!document.getElementById('print-room-btn')) {
            const btn = document.createElement('button');
            btn.id = 'print-room-btn';
            btn.textContent = '🖨️ พิมพ์ตารางรายห้อง';
            btn.className = 'bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3 ml-2';
            btn.onclick = function() {
                // Hide last 2 columns in tbody for room print
                printTable('modal-room-content', [6, 7], document.getElementById('modal-class-label').textContent + ' - รายละเอียดรายห้อง', true);
            };
            document.getElementById('modal-room-content').insertAdjacentElement('beforebegin', btn);
        }
    }

    // Print button for modal-summary
    function addPrintBtnToModalSummary() {
        if (!document.getElementById('print-summary-btn')) {
            const btn = document.createElement('button');
            btn.id = 'print-summary-btn';
            btn.textContent = '🖨️ พิมพ์ตารางสรุป';
            btn.className = 'bg-green-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 mb-3 ml-2';
            btn.onclick = function() {
                printTable('modal-summary-content', [], 'สรุปคำตอบ ' + document.getElementById('modal-summary-label').textContent);
            };
            document.getElementById('modal-summary-content').insertAdjacentElement('beforebegin', btn);
        }
    }

    // Print function: containerId = element id, hideCols = array of column indexes to hide (0-based or negative), title = header, hideLast2TbodyCols = true/false
    function printTable(containerId, hideCols = [], title = '', hideLast2TbodyCols = false) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const printWindow = window.open('', '', 'width=900,height=700');
        let style = `
            <style>
                body { font-family: 'Sarabun', sans-serif; background: #f9fafb; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: center; }
                th { background: #6366f1; color: #fff; }
                tr:nth-child(even) { background: #f1f5f9; }
                .font-semibold { font-weight: 600; }
                .text-green-600 { color: #16a34a; }
                .text-pink-600 { color: #db2777; }
                .hover\\:bg-blue-50 { background: #eff6ff !important; }
                .bg-indigo-500 { background: #6366f1 !important; color: #fff !important; }
                .bg-blue-50 { background: #eff6ff !important; }
                .rounded-lg { border-radius: 0.5rem; }
                .shadow, .shadow-md, .shadow-lg { box-shadow: 0 1px 3px 0 #0001, 0 1px 2px 0 #0001; }
                .mb-4 { margin-bottom: 1rem; }
                .mb-6 { margin-bottom: 1.5rem; }
                .max-h-\\[70vh\\] { max-height: 70vh; }
                .overflow-y-auto { overflow-y: auto; }
                .print-hide { display: none !important; }
            </style>
        `;
        // Clone the table and hide columns if needed
        let html = '';
        const table = container.querySelector('table');
        if (table) {
            const tableClone = table.cloneNode(true);
            // Hide only the header columns for "ดูรายชื่อ", "ดูสรุป"
            if (tableClone.tHead && tableClone.tHead.rows.length > 0) {
                const ths = tableClone.tHead.rows[0].children;
                for (let i = 0; i < ths.length; i++) {
                    const text = ths[i].textContent.trim();
                    if (text.includes('ดูรายชื่อ') || text.includes('ดูสรุป')) {
                        ths[i].style.display = 'none';
                    }
                }
            }
            // Hide last 2 columns in tbody if requested
            if (hideLast2TbodyCols) {
                const tbodyRows = tableClone.querySelectorAll('tbody tr');
                tbodyRows.forEach(row => {
                    const len = row.children.length;
                    if (len > 2) {
                        row.children[len - 1].style.display = 'none';
                        row.children[len - 2].style.display = 'none';
                    }
                });
            }
            html = tableClone.outerHTML;
        } else {
            html = container.innerHTML;
        }
        printWindow.document.write(`<html><head><title>Print Table</title>${style}</head><body>
            ${title ? `<h2 style="text-align:center;margin-bottom:1em;">${title}</h2>` : ''}
            ${html}
        </body></html>`);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => printWindow.print(), 300);
    }

    // --- Patch print button into modals after content loaded ---
    // For modal-room-detail
    const origRoomContentSetter = document.getElementById('modal-room-content').innerHTML;
    const observerRoom = new MutationObserver(() => addPrintBtnToModalRoom());
    observerRoom.observe(document.getElementById('modal-room-content'), { childList: true });

    // For modal-summary
    const origSummaryContentSetter = document.getElementById('modal-summary-content').innerHTML;
    const observerSummary = new MutationObserver(() => addPrintBtnToModalSummary());
    observerSummary.observe(document.getElementById('modal-summary-content'), { childList: true });
});

// Modal สรุปคำตอบและ chart
if (!document.getElementById('modal-summary')) {
    const summaryModal = document.createElement('div');
    summaryModal.id = 'modal-summary';
    summaryModal.className = 'fixed inset-0 z-50 hidden bg-black bg-opacity-40 flex items-center justify-center';
    summaryModal.innerHTML = `
        <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl md:w-4/5 p-6 relative">
            <button id="close-summary-modal-btn" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 text-2xl">&times;</button>
            <h4 class="text-lg font-bold mb-4">สรุปคำตอบ ห้อง <span id="modal-summary-label"></span></h4>
            <div id="modal-summary-content" class="max-h-[70vh] overflow-y-auto">
                <div class="flex justify-center items-center py-8">
                    <span class="text-gray-400 animate-spin mr-2">⏳</span>
                    <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(summaryModal);
}

function showSummaryModal(major, room, label) {
    document.getElementById('modal-summary-label').textContent = label;
    document.getElementById('modal-summary').classList.remove('hidden');
    document.getElementById('modal-summary-content').innerHTML = `
        <div class="flex justify-center items-center py-8">
            <span class="text-gray-400 animate-spin mr-2">⏳</span>
            <span class="text-gray-500">กำลังโหลดข้อมูล...</span>
        </div>
    `;
    fetch('api/api_report_homevisit_summary.php?major=' + encodeURIComponent(major) + '&room=' + encodeURIComponent(room))
        .then(res => res.json())
        .then(res => {
            if (!res.success || !Array.isArray(res.data) || res.data.length === 0) {
                document.getElementById('modal-summary-content').innerHTML = `<div class="text-gray-500 text-center py-8">ไม่พบข้อมูลสรุปสำหรับห้องนี้</div>`;
                return;
            }
            const data = res.data;
            let html = '';
            // สร้างตารางสรุปแบบ merge column
            html += `<div class="overflow-x-auto mb-6"><table class="table-auto w-full border border-gray-200 shadow mb-4">
                <thead class="bg-indigo-500 text-white">
                    <tr>
                        <th class="border px-4 py-2 text-center">หัวข้อ</th>
                        <th class="border px-4 py-2 text-center">คำตอบ</th>
                        <th class="border px-4 py-2 text-center">จำนวน</th>
                        <th class="border px-4 py-2 text-center">ร้อยละ</th>
                    </tr>
                </thead>
                <tbody>`;

            // group by question
            data.forEach(row => {
                const answers = Array.isArray(row.answers) ? row.answers : [];
                if (answers.length === 0) return;
                answers.forEach((ans, idx) => {
                    html += `<tr>`;
                    if (idx === 0) {
                        html += `<td class="border px-4 py-2" rowspan="${answers.length}">${row.question}</td>`;
                    }
                    html += `<td class="border px-4 py-2">${ans.answer}</td>
                        <td class="border px-4 py-2 text-center">${ans.count}</td>
                        <td class="border px-4 py-2 text-center">${ans.percent}%</td>
                    </tr>`;
                });
            });

            html += `</tbody></table></div>`;

            // Chart
            html += `<div id="summary-charts" class="grid grid-cols-1 md:grid-cols-2 gap-4"></div>`;
            document.getElementById('modal-summary-content').innerHTML = html;

            // สร้างกราฟแยกตามหัวข้อ
            data.forEach((row, idx) => {
                const answers = Array.isArray(row.answers) ? row.answers : [];
                if (answers.length === 0) return;
                const chartId = `summary-chart-${idx}`;
                const card = document.createElement('div');
                card.className = 'bg-white border rounded-lg shadow-md p-4';
                card.innerHTML = `<h5 class="text-center font-bold mb-4">${row.question}</h5>
                    <canvas id="${chartId}" style="min-height:120px;max-height:200px;max-width:100%"></canvas>`;
                document.getElementById('summary-charts').appendChild(card);

                // Chart.js
                const ctx = document.getElementById(chartId).getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: answers.map(i => i.answer),
                        datasets: [{
                            data: answers.map(i => i.count),
                            backgroundColor: [
                                'rgba(59,130,246,0.7)','rgba(34,197,94,0.7)','rgba(234,88,12,0.7)','rgba(239,68,68,0.7)',
                                'rgba(139,92,246,0.7)','rgba(16,185,129,0.7)','rgba(249,115,22,0.7)','rgba(236,72,153,0.7)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.raw || 0;
                                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                        return `${label}: ${value} คน (${percentage}%)`;
                                    }
                                }
                            }
                        }
                    }
                });
            });
        })
        .catch(err => {
            document.getElementById('modal-summary-content').innerHTML = `<div class="text-red-500 text-center py-8">❌ ไม่สามารถโหลดข้อมูลสรุปได้<br>${err.message}</div>`;
        });
}

document.getElementById('close-summary-modal-btn').onclick = function() {
    document.getElementById('modal-summary').classList.add('hidden');
};
// Close modal on background click
document.getElementById('modal-summary').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>
