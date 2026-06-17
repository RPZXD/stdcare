<?php
/**
 * View: LINE Webhook & Notify Monitor
 * Uses Bootstrap 4 modals + jQuery (matching project pattern)
 * Components: ui_header, ui_stat_card, glass-effect cards
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

// Export tokens as JSON for JS usage (safe — no inline PHP in attributes)
$tokensJson = json_encode($tokens ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<div class="animate-fadeIn">

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <script>
            Swal.fire({
                title: '<?= htmlspecialchars($_SESSION['flash_message']) ?>',
                icon: '<?= $_SESSION['flash_type'] ?? 'success' ?>',
                confirmButtonColor: '#10b981',
                timer: 3000
            });
        </script>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Header -->
    <?php
    $headerData = [
        'title' => 'LINE <span class="text-emerald-500 italic">Webhook & Notify</span>',
        'subtitle' => 'ศูนย์บริการจัดการและมอนิเตอร์การรับส่งข้อมูลผ่านไลน์',
        'icon' => 'fa-desktop',
        'color' => 'emerald'
    ];
    include __DIR__ . '/../components/ui_header.php';
    ?>

    <!-- DB Error Alert -->
    <?php if (!empty($db_error)): ?>
    <div class="mb-6 p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-400 rounded-2xl flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-lg mt-0.5"></i>
        <div>
            <p class="font-bold text-sm">พบข้อผิดพลาดฐานข้อมูล</p>
            <p class="text-xs mt-1"><?= htmlspecialchars($db_error) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6 mb-6 lg:mb-10">
        <!-- Webhook URL Card -->
        <div class="col-span-2 glass-effect p-4 lg:p-6 rounded-2xl lg:rounded-[2rem] border border-white/50 shadow-xl relative overflow-hidden">
            <div class="flex items-center gap-2 text-emerald-500 font-black text-[9px] uppercase tracking-widest mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                WEBHOOK ENDPOINT URL
            </div>
            <p class="text-[10px] text-slate-400 mb-3">นำไปกรอกในหน้าคอนโซลนักพัฒนาของไลน์</p>
            <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-900 p-2.5 rounded-xl border border-slate-200 dark:border-slate-800">
                <input type="text" readonly id="webhookUrlInput" value="<?= htmlspecialchars($webhook_url_display) ?>"
                       class="bg-transparent border-0 outline-none w-full text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 select-all">
                <button onclick="copyWebhookUrl()" class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-[10px] rounded-lg transition whitespace-nowrap">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
            </div>
        </div>

        <?php
        $cards = [
            ['label' => 'Webhook Logs', 'value' => number_format($stats['total_logs']), 'icon' => 'fa-history', 'color' => 'emerald', 'status' => $stats['err_logs'] > 0 ? 'Error: ' . $stats['err_logs'] : 'OK'],
            ['label' => 'Connected Parents', 'value' => number_format($stats['linked_parents']), 'icon' => 'fa-user-friends', 'color' => 'indigo', 'status' => $stats['notify_tokens'] . ' กลุ่ม'],
        ];
        foreach ($cards as $card):
            $statData = $card;
            include __DIR__ . '/../components/ui_stat_card.php';
        endforeach;
        ?>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-pills mb-6 flex gap-1 bg-slate-100 dark:bg-slate-900/60 p-1.5 rounded-2xl w-fit border border-slate-200/50 dark:border-slate-800/50" id="lineTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-xl font-bold text-xs <?= $activeTab === 'activity' ? 'active bg-white dark:bg-slate-800 text-emerald-600 shadow-md' : 'text-slate-500' ?>"
               id="tab-activity" data-toggle="pill" href="#pane-activity" role="tab">
                <i class="fas fa-terminal mr-1"></i> Webhook & Simulator
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-xl font-bold text-xs <?= $activeTab === 'tokens' ? 'active bg-white dark:bg-slate-800 text-emerald-600 shadow-md' : 'text-slate-500' ?>"
               id="tab-tokens" data-toggle="pill" href="#pane-tokens" role="tab">
                <i class="fab fa-line mr-1"></i> LINE Notify Groups
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-4 py-2 rounded-xl font-bold text-xs <?= $activeTab === 'parents' ? 'active bg-white dark:bg-slate-800 text-emerald-600 shadow-md' : 'text-slate-500' ?>"
               id="tab-parents" data-toggle="pill" href="#pane-parents" role="tab">
                <i class="fas fa-users-cog mr-1"></i> Parents Mapping
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="lineTabContent">

        <!-- ═══════════════════ TAB 1: WEBHOOK ACTIVITY ═══════════════════ -->
        <div class="tab-pane fade <?= $activeTab === 'activity' ? 'show active' : '' ?>" id="pane-activity" role="tabpanel">
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- Simulator -->
                <div class="glass-effect rounded-3xl lg:rounded-[2.5rem] p-5 lg:p-8 shadow-xl border-t border-white/50 xl:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-emerald-600 text-white rounded-2xl flex items-center justify-center shadow-lg"><i class="fas fa-vial"></i></div>
                        <div>
                            <h4 class="font-black text-slate-800 dark:text-white text-sm leading-tight">จำลองส่งรหัสนักเรียน</h4>
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Local Webhook Sandbox</p>
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4 leading-relaxed">พิมพ์รหัสนักเรียนด้านล่าง เพื่อจำลองข้อความจากผู้ปกครอง</p>
                    <form id="simulateForm">
                        <div class="mb-3">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">จำลอง LINE User ID</label>
                            <input type="text" name="sim_user_id" value="U_SIMULATED_TEST_PARENT" required
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-mono text-xs outline-none focus:border-emerald-500">
                        </div>
                        <div class="mb-3">
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">รหัสนักเรียนสำหรับทดสอบ</label>
                            <input type="text" name="sim_text" placeholder="เช่น 27505 หรือ /start" required
                                   class="w-full px-3 py-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-bold outline-none focus:border-emerald-500">
                        </div>
                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg text-xs flex items-center justify-center gap-2 transition">
                            <i class="fas fa-play"></i> จำลองการยิง Webhook
                        </button>
                    </form>

                    <!-- Console Output -->
                    <div class="mt-4 hidden" id="consoleBox">
                        <div class="bg-slate-950 rounded-2xl overflow-hidden border border-slate-800 shadow-2xl">
                            <div class="bg-slate-900 px-4 py-2 flex items-center justify-between border-b border-slate-950">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                </div>
                                <span class="text-[9px] font-mono text-slate-500 font-bold uppercase tracking-wider">Terminal</span>
                                <div class="w-12"></div>
                            </div>
                            <div class="p-4 font-mono text-[10px] text-slate-300 overflow-y-auto max-h-72 space-y-2 select-all leading-normal" id="consoleOutput"></div>
                        </div>
                    </div>
                </div>

                <!-- Webhook Logs -->
                <div class="glass-effect rounded-3xl lg:rounded-[2.5rem] p-5 lg:p-8 shadow-xl border-t border-white/50 xl:col-span-2">
                    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-600 text-white rounded-2xl flex items-center justify-center shadow-lg"><i class="fas fa-receipt"></i></div>
                            <div>
                                <h4 class="font-black text-slate-800 dark:text-white text-sm leading-tight">ประวัติทราฟฟิก Webhook</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Live Webhook Log Feed</p>
                            </div>
                        </div>
                        <form action="line_monitor.php?action=clear_logs" method="POST" onsubmit="return confirmClearLogs(event)">
                            <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-100 dark:border-rose-950 dark:bg-rose-950/20 rounded-xl font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                                <i class="fas fa-trash-alt"></i> ล้างประวัติ
                            </button>
                        </form>
                    </div>

                    <div class="space-y-3 max-h-[600px] overflow-y-auto pr-1">
                        <?php if (!empty($logs)): ?>
                            <?php foreach ($logs as $l):
                                $is_success = ($l['status'] ?? '') === 'success';
                                $badge_class = $is_success ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' : 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400';
                                $icon = ($l['event_type'] ?? '') === 'message' ? 'fa-comment-dots' : (($l['event_type'] ?? '') === 'join' ? 'fa-users' : 'fa-info-circle');
                            ?>
                            <div class="glass-effect rounded-2xl border border-slate-200/60 dark:border-slate-800/80 hover:shadow-lg transition-all">
                                <div class="p-4 cursor-pointer flex items-center justify-between gap-4 select-none" onclick="$(this).next().toggle(300)">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-8 h-8 rounded-xl <?= $badge_class ?> flex items-center justify-center flex-shrink-0">
                                            <i class="fas <?= $icon ?> text-xs"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-[9px] px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded font-bold uppercase"><?= htmlspecialchars($l['event_type'] ?? '-') ?></span>
                                                <span class="font-bold text-xs text-slate-800 dark:text-white truncate"><?= htmlspecialchars($l['response_message'] ?? '') ?></span>
                                            </div>
                                            <p class="text-[9px] text-slate-400 mt-1">User: <span class="font-mono"><?= htmlspecialchars($l['user_id'] ?? '-') ?></span></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 flex-shrink-0">
                                        <span class="text-[9px] font-bold text-slate-400 whitespace-nowrap"><?= date('H:i:s d/m/Y', strtotime($l['created_at'] ?? 'now')) ?></span>
                                        <i class="fas fa-chevron-down text-slate-400 text-[10px]"></i>
                                    </div>
                                </div>
                                <div class="border-t border-slate-200/50 dark:border-slate-800/50 p-4 bg-slate-50/50 dark:bg-slate-950/20 space-y-3 rounded-b-2xl" style="display:none">
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">HTTP Headers</span>
                                        <pre class="bg-slate-950 text-slate-300 p-3 rounded-xl text-[10px] font-mono overflow-x-auto whitespace-pre-wrap max-h-40 border border-slate-900"><?= htmlspecialchars(json_encode(json_decode($l['headers'] ?? '{}', true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}') ?></pre>
                                    </div>
                                    <div>
                                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider block mb-1">Raw JSON Payload</span>
                                        <pre class="bg-slate-950 text-emerald-400 p-3 rounded-xl text-[10px] font-mono overflow-x-auto whitespace-pre-wrap max-h-40 border border-slate-900"><?= htmlspecialchars(json_encode(json_decode($l['payload'] ?? '{}', true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}') ?></pre>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-16 text-slate-400 glass-effect rounded-2xl border border-dashed border-slate-300 dark:border-slate-800">
                                <i class="fas fa-inbox text-5xl mb-4 opacity-20 block"></i>
                                <span class="font-bold">ยังไม่มีข้อมูล Log Webhook</span>
                                <p class="text-[10px] text-slate-400 mt-1">Logs จะถูกบันทึกเมื่อบอทได้รับข้อความจาก LINE</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══════════════════ TAB 2: LINE NOTIFY TOKENS ═══════════════════ -->
        <div class="tab-pane fade <?= $activeTab === 'tokens' ? 'show active' : '' ?>" id="pane-tokens" role="tabpanel">
            <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 mb-6">
                <div>
                    <h4 class="font-black text-slate-800 dark:text-white text-lg">กลุ่มห้องเรียน LINE Notify</h4>
                    <p class="text-xs text-slate-400 mt-1">จัดการโทเค็นแจ้งเตือนแยกรายห้องเรียน</p>
                </div>
                <button class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg flex items-center gap-2 text-xs transition"
                        data-toggle="modal" data-target="#addTokenModal">
                    <i class="fas fa-plus"></i> เพิ่มกลุ่มใหม่
                </button>
            </div>

            <!-- Guide -->
            <div class="bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/50 dark:border-emerald-800/30 p-4 rounded-2xl text-xs text-emerald-800 dark:text-emerald-300 flex items-start gap-3 mb-6">
                <i class="fas fa-lightbulb text-emerald-500 text-lg mt-0.5"></i>
                <div>
                    <p class="font-bold mb-1">💡 คู่มือ:</p>
                    <p>1. เข้า <a href="https://notify-bot.line.me/" target="_blank" class="underline font-bold">LINE Notify Portal</a> → 2. ออก Access Token → 3. เพิ่มในระบบนี้ → 4. เชิญ <strong>@linenotify</strong> เข้ากลุ่ม</p>
                </div>
            </div>

            <!-- Token Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($tokens)): ?>
                    <?php foreach ($tokens as $idx => $t): ?>
                    <div class="glass-effect rounded-3xl lg:rounded-[2rem] p-5 lg:p-6 shadow-xl border-t border-white/50 hover:shadow-2xl hover:scale-[1.01] transition-all relative overflow-hidden flex flex-col justify-between group">
                        <div class="absolute -right-4 -bottom-4 text-emerald-500/5 text-8xl font-bold select-none pointer-events-none">ม.<?= htmlspecialchars($t['line_class']) ?></div>
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 px-2 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                    <i class="fab fa-line"></i> LINE Notify
                                </div>
                                <span class="px-2.5 py-1 bg-indigo-600 text-white font-bold rounded-full text-[10px] shadow-sm">
                                    ม.<?= htmlspecialchars($t['line_class']) ?><?= $t['line_room'] > 0 ? '/' . htmlspecialchars($t['line_room']) : ' (ทุกห้อง)' ?>
                                </span>
                            </div>
                            <h4 class="font-black text-slate-800 dark:text-white text-base leading-tight mb-1"><?= htmlspecialchars($t['line_name']) ?></h4>
                            <p class="text-[10px] text-slate-400 mb-3">ม.<?= htmlspecialchars($t['line_class']) ?><?= $t['line_room'] > 0 ? ' ห้อง ' . htmlspecialchars($t['line_room']) : ' ทุกห้องเรียน' ?></p>

                            <div class="bg-slate-100 dark:bg-slate-900 p-2 rounded-xl border border-slate-200/50 dark:border-slate-800 flex items-center justify-between gap-2 mb-4">
                                <input type="password" readonly value="<?= htmlspecialchars($t['token']) ?>" id="tokenField_<?= $idx ?>"
                                       class="bg-transparent border-0 outline-none w-full text-[10px] font-mono text-slate-600 dark:text-slate-400 select-all">
                                <button onclick="toggleToken(<?= $idx ?>)" class="text-slate-400 hover:text-slate-600 px-1 text-xs">
                                    <i class="fas fa-eye" id="tokenEye_<?= $idx ?>"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/50 gap-2">
                            <button class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-[10px] shadow transition flex items-center justify-center gap-1.5"
                                    data-test-id="<?= $t['id'] ?>"
                                    data-test-name="<?= htmlspecialchars($t['line_name'] . ' (ม.' . $t['line_class'] . ($t['line_room'] > 0 ? '/' . $t['line_room'] : ' ทุกห้อง') . ')') ?>"
                                    onclick="openTestModal(this)">
                                <i class="fas fa-paper-plane"></i> ทดสอบส่ง
                            </button>
                            <div class="flex items-center gap-1">
                                <button class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 hover:bg-amber-500 text-amber-500 hover:text-white rounded-xl text-[10px] transition flex items-center justify-center"
                                        data-edit-id="<?= $t['id'] ?>"
                                        data-edit-name="<?= htmlspecialchars($t['line_name']) ?>"
                                        data-edit-class="<?= $t['line_class'] ?>"
                                        data-edit-room="<?= $t['line_room'] ?>"
                                        data-edit-token="<?= htmlspecialchars($t['token']) ?>"
                                        onclick="openEditModal(this)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form action="line_monitor.php?action=delete_token" method="POST" onsubmit="return confirm('ลบกลุ่มนี้?');" class="inline">
                                    <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                    <button type="submit" class="w-8 h-8 bg-rose-100 dark:bg-rose-900/30 hover:bg-rose-500 text-rose-500 hover:text-white rounded-xl text-[10px] transition flex items-center justify-center">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-20 text-slate-400 glass-effect rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-800">
                        <i class="fas fa-bell-slash text-6xl mb-4 opacity-20 block"></i>
                        <span class="font-bold text-lg text-slate-700 dark:text-slate-300">ยังไม่มีกลุ่ม LINE Notify</span>
                        <p class="text-xs text-slate-400 mt-1">กดปุ่ม "เพิ่มกลุ่มใหม่" เพื่อเริ่มต้น</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ═══════════════════ TAB 3: PARENTS MAPPING ═══════════════════ -->
        <div class="tab-pane fade <?= $activeTab === 'parents' ? 'show active' : '' ?>" id="pane-parents" role="tabpanel">
            <div class="mb-6">
                <h4 class="font-black text-slate-800 dark:text-white text-lg">ผู้ปกครองที่เชื่อมต่อ LINE</h4>
                <p class="text-xs text-slate-400 mt-1">รายชื่อที่ยืนยันตัวตนกับรหัสนักเรียนสำเร็จ</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($linked_parents)): ?>
                    <?php foreach ($linked_parents as $p): ?>
                    <div class="glass-effect rounded-3xl lg:rounded-[2rem] p-5 lg:p-6 shadow-xl border-t border-white/50 hover:shadow-2xl transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center shadow-lg font-bold text-lg uppercase flex-shrink-0">
                                    <?= mb_substr($p['Stu_name'] ?? 'P', 0, 1) ?>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[9px] font-bold text-emerald-500 bg-emerald-100 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full uppercase tracking-wider block w-fit mb-1"><i class="fab fa-line"></i> Verified</span>
                                    <h4 class="font-bold text-slate-800 dark:text-white text-xs truncate" title="<?= htmlspecialchars($p['line_userid'] ?? '') ?>">
                                        LINE: <?= htmlspecialchars(mb_substr((string)($p['line_userid'] ?? ''), 0, 15)) ?>...
                                    </h4>
                                </div>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-900/40 p-3 rounded-2xl border border-slate-200/50 dark:border-slate-800 mb-4">
                                <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">นักเรียนในความดูแล</span>
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-graduation-cap text-sm"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <h5 class="font-bold text-xs text-slate-800 dark:text-white truncate">
                                            <?= htmlspecialchars(($p['Stu_pre'] ?? '') . ($p['Stu_name'] ?? '-') . ' ' . ($p['Stu_sur'] ?? '')) ?>
                                        </h5>
                                        <div class="flex items-center gap-2 mt-0.5 text-[9px] font-bold text-slate-400">
                                            <span>รหัส: <?= htmlspecialchars($p['student_id'] ?? '-') ?></span>
                                            <?php if (isset($p['Stu_major'])): ?>
                                                <span class="px-1.5 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-md">ม.<?= htmlspecialchars($p['Stu_major']) ?>/<?= htmlspecialchars($p['Stu_room'] ?? '') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800/50">
                            <span class="text-[9px] text-slate-400 font-bold"><?= !empty($p['created_at']) ? date('d/m/Y H:i', strtotime($p['created_at'])) : '-' ?></span>
                            <form action="line_monitor.php?action=unlink_parent" method="POST" onsubmit="return confirm('ยกเลิกการผูกบัญชีไลน์นี้?');" class="inline">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="px-3 py-1.5 bg-rose-100 dark:bg-rose-900/30 hover:bg-rose-500 text-rose-600 hover:text-white border border-rose-200 dark:border-rose-800 rounded-xl text-[10px] font-bold transition flex items-center gap-1.5">
                                    <i class="fas fa-unlink"></i> ยกเลิก
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-20 text-slate-400 glass-effect rounded-[2.5rem] border border-dashed border-slate-300 dark:border-slate-800">
                        <i class="fas fa-user-slash text-6xl mb-4 opacity-20 block"></i>
                        <span class="font-bold text-lg text-slate-700 dark:text-slate-300">ยังไม่มีผู้ปกครองลงทะเบียน</span>
                        <p class="text-xs text-slate-400 mt-1">จะปรากฏหลังจากจับคู่รหัสนักเรียนผ่านแชทไลน์สำเร็จ</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════ BOOTSTRAP MODAL: ADD TOKEN ═══════════════════ -->
<div class="modal fade" id="addTokenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-green-600 text-white">
                <h5 class="modal-title font-bold"><i class="fab fa-line mr-2"></i>เพิ่มกลุ่ม LINE Notify ใหม่</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="line_monitor.php?action=add_token" method="POST">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">ชื่อกลุ่ม / คำอธิบาย</label>
                        <input type="text" name="line_name" class="form-control rounded-xl" placeholder="เช่น ครูที่ปรึกษา ม.1/1" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-500 mb-1">ระดับชั้น</label>
                            <select name="line_class" class="form-control rounded-xl" required>
                                <?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>">ม.<?= $i ?></option><?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-500 mb-1">ห้อง (0 = ทุกห้อง)</label>
                            <input type="number" name="line_room" value="0" min="0" max="20" class="form-control rounded-xl" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">LINE Notify Token</label>
                        <input type="text" name="token" class="form-control rounded-xl font-monospace" placeholder="วาง Access Token ตรงนี้" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-xl" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success rounded-xl font-bold">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════ BOOTSTRAP MODAL: EDIT TOKEN ═══════════════════ -->
<div class="modal fade" id="editTokenModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-r from-amber-500 to-orange-600 text-white">
                <h5 class="modal-title font-bold"><i class="fas fa-edit mr-2"></i>แก้ไข LINE Notify</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="line_monitor.php?action=edit_token" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">ชื่อกลุ่ม / คำอธิบาย</label>
                        <input type="text" name="line_name" id="edit_line_name" class="form-control rounded-xl" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-500 mb-1">ระดับชั้น</label>
                            <select name="line_class" id="edit_line_class" class="form-control rounded-xl" required>
                                <?php for ($i = 1; $i <= 6; $i++): ?><option value="<?= $i ?>">ม.<?= $i ?></option><?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs font-bold text-slate-500 mb-1">ห้อง (0 = ทุกห้อง)</label>
                            <input type="number" name="line_room" id="edit_line_room" min="0" max="20" class="form-control rounded-xl" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">LINE Notify Token</label>
                        <input type="text" name="token" id="edit_token" class="form-control rounded-xl font-monospace" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-xl" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning rounded-xl font-bold text-white">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ═══════════════════ BOOTSTRAP MODAL: TEST NOTIFY ═══════════════════ -->
<div class="modal fade" id="testNotifyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-to-r from-emerald-500 to-green-600 text-white">
                <h5 class="modal-title font-bold"><i class="fas fa-paper-plane mr-2"></i>ทดสอบส่งข้อความแจ้งเตือน</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="testNotifyForm">
                <input type="hidden" name="id" id="test_id">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">กลุ่มเป้าหมาย</label>
                        <div class="flex items-center gap-2 bg-emerald-50 dark:bg-emerald-900/20 px-3 py-2 rounded-xl border border-emerald-200">
                            <i class="fab fa-line text-emerald-500"></i>
                            <span class="font-bold text-sm text-slate-800" id="test_group_name">-</span>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs font-bold text-slate-500 mb-1">ข้อความทดสอบ</label>
                        <textarea name="test_message" id="test_message" class="form-control rounded-xl" rows="3" required>ทดสอบแจ้งเตือนจากระบบ StdCare 🔔</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-xl" data-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-success rounded-xl font-bold"><i class="fas fa-paper-plane mr-1"></i>ยิงข้อความทดสอบ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Copy webhook URL
function copyWebhookUrl() {
    var el = document.getElementById('webhookUrlInput');
    el.select(); el.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(el.value).then(function() {
        Swal.fire({ title: 'คัดลอก URL เรียบร้อย!', icon: 'success', confirmButtonColor: '#10b981', timer: 1500 });
    });
}

// Toggle token visibility
function toggleToken(idx) {
    var f = document.getElementById('tokenField_' + idx);
    var e = document.getElementById('tokenEye_' + idx);
    if (f.type === 'password') { f.type = 'text'; e.className = 'fas fa-eye-slash'; }
    else { f.type = 'password'; e.className = 'fas fa-eye'; }
}

// Open Edit Modal (reads data-* attributes)
function openEditModal(btn) {
    $('#edit_id').val(btn.dataset.editId);
    $('#edit_line_name').val(btn.dataset.editName);
    $('#edit_line_class').val(btn.dataset.editClass);
    $('#edit_line_room').val(btn.dataset.editRoom);
    $('#edit_token').val(btn.dataset.editToken);
    $('#editTokenModal').modal('show');
}

// Open Test Modal (reads data-* attributes)
function openTestModal(btn) {
    $('#test_id').val(btn.dataset.testId);
    $('#test_group_name').text(btn.dataset.testName);
    $('#test_message').val('ทดสอบแจ้งเตือนจากระบบ StdCare 🔔');
    $('#testNotifyModal').modal('show');
}

// Confirm clear logs
function confirmClearLogs(e) {
    e.preventDefault();
    Swal.fire({
        title: 'ล้างประวัติ Webhook?',
        text: 'ข้อมูลที่ลบจะไม่สามารถกู้คืนได้',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'ใช่, ล้างทั้งหมด!',
        cancelButtonText: 'ยกเลิก'
    }).then(function(result) {
        if (result.isConfirmed) { e.target.submit(); }
    });
}

$(document).ready(function() {
    // Webhook Simulator AJAX
    $('#simulateForm').submit(function(e) {
        e.preventDefault();
        var $form = $(this), $btn = $form.find('button[type="submit"]'), orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังทดสอบ...');
        $('#consoleBox').removeClass('hidden');
        $('#consoleOutput').html('<span class="text-slate-600">// กำลังส่ง request...</span>');

        $.ajax({
            url: 'line_monitor.php?action=simulate', method: 'POST',
            data: $form.serialize(), dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(orig);
                var out = '';
                if (res.success) {
                    out += '<span class="text-emerald-400">[OK 200] Webhook Call Success!</span>\n';
                    out += '<span class="text-slate-500">URL: ' + res.webhook_url + '</span>\n';
                    out += '<span class="text-blue-400">' + JSON.stringify(res.payload, null, 2) + '</span>\n\n';
                    out += '<span class="text-emerald-300 font-bold">' + JSON.stringify(res.response, null, 2) + '</span>\n';
                    Swal.fire({ title: 'สำเร็จ!', icon: 'success', confirmButtonColor: '#10b981' }).then(function() { setTimeout(function() { location.reload(); }, 500); });
                } else {
                    out += '<span class="text-rose-500">[ERROR ' + (res.http_code || '500') + ']</span>\n';
                    out += '<span class="text-rose-400">' + res.message + '</span>\n';
                    Swal.fire({ title: 'ผิดพลาด', text: res.message, icon: 'error', confirmButtonColor: '#10b981' });
                }
                $('#consoleOutput').html(out);
            },
            error: function(xhr, status, error) {
                $btn.prop('disabled', false).html(orig);
                $('#consoleOutput').html('<span class="text-rose-500">[FATAL]</span>\n' + (xhr.responseText || error));
                Swal.fire({ title: 'เชื่อมต่อล้มเหลว', icon: 'error', confirmButtonColor: '#10b981' });
            }
        });
    });

    // Test Notify AJAX
    $('#testNotifyForm').submit(function(e) {
        e.preventDefault();
        var $form = $(this), $btn = $form.find('button[type="submit"]'), orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> กำลังส่ง...');

        $.ajax({
            url: 'line_monitor.php?action=test_notify', method: 'POST',
            data: $form.serialize(), dataType: 'json',
            success: function(res) {
                $btn.prop('disabled', false).html(orig);
                if (res.success) {
                    Swal.fire({ title: 'ส่งสำเร็จ!', text: 'ส่งข้อความเข้ากลุ่มแล้ว', icon: 'success', confirmButtonColor: '#10b981' });
                    $('#testNotifyModal').modal('hide');
                } else {
                    Swal.fire({ title: 'ส่งไม่สำเร็จ', text: res.message, icon: 'error', confirmButtonColor: '#10b981' });
                }
            },
            error: function() {
                $btn.prop('disabled', false).html(orig);
                Swal.fire({ title: 'เชื่อมต่อขัดข้อง', icon: 'error', confirmButtonColor: '#10b981' });
            }
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/admin_app.php';
?>
