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
          <!-- Header / Hero with animations -->
          <div class="flex flex-col lg:flex-row items-center gap-6 mb-8 hero-section">
            <div class="flex items-center gap-4 animate-slideInLeft">
              <div class="logo-circle w-20 h-20 rounded-full bg-gradient-to-br from-yellow-400 via-orange-500 to-red-500 flex items-center justify-center text-white text-3xl font-bold shadow-lg animate-float">
                <span class="animate-pulse-slow"><img src="dist/img/logo-phicha.png" alt=""></span>
              </div>
              <div>
                <h1 class="text-2xl lg:text-3xl font-extrabold text-gray-800 bg-clip-text text-transparent bg-gradient-to-r from-yellow-600 to-red-600">โรงเรียนพิชัย</h1>
                <p class="text-sm text-gray-600 animate-fadeIn">อำเภอพิชัย จังหวัดอุตรดิตถ์ • สพม. พิษณุโลก-อุตรดิตถ์</p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Timeline / Strategies with hover effects -->
            <div class="col-span-1 animate-slideInUp">
              <div class="bg-white rounded-2xl p-6 shadow-md border hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                  <span class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-xs">📋</span>
                  กลยุทธ์ & ขั้นตอน
                </h3>
                <div class="timeline expanded" id="timelineBlock">
                  <div class="timeline-item hover-lift">
                    <div class="timeline-badge bg-gradient-to-br from-green-400 to-green-600 shadow-lg">1</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">พัฒนาผู้เรียน</div>
                      <div class="text-xs text-gray-600">พัฒนาสมรรถนะด้านเทคโนโลยีและทักษะชีวิต</div>
                    </div>
                  </div>
                  <div class="timeline-item hover-lift">
                    <div class="timeline-badge bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg">2</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">เครือข่ายความร่วมมือ</div>
                      <div class="text-xs text-gray-600">สร้างความร่วมมือ ยกระดับมาตรฐาน</div>
                    </div>
                  </div>
                  <div class="timeline-item hover-lift">
                    <div class="timeline-badge bg-gradient-to-br from-yellow-400 to-yellow-600 shadow-lg">3</div>
                    <div class="timeline-body">
                      <div class="font-medium text-sm">นวัตกรรมการเรียนรู้</div>
                      <div class="text-xs text-gray-600">ส่งเสริมการจัดการเรียนรู้ด้วยเทคโนโลยี</div>
                    </div>
                  </div>

                  <hr class="my-3 border-dashed">
                  <h4 class="ml-12 text-sm font-medium text-gray-700 mb-2 flex items-center gap-2">
                    <span class="text-lg">✨</span>
                    ขั้นตอนสำคัญ 5 ขั้นตอน
                  </h4>
                  <ol class="list-decimal ml-16 text-gray-600 space-y-1">
                    <li class="text-xs hover:text-indigo-600 transition-colors cursor-pointer">รู้จักนักเรียนเป็นรายบุคคล</li>
                    <li class="text-xs hover:text-indigo-600 transition-colors cursor-pointer">คัดกรองนักเรียน</li>
                    <li class="text-xs hover:text-indigo-600 transition-colors cursor-pointer">ส่งเสริมและพัฒนานักเรียน</li>
                    <li class="text-xs hover:text-indigo-600 transition-colors cursor-pointer">ป้องกันและแก้ไขปัญหา</li>
                    <li class="text-xs hover:text-indigo-600 transition-colors cursor-pointer">ส่งต่อนักเรียนอย่างมีคุณภาพ</li>
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

            <!-- Right: Values / Identity / Competencies with enhanced effects -->
            <div class="col-span-1 animate-slideInUp animation-delay-200">
              <div class="space-y-6">
                <div class="rounded-2xl p-4 bg-gradient-to-br from-indigo-50 via-indigo-100 to-purple-50 border shadow hover:shadow-2xl transform hover:scale-105 transition-all duration-500 hover:border-indigo-300">
                  <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="text-xl">🎯</span>
                    อัตลักษณ์
                  </h4>
                  <p class="text-gray-700 mt-2 font-medium">ลูกพิชัย พอเพียง กล้าหาญ เสียสละ กตัญญู</p>
                </div>

                <div class="rounded-2xl p-4 bg-gradient-to-br from-pink-50 via-pink-100 to-red-50 border shadow hover:shadow-2xl transform hover:scale-105 transition-all duration-500 hover:border-pink-300">
                  <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="text-xl">⭐</span>
                    เอกลักษณ์
                  </h4>
                  <p class="text-gray-700 mt-2 font-medium">ลูกหลานพระยาพิชัย สืบสานความกล้า ตามหลักปรัชญาของเศรษฐกิจพอเพียง</p>
                </div>

                <div class="rounded-2xl p-4 bg-white border shadow hover:shadow-2xl transform hover:scale-105 transition-all duration-500 hover:border-yellow-300">
                  <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="text-xl">💎</span>
                    ค่านิยม & วัฒนธรรม
                  </h4>
                  <div class="mt-3 flex flex-wrap gap-2">
                    <span class="px-3 py-1 bg-gradient-to-r from-yellow-100 to-yellow-200 text-yellow-800 rounded-full text-sm font-medium shadow-sm">✓ กล้าทำสิ่งที่ถูกต้อง</span>
                    <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-green-200 text-green-800 rounded-full text-sm font-medium shadow-sm">✓ ยึดหลักพอเพียง</span>
                    <span class="px-3 py-1 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 rounded-full text-sm font-medium shadow-sm">✓ รับผิดชอบ</span>
                  </div>
                </div>

                <div class="rounded-2xl p-4 bg-gradient-to-br from-gray-50 to-white border shadow hover:shadow-2xl transform hover:scale-105 transition-all duration-500 hover:border-indigo-300">
                  <h4 class="font-semibold text-gray-800 flex items-center gap-2">
                    <span class="text-xl">📊</span>
                    สมรรถนะหลัก
                  </h4>
                  <div class="mt-3 space-y-3 text-sm text-gray-700">
                    <div class="flex items-center justify-between">
                      <span>ระบบการบริหารจัดการที่มีคุณภาพ</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden mt-2 relative">
                      <div class="bg-indigo-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width:100%"></div>
                      <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white to-transparent opacity-30" style="animation: shimmer 2s infinite;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-8 text-center animate-fadeIn">
            <div class="inline-block bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 px-6 py-3 rounded-full border border-indigo-200 shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
              <p class="text-gray-700 font-medium flex items-center gap-2">
                <span class="text-xl">📄</span>
                <span>เอกสารอ้างอิง: วิสัยทัศน์และนโยบาย ปี 2569</span>
                <span class="text-xl">✨</span>
              </p>
            </div>
          </div>
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
      
      // Timeline is shown fully by default (no expand/collapse)

      // Add interactive particle effect on mouse move
      document.addEventListener('DOMContentLoaded', function() {
        const infographic = document.querySelector('.infographic');
        
        // Create floating particles
        function createParticle(x, y) {
          const particle = document.createElement('div');
          particle.style.position = 'fixed';
          particle.style.left = x + 'px';
          particle.style.top = y + 'px';
          particle.style.width = '4px';
          particle.style.height = '4px';
          particle.style.borderRadius = '50%';
          particle.style.background = 'rgba(99, 102, 241, 0.6)';
          particle.style.pointerEvents = 'none';
          particle.style.zIndex = '9999';
          particle.style.animation = 'float-particles 2s ease-out forwards';
          document.body.appendChild(particle);
          
          setTimeout(() => {
            particle.style.opacity = '0';
            setTimeout(() => particle.remove(), 500);
          }, 1500);
        }

        // Throttled mouse move for particles
        let lastParticleTime = 0;
        infographic.addEventListener('mousemove', function(e) {
          const now = Date.now();
          if (now - lastParticleTime > 100) {
            createParticle(e.clientX, e.clientY);
            lastParticleTime = now;
          }
        });

        // Add click ripple effect to cards
        const cards = document.querySelectorAll('.infographic div[class*="rounded"]');
        cards.forEach(card => {
          card.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.position = 'absolute';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(99, 102, 241, 0.3)';
            ripple.style.transform = 'scale(0)';
            ripple.style.animation = 'ripple 0.6s ease-out';
            ripple.style.pointerEvents = 'none';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
          });
        });

        // Add counter animation for the progress bar
        const progressBar = document.querySelector('.bg-indigo-500');
        if (progressBar) {
          let width = 0;
          const targetWidth = 78;
          const interval = setInterval(() => {
            if (width >= targetWidth) {
              clearInterval(interval);
            } else {
              width++;
              progressBar.style.width = width + '%';
            }
          }, 20);
        }

        // Add emoji bounce on hover
        const emojis = document.querySelectorAll('.infographic span[class*="text-"]');
        emojis.forEach(emoji => {
          if (emoji.textContent.match(/[\u{1F300}-\u{1F9FF}]/u)) {
            emoji.style.display = 'inline-block';
            emoji.style.transition = 'transform 0.3s ease';
            emoji.addEventListener('mouseenter', function() {
              this.style.transform = 'scale(1.3) rotate(10deg)';
            });
            emoji.addEventListener('mouseleave', function() {
              this.style.transform = 'scale(1) rotate(0deg)';
            });
          }
        });

        // Add number counting animation
        const stats = document.querySelectorAll('.infographic div[class*="text-"]');
        stats.forEach(stat => {
          const text = stat.textContent;
          const match = text.match(/\d+/);
          if (match) {
            const finalNumber = parseInt(match[0]);
            let currentNumber = 0;
            const increment = finalNumber / 50;
            const timer = setInterval(() => {
              currentNumber += increment;
              if (currentNumber >= finalNumber) {
                clearInterval(timer);
                currentNumber = finalNumber;
              }
              stat.textContent = text.replace(/\d+/, Math.floor(currentNumber));
            }, 30);
          }
        });

        // Add parallax effect to cards
        document.addEventListener('mousemove', function(e) {
          const cards = document.querySelectorAll('.infographic > div > div');
          const mouseX = e.clientX / window.innerWidth;
          const mouseY = e.clientY / window.innerHeight;
          
          cards.forEach((card, index) => {
            const depth = (index + 1) * 5;
            const moveX = (mouseX - 0.5) * depth;
            const moveY = (mouseY - 0.5) * depth;
            card.style.transform = `translate(${moveX}px, ${moveY}px)`;
          });
        });

        // Add confetti effect on button click
        function createConfetti() {
          const colors = ['#FCD34D', '#F97316', '#EF4444', '#6366F1', '#8B5CF6'];
          for (let i = 0; i < 30; i++) {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.left = Math.random() * 100 + '%';
            confetti.style.top = '-10px';
            confetti.style.width = Math.random() * 10 + 5 + 'px';
            confetti.style.height = confetti.style.width;
            confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            confetti.style.opacity = Math.random();
            confetti.style.zIndex = '99999';
            confetti.style.pointerEvents = 'none';
            confetti.style.animation = `confettiFall ${Math.random() * 3 + 2}s linear forwards`;
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 5000);
          }
        }

        // Add confetti animation keyframe
        const style = document.createElement('style');
        style.textContent = `
          @keyframes confettiFall {
            to {
              transform: translateY(100vh) rotate(360deg);
              opacity: 0;
            }
          }
          @keyframes ripple {
            to {
              transform: scale(4);
              opacity: 0;
            }
          }
        `;
        document.head.appendChild(style);

        // Trigger confetti on PDF button click
        document.getElementById('btnPdf').addEventListener('click', createConfetti);
      });
      </script>

      <style>
  /* Animations and Keyframes */
  @keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-50px); }
    to { opacity: 1; transform: translateX(0); }
  }
  @keyframes slideInRight {
    from { opacity: 0; transform: translateX(50px); }
    to { opacity: 1; transform: translateX(0); }
  }
  @keyframes slideInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
  }
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
  }
  @keyframes pulse-slow {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  @keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
  }
  @keyframes glow {
    0%, 100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); }
    50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8), 0 0 30px rgba(59, 130, 246, 0.6); }
  }

  /* Apply Animations */
  .animate-slideInLeft { animation: slideInLeft 0.8s ease-out; }
  .animate-slideInRight { animation: slideInRight 0.8s ease-out; }
  .animate-slideInUp { animation: slideInUp 0.8s ease-out; }
  .animation-delay-200 { animation-delay: 0.2s; animation-fill-mode: both; }
  .animate-fadeIn { animation: fadeIn 1s ease-in; }
  .animate-float { animation: float 3s ease-in-out infinite; }
  .animate-pulse-slow { animation: pulse-slow 2s ease-in-out infinite; }

  /* Logo Circle with gradient animation */
  .logo-circle {
    animation: float 3s ease-in-out infinite;
    background: linear-gradient(135deg, #FCD34D 0%, #F97316 50%, #EF4444 100%);
    box-shadow: 0 10px 25px rgba(251, 146, 60, 0.4);
  }
  .logo-circle:hover {
    animation: glow 1.5s ease-in-out infinite;
    transform: scale(1.1) rotate(5deg);
    transition: all 0.3s ease;
  }

  /* Button Actions with enhanced effects */
  .btn-action {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
  }
  .btn-action::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
  }
  .btn-action:hover::before {
    width: 300px;
    height: 300px;
  }
  .btn-action:active {
    transform: scale(0.95);
  }

  /* Sparkle Background Effect */
  .sparkle-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(90deg, 
      transparent 0%,
      rgba(255, 255, 255, 0.4) 50%,
      transparent 100%
    );
    animation: shimmer 3s infinite;
  }

  /* Hover Lift Effect */
  .hover-lift {
    transition: all 0.3s ease;
  }
  .hover-lift:hover {
    transform: translateY(-5px);
    background: rgba(99, 102, 241, 0.05);
    border-radius: 8px;
    padding: 8px;
    margin: -2px;
  }

  /* Enhanced Cards with 3D effect */
  .infographic > div > div > div[class*="rounded"] {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  }

  /* Progress bar animation */
  .bg-indigo-500 {
    background: linear-gradient(90deg, #6366F1, #8B5CF6, #6366F1);
    background-size: 200% 100%;
    animation: shimmer 2s ease infinite;
  }

  /* Badge Glow Effects */
  .timeline-badge {
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  .timeline-item:hover .timeline-badge {
    transform: scale(1.15) rotate(5deg);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
  }

  /* Tag Pills with hover effects */
  .infographic span[class*="rounded-full"] {
    transition: all 0.3s ease;
    cursor: pointer;
  }
  .infographic span[class*="rounded-full"]:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
  }

  /* Gradient Text */
  .hero-section h1 {
    background: linear-gradient(135deg, #D97706 0%, #DC2626 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

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
  .infographic .card-hero { 
    background-image: linear-gradient(120deg, rgba(253,224,71,0.08), rgba(255,244,229,0.08)); 
  }
  
  /* Floating particles effect */
  @keyframes float-particles {
    0%, 100% { transform: translateY(0) translateX(0); }
    25% { transform: translateY(-10px) translateX(5px); }
    75% { transform: translateY(10px) translateX(-5px); }
  }
  
  /* Add subtle grid background */
  .infographic {
    background-image: 
      linear-gradient(rgba(99, 102, 241, 0.03) 1px, transparent 1px),
      linear-gradient(90deg, rgba(99, 102, 241, 0.03) 1px, transparent 1px);
    background-size: 20px 20px;
  }

  /* List hover effects */
  ul li {
    transition: all 0.2s ease;
    cursor: pointer;
    position: relative;
    padding-left: 4px;
  }
  ul li:hover::before {
    content: '→';
    position: absolute;
    left: -15px;
    color: #6366F1;
    font-weight: bold;
  }

  /* Card shine effect on hover */
  .infographic div[class*="col-span"] > div > div {
    position: relative;
    overflow: hidden;
  }
  .infographic div[class*="col-span"] > div > div::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    bottom: -50%;
    left: -50%;
    background: linear-gradient(to bottom, rgba(229, 231, 235, 0), rgba(255, 255, 255, 0.3) 50%, rgba(229, 231, 235, 0));
    transform: rotateZ(60deg) translate(-5em, 7.5em);
    transition: transform 0.5s ease;
  }
  .infographic div[class*="col-span"] > div > div:hover::after {
    transform: rotateZ(60deg) translate(5em, -7.5em);
  }

  @media print { 
    .infographic { box-shadow:none !important; border: none !important; }
    .btn-action { display: none !important; }
    .animate-slideInLeft, .animate-slideInRight, .animate-slideInUp { animation: none !important; }
  }
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

