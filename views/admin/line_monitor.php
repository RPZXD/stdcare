<?php
/**
 * View: LINE Webhook & Notify Monitor
 * Modern Premium Dashboard with Bento Grid Layouts, macOS Developer Console, 
 * Visual Parent Directory, and Interactive Modals.
 */
ob_start();
$activeTab = $_GET['tab'] ?? 'activity';
if (!in_array($activeTab, ['activity', 'tokens', 'parents'])) {
    $activeTab = 'activity';
}

// Resolve Webhook URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$dir = dirname($_SERVER['SCRIPT_NAME']);
$dir = ($dir === '\\' || $dir === '/') ? '' : $dir;
$parent_dir = dirname($dir);
$parent_dir = ($parent_dir === '\\' || $parent_dir === '/') ? '' : $parent_dir;
$webhook_url_display = $protocol . '://' . $host . $parent_dir . '/line_webhook.php';
?>

<!-- Tailwind custom styles inline to ensure premium feel -->
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.45);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    .dark .glass-card {
        background: rgba(15, 23, 42, 0.45);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .btn-gradient-emerald {
        background: linear-gradient(135deg, #10b981, #059669);
        transition: all 0.3s ease;
    }
    .btn-gradient-emerald:hover {
        background: linear-gradient(135deg, #34d399, #059669);
        box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
    }
</style>

<div class="animate-fadeIn" 
     x-data="{ 
         activeTab: '<?= $activeTab ?>', 
         showAddModal: false, 
         showEditModal: false, 
         showTestModal: false, 
         editTokenData: { id: '', line_name: '', line_class: '', line_room: '', token: '' }, 
         testTokenData: { id: '', name: '', message: 'ทดสอบแจ้งเตือนจากระบบ StdCare 🔔' } 
     }">
    
    <!-- Flash Messages (SweetAlert) -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <script>
            Swal.fire({
                title: '<?= htmlspecialchars($_SESSION['flash_message']) ?>',
                icon: '<?= $_SESSION['flash_type'] ?? 'success' ?>',
                confirmButtonColor: '#10b981',
                timer: 3000
            });
        </script>
        <?php 
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        ?>
    <?php endif; ?>

    <!-- Page Header -->
    <?php 
    $headerData = [
        'title' => 'LINE <span class="text-emerald-500 italic font-black">Webhook & Notify</span>',
        'subtitle' => 'ศูนย์บริการจัดการและมอนิเตอร์การรับส่งข้อมูลผ่านไลน์',
        'icon' => 'fa-desktop',
        'color' => 'emerald'
    ];
    include __DIR__ . '/../components/ui_header.php'; 
    ?>

    <!-- Database Error Alert -->
    <?php if (!empty($db_error)): ?>
        <div class="mb-8 p-5 bg-rose-500/10 border border-rose-500/30 text-rose-700 dark:text-rose-400 rounded-[2rem] flex items-start gap-4 animate-pulse">
            <div class="w-10 h-10 bg-rose-500 text-white rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                <i class="fas fa-exclamation-triangle text-lg"></i>
            </div>
            <div>
                <h5 class="font-black text-sm mb-1 text-rose-800 dark:text-rose-300">พบข้อผิดพลาดของระบบฐานข้อมูล (Database Sync Required)</h5>
                <p class="text-xs opacity-90 leading-relaxed"><?= htmlspecialchars($db_error) ?></p>
                <p class="text-[10px] opacity-75 mt-1.5 font-bold">กรุณาแจ้งผู้ดูแลระบบให้รันการสร้างตารางและคอลัมน์ใหม่ หรือตรวจสอบสิทธิ์การเขียน/อ่านฐานข้อมูลบนโปรดักชัน</p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Stats and Webhook Info Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Webhook URL Display (Sleek Dark Theme Code look) -->
        <div class="bg-slate-900 text-white rounded-[2rem] p-6 shadow-2xl lg:col-span-2 flex flex-col justify-between border border-slate-800 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 text-slate-800 text-[10rem] font-bold opacity-20 pointer-events-none select-none">API</div>
            <div class="relative z-10">
                <div class="flex items-center gap-2 text-emerald-400 font-black text-[10px] uppercase tracking-widest mb-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    LINE WEBHOOK ENDPOINT URL
                </div>
                <p class="text-slate-400 text-xs mb-4 leading-relaxed max-w-md">
                    ใช้สำหรับนำไปกรอกในหน้าคอนโซลนักพัฒนาของไลน์เพื่อรับข้อความและการเชิญเข้ากลุ่มห้องเรียน
                </p>
            </div>
            <div class="flex items-center gap-2 bg-slate-950 p-3 rounded-2xl border border-slate-800 relative z-10">
                <input type="text" readonly id="webhookUrlInput" value="<?= htmlspecialchars($webhook_url_display) ?>" 
                       class="bg-transparent border-0 outline-none w-full text-xs font-mono font-bold text-emerald-400 select-all">
                <button onclick="copyWebhookUrl()" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-black text-xs rounded-xl transition-all shadow-md active:scale-95 whitespace-nowrap">
                    <i class="fas fa-copy mr-1"></i> Copy
                </button>
            </div>
        </div>

        <!-- Total Logs Count Card (Glowing Border) -->
        <div class="glass-card rounded-[2rem] p-6 shadow-xl flex flex-col justify-between hover:shadow-2xl transition-all border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 text-4xl group-hover:scale-110 transition-transform"><i class="fas fa-history"></i></div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-history text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block leading-none">Webhook Logs</span>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white leading-tight mt-1"><?= number_format($stats['total_logs']) ?></h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/50 flex items-center justify-between text-[10px]">
                <span class="text-slate-400 font-bold">Error Rate:</span>
                <span class="font-black text-rose-500 flex items-center gap-1">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= $stats['total_logs'] > 0 ? round(($stats['err_logs'] / $stats['total_logs']) * 100, 1) : 0 ?>% (<?= number_format($stats['err_logs']) ?>)
                </span>
            </div>
        </div>

        <!-- Linked Parents Card -->
        <div class="glass-card rounded-[2rem] p-6 shadow-xl flex flex-col justify-between hover:shadow-2xl transition-all border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 text-4xl group-hover:scale-110 transition-transform"><i class="fas fa-user-friends"></i></div>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-indigo-500/10 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-user-friends text-lg"></i>
                </div>
                <div>
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block leading-none">Connected Parents</span>
                    <h3 class="text-3xl font-black text-slate-800 dark:text-white leading-tight mt-1"><?= number_format($stats['linked_parents']) ?></h3>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800/50 flex items-center justify-between text-[10px]">
                <span class="text-slate-400 font-bold">LINE Notify Groups:</span>
                <span class="font-black text-indigo-600 dark:text-indigo-400 flex items-center gap-1">
                    <i class="fab fa-line"></i> <?= number_format($stats['notify_tokens']) ?> กลุ่ม
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs Menu (Pill styling) -->
    <div class="flex bg-slate-100/80 dark:bg-slate-900/60 p-1.5 rounded-2xl mb-8 gap-1 w-fit border border-slate-200/50 dark:border-slate-800/50 relative">
        <button @click="activeTab = 'activity'" 
                :class="activeTab === 'activity' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-6 py-2.5 rounded-xl font-black text-xs transition-all flex items-center gap-2">
            <i class="fas fa-terminal text-sm"></i> Webhook & Simulator
        </button>
        <button @click="activeTab = 'tokens'" 
                :class="activeTab === 'tokens' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-6 py-2.5 rounded-xl font-black text-xs transition-all flex items-center gap-2">
            <i class="fab fa-line text-sm"></i> LINE Notify Groups
        </button>
        <button @click="activeTab = 'parents'" 
                :class="activeTab === 'parents' ? 'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-md' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300'"
                class="px-6 py-2.5 rounded-xl font-black text-xs transition-all flex items-center gap-2">
            <i class="fas fa-users-cog text-sm"></i> Parents Mapping
        </button>
    </div>

    <!-- TAB 1: WEBHOOK ACTIVITY & SIMULATOR -->
    <div x-show="activeTab === 'activity'" class="space-y-8" x-transition>
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            
            <!-- Webhook Simulator Panel -->
            <div class="glass-card rounded-[2.5rem] p-6 shadow-xl border border-white/50 xl:col-span-1 h-fit">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-500 text-white rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-vial"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-slate-800 dark:text-white leading-tight">จำลองส่งรหัสนักเรียน (Simulator)</h4>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">Local Webhook Sandbox</p>
                    </div>
                </div>
                
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-6 leading-relaxed">
                    พิมพ์รหัสประจำตัวนักเรียนลงด้านล่าง เพื่อจำลองข้อความที่พิมพ์โดยผู้ปกครองส่งเข้ามาในไลน์บอท เพื่อทดสอบระบบการจับคู่นักเรียน
                </p>

                <form id="simulateForm" class="space-y-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1.5">จำลอง LINE User ID</label>
                        <input type="text" name="sim_user_id" value="U_SIMULATED_TEST_PARENT" required
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-mono text-xs text-slate-700 dark:text-white outline-none focus:border-emerald-500 transition-colors">
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1.5">รหัสนักเรียนสำหรับทดสอบ</label>
                        <input type="text" name="sim_text" placeholder="เช่น 27505 หรือ /start" required
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-black text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                    </div>
                    <button type="submit" class="w-full py-3.5 btn-gradient-emerald text-white font-black rounded-xl shadow-lg active:scale-95 flex items-center justify-center gap-2 text-xs">
                        <i class="fas fa-play text-xs"></i> จำลองการยิง Webhook
                    </button>
                </form>

                <!-- macOS Style Console Output Log Box -->
                <div class="mt-6 hidden" id="consoleBox">
                    <div class="bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
                        <!-- macOS Header buttons -->
                        <div class="bg-slate-900 px-4 py-2.5 flex items-center justify-between border-b border-slate-950">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-rose-500 block"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-500 block"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-500 block"></span>
                            </div>
                            <span class="text-[9px] font-mono text-slate-500 font-bold uppercase tracking-wider">Terminal Output</span>
                            <div class="w-12"></div>
                        </div>
                        <!-- Code Output -->
                        <div class="p-4 font-mono text-[10px] text-slate-300 overflow-y-auto max-h-72 space-y-2 select-all leading-normal" id="consoleOutput">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webhook Activity Logs List -->
            <div class="glass-card rounded-[2.5rem] p-6 shadow-xl border border-white/50 xl:col-span-2">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-500 text-white rounded-2xl flex items-center justify-center shadow-lg">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-slate-800 dark:text-white leading-tight">ประวัติทราฟฟิกขาเข้า Webhook</h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none mt-1">Live Webhook Log Feed</p>
                        </div>
                    </div>
                    
                    <form action="line_monitor.php?action=clear_logs" method="POST" onsubmit="return confirmClearLogs(event)">
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-100 dark:border-rose-950 dark:bg-rose-950/20 rounded-xl font-black text-xs transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-trash-alt"></i> ล้างประวัติทั้งหมด
                        </button>
                    </form>
                </div>

                <!-- Webhook Logs Accordion List -->
                <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                    <?php if (!empty($logs)): ?>
                        <?php foreach ($logs as $l): 
                            $is_success = $l['status'] === 'success';
                            $status_color = $is_success ? 'emerald' : 'rose';
                            $event_icon = $l['event_type'] === 'message' ? 'fa-comment-dots' : ($l['event_type'] === 'join' ? 'fa-users' : 'fa-info-circle');
                        ?>
                        <div class="glass-card rounded-2xl border border-slate-200/60 dark:border-slate-800/80 hover:border-<?= $status_color ?>-500/40 hover:shadow-lg transition-all" x-data="{ open: false }">
                            <!-- Clickable Header row -->
                            <div @click="open = !open" class="p-4 cursor-pointer flex items-center justify-between gap-4 select-none">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-xl bg-<?= $status_color ?>-500/10 text-<?= $status_color ?>-600 dark:text-<?= $status_color ?>-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fas <?= $event_icon ?> text-xs"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="font-mono text-[9px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded font-bold uppercase"><?= htmlspecialchars($l['event_type']) ?></span>
                                            <span class="font-bold text-xs text-slate-800 dark:text-white truncate"><?= htmlspecialchars($l['response_message']) ?></span>
                                        </div>
                                        <p class="text-[9px] text-slate-400 mt-1">
                                            User ID: <span class="font-mono"><?= htmlspecialchars($l['user_id'] ?? '-') ?></span>
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    <span class="text-[9px] font-bold text-slate-400 whitespace-nowrap"><?= date('H:i:s d/m/Y', strtotime($l['created_at'])) ?></span>
                                    <i class="fas fa-chevron-down text-slate-400 text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                                </div>
                            </div>
                            
                            <!-- Collapsed Details Panel -->
                            <div x-show="open" x-collapse class="border-t border-slate-200/50 dark:border-slate-800/50 p-4 bg-slate-50/50 dark:bg-slate-950/20 space-y-4 rounded-b-2xl">
                                <!-- Headers -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">HTTP Headers</span>
                                        <button onclick="copyRawCode(this)" class="text-[9px] text-emerald-500 font-bold hover:underline"><i class="fas fa-copy"></i> Copy</button>
                                    </div>
                                    <pre class="bg-slate-950 text-slate-300 p-3 rounded-xl text-[10px] font-mono overflow-x-auto whitespace-pre-wrap max-h-40 border border-slate-900 leading-normal"><?= htmlspecialchars(json_encode(json_decode($l['headers'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                </div>
                                <!-- Raw Payload -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Raw JSON Payload</span>
                                        <button onclick="copyRawCode(this)" class="text-[9px] text-emerald-500 font-bold hover:underline"><i class="fas fa-copy"></i> Copy</button>
                                    </div>
                                    <pre class="bg-slate-950 text-emerald-400 p-3 rounded-xl text-[10px] font-mono overflow-x-auto whitespace-pre-wrap max-h-40 border border-slate-900 leading-normal"><?= htmlspecialchars(json_encode(json_decode($l['payload'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-16 text-slate-400 glass-card rounded-2xl border border-dashed border-slate-300 dark:border-slate-800">
                            <i class="fas fa-inbox text-5xl mb-4 opacity-20 block"></i>
                            <span class="font-bold">ยังไม่มีข้อมูล Log Webhook ในระบบ</span>
                            <p class="text-[10px] text-slate-400 mt-1">Webhook Logs จะถูกบันทึกเมื่อบอทได้รับข้อความจากทาง LINE</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: LINE NOTIFY GROUP TOKENS -->
    <div x-show="activeTab === 'tokens'" class="space-y-6" x-transition>
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
            <div>
                <h4 class="font-black text-slate-800 dark:text-white text-lg">กลุ่มห้องเรียนสำหรับการแจ้งเตือน LINE Notify</h4>
                <p class="text-xs text-slate-400 mt-1">จัดการโทเค็นแจ้งเตือนเช็คชื่อแยกรายห้องเรียนของครูที่ปรึกษา</p>
            </div>
            <button @click="showAddModal = true" class="px-5 py-3 btn-gradient-emerald text-white font-black rounded-xl shadow-lg flex items-center gap-2 text-xs">
                <i class="fas fa-plus"></i> เพิ่มกลุ่ม LINE Notify ใหม่
            </button>
        </div>

        <!-- Guide Alert -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/20 dark:to-teal-950/20 border border-emerald-200/50 dark:border-emerald-800/30 p-5 rounded-[2rem] text-xs text-emerald-800 dark:text-emerald-300 flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-lightbulb text-lg"></i>
            </div>
            <div>
                <p class="font-black text-sm mb-1.5">💡 คู่มือการรับ Token ส่งแจ้งเตือนรายห้องเรียน:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 leading-relaxed text-slate-600 dark:text-slate-400">
                    <div>
                        <p>1. เข้าเว็บ <a href="https://notify-bot.line.me/" target="_blank" class="underline font-bold text-emerald-600 dark:text-emerald-400">LINE Notify Portal</a> แล้วล็อกอิน</p>
                        <p>2. ในเมนูจัดการส่วนตัว กด <strong>"ออก Access Token"</strong> ระบุชื่อและเลือกกลุ่มที่จะแจ้งเตือน</p>
                    </div>
                    <div>
                        <p>3. กดปุ่มบันทึกเพิ่มในระบบนี้ พร้อมจับคู่ระดับชั้น/ห้องเรียนที่ถูกต้อง</p>
                        <p>4. <strong class="text-rose-600">ข้อระวัง:</strong> อย่าลืมกดเชิญบอท <strong>@linenotify</strong> เข้าไปในกลุ่มไลน์นั้นๆ ด้วยนะคะ</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bento Grid classroom cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($tokens)): ?>
                <?php foreach ($tokens as $t): ?>
                <div class="glass-card rounded-[2rem] p-6 shadow-lg border border-white/50 hover:shadow-xl hover:scale-[1.01] transition-all relative overflow-hidden flex flex-col justify-between group">
                    <div class="absolute -right-4 -bottom-4 text-emerald-500/5 text-9xl font-bold select-none pointer-events-none group-hover:scale-110 transition-transform">ม.<?= htmlspecialchars($t['line_class']) ?></div>
                    <div>
                        <!-- Header target -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-1 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-2.5 py-1 rounded-xl text-[10px] font-black uppercase tracking-wider">
                                <i class="fab fa-line"></i> LINE Notify
                            </div>
                            <span class="px-3 py-1 bg-indigo-500 text-white font-black rounded-full text-xs shadow-sm shadow-indigo-500/20">
                                ม.<?= htmlspecialchars($t['line_class']) ?><?= $t['line_room'] > 0 ? '/' . htmlspecialchars($t['line_room']) : ' (ทุกห้อง)' ?>
                            </span>
                        </div>
                        
                        <!-- Room name & Title -->
                        <h4 class="font-black text-slate-800 dark:text-white text-base leading-tight mb-1"><?= htmlspecialchars($t['line_name']) ?></h4>
                        <p class="text-[10px] text-slate-400 mb-4">ครูที่ปรึกษา ม.<?= htmlspecialchars($t['line_class']) ?><?= $t['line_room'] > 0 ? ' ห้อง ' . htmlspecialchars($t['line_room']) : ' ทุกห้องเรียน' ?></p>
                        
                        <!-- Masked Token Input -->
                        <div class="bg-slate-100 dark:bg-slate-950 p-2.5 rounded-xl border border-slate-200/50 dark:border-slate-800/80 flex items-center justify-between gap-2 mb-6" x-data="{ reveal: false }">
                            <input :type="reveal ? 'text' : 'password'" readonly value="<?= htmlspecialchars($t['token']) ?>" 
                                   class="bg-transparent border-0 outline-none w-full text-[10px] font-mono text-slate-600 dark:text-slate-400 select-all">
                            <button @click="reveal = !reveal" class="text-slate-400 hover:text-slate-600 px-1">
                                <i class="fas" :class="reveal ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Footer actions buttons -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800/50 gap-2">
                        <button @click="testTokenData.id = <?= htmlspecialchars(json_encode($t['id']), ENT_QUOTES, 'UTF-8') ?>; testTokenData.name = <?= htmlspecialchars(json_encode($t['line_name'] . ' (ม.' . $t['line_class'] . ($t['line_room'] > 0 ? '/' . $t['line_room'] : ' ทุกห้อง') . ')'), ENT_QUOTES, 'UTF-8') ?>; showTestModal = true"
                                class="flex-1 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold rounded-xl text-[10px] shadow-md transition flex items-center justify-center gap-1.5 active:scale-95">
                            <i class="fas fa-paper-plane"></i> ทดสอบส่ง
                        </button>
                        
                        <div class="flex items-center gap-1">
                            <button @click="editTokenData = { id: <?= htmlspecialchars(json_encode($t['id']), ENT_QUOTES, 'UTF-8') ?>, line_name: <?= htmlspecialchars(json_encode($t['line_name']), ENT_QUOTES, 'UTF-8') ?>, line_class: <?= htmlspecialchars(json_encode($t['line_class']), ENT_QUOTES, 'UTF-8') ?>, line_room: <?= htmlspecialchars(json_encode($t['line_room']), ENT_QUOTES, 'UTF-8') ?>, token: <?= htmlspecialchars(json_encode($t['token']), ENT_QUOTES, 'UTF-8') ?> }; showEditModal = true"
                                    class="w-8 h-8 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-white rounded-xl text-[10px] font-bold transition flex items-center justify-center active:scale-95 shadow-sm">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="line_monitor.php?action=delete_token" method="POST" onsubmit="return confirm('คุณต้องการลบกลุ่ม LINE Notify นี้ใช่หรือไม่?');" class="inline">
                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                <button type="submit" class="w-8 h-8 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl text-[10px] font-bold transition flex items-center justify-center active:scale-95 shadow-sm">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 text-slate-400 glass-card rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-800">
                    <i class="fas fa-bell-slash text-6xl mb-4 opacity-20 block text-slate-300 dark:text-slate-700"></i>
                    <span class="font-black text-lg text-slate-700 dark:text-slate-300">ยังไม่มีข้อมูลกลุ่ม LINE Notify ในระบบ</span>
                    <p class="text-xs text-slate-400 mt-1">กดปุ่ม "เพิ่มกลุ่ม LINE Notify ใหม่" เพื่อเริ่มส่งแจ้งเตือนการเช็คชื่อเวลาเรียน</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- TAB 3: LINKED PARENTS -->
    <div x-show="activeTab === 'parents'" class="space-y-6" x-transition>
        <div class="mb-6">
            <h4 class="font-black text-slate-800 dark:text-white text-lg">รายชื่อผู้ปกครองที่เชื่อมต่อไลน์ระบบ StdCare</h4>
            <p class="text-xs text-slate-400 mt-1">รายชื่อบัญชี LINE ของผู้ปกครองที่ทำการยืนยันตัวตนกับรหัสนักเรียนสำเร็จ</p>
        </div>

        <!-- Parents directory cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($linked_parents)): ?>
                <?php foreach ($linked_parents as $p): ?>
                <div class="glass-card rounded-[2rem] p-6 shadow-lg border border-white/50 hover:shadow-xl transition-all relative overflow-hidden flex flex-col justify-between group">
                    <div>
                        <!-- Parent Profile Header -->
                        <div class="flex items-center gap-3.5 mb-5">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg font-black text-lg uppercase flex-shrink-0">
                                <?= mb_substr($p['Stu_name'] ?? 'P', 0, 1) ?>
                            </div>
                            <div class="min-w-0">
                                <span class="text-[9px] font-black text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded-full uppercase tracking-wider block w-fit mb-1"><i class="fab fa-line"></i> LINE Verified</span>
                                <h4 class="font-black text-slate-800 dark:text-white text-sm truncate" title="LINE ID: <?= htmlspecialchars($p['line_userid']) ?>">
                                    LINE User: <?= htmlspecialchars(mb_substr((string)$p['line_userid'], 0, 15)) ?>...
                                </h4>
                            </div>
                        </div>

                        <!-- Child info section -->
                        <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-200/50 dark:border-slate-800/80 mb-6">
                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-2">นักเรียนในความดูแล</span>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-graduation-cap text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-bold text-xs text-slate-800 dark:text-white truncate">
                                        <?= htmlspecialchars(($p['Stu_pre'] ?? '') . ($p['Stu_name'] ?? 'ไม่พบชื่อนักเรียน') . ' ' . ($p['Stu_sur'] ?? '')) ?>
                                    </h5>
                                    <div class="flex items-center gap-2 mt-0.5 text-[9px] font-bold text-slate-400">
                                        <span>รหัสประจำตัว: <?= htmlspecialchars($p['student_id']) ?></span>
                                        <?php if (isset($p['Stu_major'])): ?>
                                            <span class="px-1.5 py-0.2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-md">ม.<?= htmlspecialchars($p['Stu_major']) ?>/<?= htmlspecialchars($p['Stu_room']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-slate-800/50">
                        <span class="text-[9px] text-slate-400 font-bold">ผูกเมื่อ: <?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></span>
                        
                        <form action="line_monitor.php?action=unlink_parent" method="POST" onsubmit="return confirm('คุณต้องการยกเลิกการผูกบัญชีไลน์ของผู้ปกครองรายนี้ใช่หรือไม่?');" class="inline">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="px-3.5 py-2 bg-rose-500/10 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-100 dark:border-rose-950 rounded-xl text-[10px] font-black shadow-sm transition flex items-center gap-1.5 active:scale-95">
                                <i class="fas fa-unlink"></i> ยกเลิกการผูกบัญชี
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 text-slate-400 glass-card rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-800">
                    <i class="fas fa-user-slash text-6xl mb-4 opacity-20 block text-slate-300 dark:text-slate-700"></i>
                    <span class="font-black text-lg text-slate-700 dark:text-slate-300">ยังไม่มีรายชื่อผู้ปกครองลงทะเบียน LINE</span>
                    <p class="text-xs text-slate-400 mt-1">ผู้ปกครองจะปรากฏที่นี่หลังจากจับคู่รหัสนักเรียนผ่านระบบแชทไลน์สำเร็จ</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- MODAL: ADD LINE NOTIFY TOKEN -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="showAddModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="relative w-full max-w-lg mx-auto z-50">
            <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" x-transition:enter="ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-5 flex items-center justify-between text-white border-b border-emerald-600">
                    <h5 class="font-black flex items-center gap-2"><i class="fab fa-line text-lg"></i> เพิ่มกลุ่ม LINE Notify ใหม่</h5>
                    <button @click="showAddModal = false" class="text-white/80 hover:text-white transition-colors"><i class="fas fa-times text-lg"></i></button>
                </div>
                <form action="line_monitor.php?action=add_token" method="POST" class="p-6 space-y-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ชื่อกลุ่ม / คำอธิบายกลุ่ม</label>
                        <input type="text" name="line_name" placeholder="เช่น ครูที่ปรึกษา ม.1/1" required 
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ระดับชั้น</label>
                            <select name="line_class" required 
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:border-emerald-500 cursor-pointer">
                                <option value="1">ม.1</option>
                                <option value="2">ม.2</option>
                                <option value="3">ม.3</option>
                                <option value="4">ม.4</option>
                                <option value="5">ม.5</option>
                                <option value="6">ม.6</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ห้องเรียน (0 = ทุกห้อง)</label>
                            <input type="number" name="line_room" value="0" min="0" max="20" required 
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">LINE Notify Token Key</label>
                        <input type="text" name="token" placeholder="วาง Access Token Key ตรงนี้" required 
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-xs text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all">
                    </div>
                    <div class="pt-5 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" @click="showAddModal = false" class="px-5 py-3 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">ยกเลิก</button>
                        <button type="submit" class="px-5 py-3 btn-gradient-emerald text-white font-black rounded-xl text-xs shadow-lg">บันทึกข้อมูล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: EDIT LINE NOTIFY TOKEN -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="showEditModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="relative w-full max-w-lg mx-auto z-50">
            <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" x-transition:enter="ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                <div class="bg-gradient-to-r from-amber-500 to-orange-600 px-6 py-5 flex items-center justify-between text-white border-b border-amber-600">
                    <h5 class="font-black flex items-center gap-2"><i class="fas fa-edit"></i> แก้ไขข้อมูล LINE Notify</h5>
                    <button @click="showEditModal = false" class="text-white/80 hover:text-white transition-colors"><i class="fas fa-times text-lg"></i></button>
                </div>
                <form action="line_monitor.php?action=edit_token" method="POST" class="p-6 space-y-4">
                    <input type="hidden" name="id" x-model="editTokenData.id">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ชื่อกลุ่ม / คำอธิบายกลุ่ม</label>
                        <input type="text" name="line_name" x-model="editTokenData.line_name" required 
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ระดับชั้น</label>
                            <select name="line_class" x-model="editTokenData.line_class" required 
                                    class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:border-amber-500 cursor-pointer">
                                <option value="1">ม.1</option>
                                <option value="2">ม.2</option>
                                <option value="3">ม.3</option>
                                <option value="4">ม.4</option>
                                <option value="5">ม.5</option>
                                <option value="6">ม.6</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">ห้องเรียน (0 = ทุกห้อง)</label>
                            <input type="number" name="line_room" x-model="editTokenData.line_room" min="0" max="20" required 
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">LINE Notify Token Key</label>
                        <input type="text" name="token" x-model="editTokenData.token" required 
                               class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-mono text-xs text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all">
                    </div>
                    <div class="pt-5 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false" class="px-5 py-3 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">ยกเลิก</button>
                        <button type="submit" class="px-5 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white font-black rounded-xl text-xs shadow-lg">บันทึกการแก้ไข</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL: TEST SEND LINE NOTIFY MESSAGE -->
    <div x-show="showTestModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-md transition-opacity" @click="showTestModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        <div class="relative w-full max-w-md mx-auto z-50">
            <div class="relative bg-white dark:bg-slate-800 rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-700 overflow-hidden" x-transition:enter="ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100">
                <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-5 flex items-center justify-between text-white border-b border-emerald-600">
                    <h5 class="font-black flex items-center gap-2"><i class="fas fa-paper-plane"></i> ทดสอบส่งข้อความแจ้งเตือน</h5>
                    <button @click="showTestModal = false" class="text-white/80 hover:text-white transition-colors"><i class="fas fa-times text-lg"></i></button>
                </div>
                <form id="testNotifyForm" class="p-6 space-y-4">
                    <input type="hidden" name="id" x-model="testTokenData.id">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest block mb-1.5">กลุ่มเป้าหมาย (Target Group)</label>
                        <div class="flex items-center gap-2.5 bg-emerald-50 dark:bg-emerald-950/20 px-4 py-3 rounded-xl border border-emerald-100 dark:border-emerald-900/50">
                            <div class="w-7 h-7 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg flex items-center justify-center text-sm flex-shrink-0">
                                <i class="fab fa-line"></i>
                            </div>
                            <span class="text-slate-800 dark:text-slate-200 font-black text-xs" x-text="testTokenData.name || 'ไม่ได้ระบุกลุ่ม'"></span>
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">พิมพ์ข้อความทดสอบ</label>
                        <textarea name="test_message" x-model="testTokenData.message" required rows="3"
                                  class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-slate-700 dark:text-white outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all"></textarea>
                    </div>
                    <div class="pt-5 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                        <button type="button" @click="showTestModal = false" class="px-5 py-3 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-300 font-bold rounded-xl text-xs hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">ยกเลิก</button>
                        <button type="submit" class="px-5 py-3 btn-gradient-emerald text-white font-black rounded-xl text-xs shadow-lg flex items-center gap-1.5">
                            <i class="fas fa-paper-plane text-xs"></i> ยิงข้อความทดสอบ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
function copyWebhookUrl() {
    var copyText = document.getElementById("webhookUrlInput");
    copyText.select();
    copyText.setSelectionRange(0, 99999); 
    navigator.clipboard.writeText(copyText.value).then(function() {
        Swal.fire({
            title: 'คัดลอก URL เรียบร้อย!',
            icon: 'success',
            confirmButtonColor: '#10b981',
            timer: 1500
        });
    });
}

function copyRawCode(btn) {
    const pre = btn.parentElement.nextElementSibling;
    navigator.clipboard.writeText(pre.innerText).then(function() {
        Swal.fire({
            title: 'คัดลอกสำเร็จ!',
            icon: 'success',
            confirmButtonColor: '#10b981',
            timer: 1000
        });
    });
}

function confirmClearLogs(e) {
    e.preventDefault();
    Swal.fire({
        title: 'คุณต้องการล้างประวัติ Webhook?',
        text: "ข้อมูลประวัติที่ถูกลบจะไม่สามารถกู้คืนกลับมาได้!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ใช่, ล้างข้อมูลทั้งหมด!',
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });
}

$(document).ready(function() {
    // Webhook Simulator Form AJAX
    $('#simulateForm').submit(function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalBtnHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังทดสอบ...');
        $('#consoleBox').removeClass('hidden');
        $('#consoleOutput').html('<span class="text-slate-600">// กำลังส่ง request จำลองข้อความไลน์...</span>');
        
        $.ajax({
            url: 'line_monitor.php?action=simulate',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                
                let output = '';
                if (res.success) {
                    output += `<span class="text-emerald-400">[CONNECTED] 200 OK - Local Webhook Call Success!</span>\n`;
                    output += `<span class="text-slate-500">Target Endpoint: ${res.webhook_url}</span>\n`;
                    output += `<span class="text-slate-500">Simulated Payload:</span>\n<span class="text-blue-400">${JSON.stringify(res.payload, null, 2)}</span>\n\n`;
                    output += `<span class="text-slate-500">Webhook JSON Response:</span>\n<span class="text-emerald-300 font-bold">${JSON.stringify(res.response, null, 2)}</span>\n`;
                    
                    Swal.fire({
                        title: 'จำลองสำเร็จ!',
                        text: 'ระบบประมวลผลข้อความและส่งแจ้งเตือนเรียบร้อยแล้ว',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    }).then(() => {
                        setTimeout(() => { location.reload(); }, 500);
                    });
                } else {
                    output += `<span class="text-rose-500">[ERROR CODE ${res.http_code || '500'}] การส่งไม่สำเร็จ</span>\n`;
                    output += `<span class="text-rose-400">รายละเอียดขัดข้อง: ${res.message}</span>\n`;
                    if (res.response) {
                        output += `<span class="text-rose-300">Raw response: ${typeof res.response === 'object' ? JSON.stringify(res.response) : res.response}</span>\n`;
                    }
                    
                    Swal.fire({
                        title: 'จำลองมีข้อผิดพลาด',
                        text: res.message,
                        icon: 'error',
                        confirmButtonColor: '#10b981'
                    });
                }
                
                $('#consoleOutput').html(output);
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                const errMsg = xhr.responseText || error;
                $('#consoleOutput').html(`<span class="text-rose-500">[cURL FATAL EXCEPTION]</span>\nStatus: ${status}\nException: ${errMsg}`);
                
                Swal.fire({
                    title: 'เชื่อมต่อล้มเหลว',
                    text: 'ไม่สามารถติดต่อ Webhook ได้ กรุณาตรวจสอบเว็บเซิร์ฟเวอร์',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
            }
        });
    });

    // Test Notify Form AJAX
    $('#testNotifyForm').submit(function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalBtnHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังยิง...');
        
        $.ajax({
            url: 'line_monitor.php?action=test_notify',
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(originalBtnHtml);
                
                if (res.success) {
                    Swal.fire({
                        title: 'ส่งสำเร็จ!',
                        text: 'ส่งข้อความแจ้งเตือนเข้ากลุ่ม LINE Notify แล้ว',
                        icon: 'success',
                        confirmButtonColor: '#10b981'
                    });
                    $form.find('button[type="button"]').click(); // close modal
                } else {
                    Swal.fire({
                        title: 'ส่งไม่สำเร็จ',
                        text: res.message,
                        icon: 'error',
                        confirmButtonColor: '#10b981'
                    });
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(originalBtnHtml);
                Swal.fire({
                    title: 'เชื่อมต่อขัดข้อง',
                    text: 'ไม่สามารถส่งขอ LINE API ได้ในขณะนี้',
                    icon: 'error',
                    confirmButtonColor: '#10b981'
                });
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
