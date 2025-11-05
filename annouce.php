<?php 

require_once('header.php');
require_once('config/Setting.php');
require_once('class/Utils.php');
?>
<body class="hold-transition sidebar-mini layout-fixed light-mode">
<div class="wrapper">

    <?php require_once('wrapper.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">

  <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div>
        </div>
      </div>
    </div>
    <!-- /.content-header -->

    <section class="content">
      <div class="max-w-8xl mx-auto py-12 px-6">
        <!-- Infographic container -->
        <div class="infographic bg-gradient-to-br from-white to-gray-50 rounded-3xl shadow-2xl p-8 border border-gray-200">
          <!-- Header / Hero -->
          <div class="flex flex-col lg:flex-row items-center gap-6 mb-8">
            <div class="flex items-center gap-4">
              <div class="w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 to-red-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg">พช</div>
              <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-800">โรงเรียนพิชัย</h1>
                <p class="text-sm text-gray-600">อำเภอพิชัย จังหวัดอุตรดิตถ์ • สพม. พิษณุโลก-อุตรดิตถ์</p>
              </div>
            </div>
            <div class="ml-auto flex gap-3">
              <button id="btnPrint" class="px-4 py-2 bg-indigo-600 text-white rounded-md shadow hover:bg-indigo-700">🖨️ พิมพ์</button>
              <button id="btnPdf" class="px-4 py-2 bg-green-600 text-white rounded-md shadow hover:bg-green-700">📥 PDF</button>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Timeline / Strategies -->
            <div class="col-span-1">
              <div class="bg-white rounded-2xl p-6 shadow-md border">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">กลยุทธ์ & ขั้นตอน</h3>
                <div class="timeline expanded" id="timelineBlock">
                  <div class="timeline-item">
                    <div class="timeline-badge bg-green-500">1</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">พัฒนาผู้เรียน</div>
                      <div class="text-xs text-gray-600">พัฒนาสมรรถนะด้านเทคโนโลยีและทักษะชีวิต</div>
                    </div>
                  </div>
                  <div class="timeline-item">
                    <div class="timeline-badge bg-blue-500">2</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">เครือข่ายความร่วมมือ</div>
                      <div class="text-xs text-gray-600">สร้างความร่วมมือ ยกระดับมาตรฐาน</div>
                    </div>
                  </div>
                  <div class="timeline-item">
                    <div class="timeline-badge bg-yellow-500">3</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">นวัตกรรมการเรียนรู้</div>
                      <div class="text-xs text-gray-600">ส่งเสริมการจัดการเรียนรู้ด้วยเทคโนโลยี</div>
                    </div>
                  </div>

                  <hr class="my-3">
                  <h4 class="ml-12 text-sm font-medium text-gray-700 mb-2">ขั้นตอนสำคัญ 5 ขั้นตอน</h4>
                  <ol class="ml-18 list-decimal ml-6 text-gray-600 space-y-1">
                    <li class="text-xs">รู้จักนักเรียนเป็นรายบุคคล</li>
                    <li class="text-xs">คัดกรองนักเรียน</li>
                    <li class="text-xs">ส่งเสริมและพัฒนานักเรียน</li>
                    <li class="text-xs">ป้องกันและแก้ไขปัญหา</li>
                    <li class="text-xs">ส่งต่อนักเรียนอย่างมีคุณภาพ</li>
                  </ol>

                  
                </div>
              </div>
            </div>

            <!-- Center: Vision / Mission / Goals big cards -->
            <div class="col-span-1">
              <div class="space-y-6">
                <div class="card-hero bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-2xl p-6 border-l-8 border-yellow-400 shadow-md">
                  <h3 class="text-xl font-bold text-gray-800">วิสัยทัศน์</h3>
                  <p class="mt-2 text-gray-700 italic text-lg">“สถานศึกษาพอเพียง ขับเคลื่อนนวัตกรรมสู่มาตรฐานสากล”</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div class="rounded-xl p-4 bg-white shadow border">
                    <h4 class="font-semibold text-gray-800">พันธกิจ</h4>
                    <ul class="mt-2 text-gray-600 list-decimal ml-6 space-y-1">
                      <li>พัฒนาผู้เรียนให้มีคุณภาพด้านวิชาการและทักษะชีวิต</li>
                      <li>สร้างเครือข่ายความร่วมมือเพื่อยกระดับคุณภาพ</li>
                      <li>ส่งเสริมการจัดการเรียนรู้ด้วยนวัตกรรมและเทคโนโลยี</li>
                    </ul>
                  </div>
                  <div class="rounded-xl p-4 bg-white shadow border">
                    <h4 class="font-semibold text-gray-800">เป้าหมาย</h4>
                    <ul class="mt-2 text-gray-600 list-decimal ml-6 space-y-1">
                      <li>ผู้เรียนมีคุณภาพ และใช้เทคโนโลยีอย่างสร้างสรรค์</li>
                      <li>มีเครือข่ายความร่วมมือที่เข้มแข็ง</li>
                      <li>ครูและบุคลากรพร้อมใช้นวัตกรรม</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right: Values / Identity / Competencies -->
            <div class="col-span-1">
              <div class="space-y-6">
                <div class="rounded-2xl p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 border shadow">
                  <h4 class="font-semibold text-gray-800">อัตลักษณ์</h4>
                  <p class="text-gray-700 mt-2">ลูกพิชัย พอเพียง กล้าหาญ เสียสละ กตัญญู</p>
                </div>

                <div class="rounded-2xl p-4 bg-gradient-to-br from-pink-50 to-pink-100 border shadow">
                  <h4 class="font-semibold text-gray-800">เอกลักษณ์</h4>
                  <p class="text-gray-700 mt-2">ลูกหลานพระยาพิชัย สืบสานความกล้า ตามหลักปรัชญาของเศรษฐกิจพอเพียง</p>
                </div>

                <div class="rounded-2xl p-4 bg-white border shadow">
                  <h4 class="font-semibold text-gray-800">ค่านิยม & วัฒนธรรม</h4>
                  <div class="mt-3 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">กล้าทำสิ่งที่ถูกต้อง</span>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">ยึดหลักพอเพียง</span>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">รับผิดชอบ</span>
                  </div>
                </div>

                <div class="rounded-2xl p-4 bg-white border shadow">
                  <h4 class="font-semibold text-gray-800">สมรรถนะหลัก</h4>
                  <div class="mt-3 space-y-3 text-sm text-gray-700">
                    <div>ระบบการบริหารจัดการที่มีคุณภาพ</div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mt-2">
                      <div class="bg-indigo-500 h-3 rounded-full" style="width:78%"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 text-center text-gray-600">เอกสารอ้างอิง: วิสัยทัศน์และนโยบาย ปี 2569</div>
        </div>
      </div>

      <!-- scripts for PDF/Print remain -->
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
      <script>
      // Print
      document.getElementById('btnPrint').addEventListener('click', ()=> window.print());

      // Export to PDF (capture infographic container only)
      document.getElementById('btnPdf').addEventListener('click', async ()=>{
        const container = document.querySelector('.infographic');
        const origBg = container.style.backgroundColor;
        container.style.backgroundColor = '#ffffff';
        try{
          const canvas = await html2canvas(container, {scale:2, useCORS:true});
          const imgData = canvas.toDataURL('image/jpeg', 0.95);
          const { jsPDF } = window.jspdf;
          const pdf = new jsPDF({orientation:'portrait', unit:'pt', format:'a4'});
          const pageWidth = pdf.internal.pageSize.getWidth();
          const pageHeight = pdf.internal.pageSize.getHeight();
          const imgProps = {width: canvas.width, height: canvas.height};
          const ratio = Math.min(pageWidth / imgProps.width, pageHeight / imgProps.height);
          const imgWidth = imgProps.width * ratio;
          const imgHeight = imgProps.height * ratio;
          pdf.addImage(imgData, 'JPEG', (pageWidth - imgWidth)/2, 20, imgWidth, imgHeight);
          pdf.save('วิสัยทัศน์_infographic_โรงเรียนพิชัย.pdf');
        }catch(err){
          alert('เกิดข้อผิดพลาดขณะสร้าง PDF: ' + err.message);
          console.error(err);
        }finally{ container.style.backgroundColor = origBg; }
      });
      
      // timeline is shown fully by default (no expand/collapse)
      </script>

      <style>
  /* Compact timeline styling */
  .timeline.compact { max-height: 220px; overflow: auto; padding-right: 6px; }
  .timeline.expanded { max-height: none; }
  .timeline.compact::-webkit-scrollbar { width: 8px; }
  .timeline.compact::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 8px; }
  .timeline-item { display:flex; gap:10px; align-items:flex-start; padding:6px 0; }
  .timeline-badge { width:28px; height:28px; border-radius:9999px; color:white; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:600; }
  .timeline-body { flex:1; }
  /* Draw short connector segments between badges instead of one long line */
  .timeline-item { position: relative; }
  .timeline-item:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 12px; /* align under the badge */
    top: 28px;  /* start a bit below the badge center */
    width: 2px;
    height: 18px; /* short segment */
    background: linear-gradient(to bottom, rgba(0,0,0,0.08), rgba(0,0,0,0.04));
    border-radius: 2px;
  }
  .infographic .card-hero { background-image: linear-gradient(120deg, rgba(253,224,71,0.08), rgba(255,244,229,0.08)); }
  @media print { .infographic { box-shadow:none !important; border: none !important; } }
      </style>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
    <?php require_once('footer.php');?>
</div>
<!-- ./wrapper -->

<?php require_once('script.php');?>
</body>
</html>

