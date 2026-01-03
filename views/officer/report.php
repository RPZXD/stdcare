<?php
/**
 * View: Report Summary (Officer)
 * Modern UI with Tailwind CSS, Glassmorphism & Responsive Design
 */
ob_start();
?>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .glass-effect {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .dark .glass-effect {
        background: rgba(30, 41, 59, 0.8);
        border-color: rgba(255, 255, 255, 0.05);
    }

    /* Mobile Table to Card Transformation */
    @media (max-width: 768px) {
        .report-content table, 
        .report-content thead, 
        .report-content tbody, 
        .report-content th, 
        .report-content td, 
        .report-content tr { 
            display: block; 
        }
        .report-content thead tr { 
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        .report-content tr { 
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 2rem;
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .report-content td { 
            border: none;
            position: relative;
            padding-left: 45%; 
            padding-bottom: 0.75rem;
            text-align: right;
            font-size: 0.875rem;
        }
        .report-content td:before { 
            position: absolute;
            top: 0;
            left: 1rem;
            width: 40%; 
            padding-right: 10px; 
            white-space: nowrap;
            text-align: left;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 0.65rem;
            color: #64748b;
            content: attr(data-label);
        }
        .report-content td:last-child {
            padding-bottom: 0;
        }
    }

    /* Printing Enhancements */
    @media print {
        @page { size: auto; margin: 1.5cm; }
        body { background: white !important; font-size: 12pt; }
        .no-print, 
        .tab-transition, 
        button, 
        form, 
        #lateForm,
        .no-print-essential { 
            display: none !important; 
        }
        
        .glass-effect {
            background: transparent !important;
            backdrop-filter: none !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Force Table View and reset card styles */
        .report-content table { 
            display: table !important; 
            width: 100% !important; 
            border-collapse: collapse !important;
            margin-top: 1cm !important;
        }
        .report-content thead { display: table-header-group !important; }
        .report-content tr { 
            display: table-row !important; 
            background: transparent !important; 
            margin: 0 !important; 
            box-shadow: none !important;
            border-bottom: 1px solid #ddd !important;
        }
        .report-content td, .report-content th { 
            display: table-cell !important; 
            padding: 8px !important; 
            text-align: inherit !important;
            border: 1px solid #eee !important;
            font-size: 10pt !important;
        }
        .report-content td:before { display: none !important; }
        .report-content td:last-child { padding-bottom: 8px !important; }

        /* Print Header - Visible only when printing */
        #print-header { 
            display: block !important; 
            text-align: center;
            margin-bottom: 2cm;
            border-bottom: 2px solid #333;
            padding-bottom: 0.5cm;
        }

        .report-content .shadow-sm, 
        .report-content .shadow-lg, 
        .report-content .shadow-2xl {
            box-shadow: none !important;
        }

        .report-content .rounded-2xl,
        .report-content .rounded-3xl,
        .report-content .rounded-[2rem],
        .report-content .rounded-[2.5rem] {
            border-radius: 0 !important;
        }
    }
    
    #print-header { display: none; }
</style>

<div class="max-w-[1600px] mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <!-- Print Header (Hidden on screen) -->
    <div id="print-header">
        <h1 class="text-2xl font-bold uppercase tracking-widest"><?php echo $global['nameschool']; ?></h1>
        <h2 class="text-xl font-bold mt-2">รายงานสรุปข้อมูลนักเรียน: <span id="print-report-name"><?php echo $pageTitle; ?></span></h2>
        <p class="text-sm italic mt-2">พิมพ์โดย: <?php echo $userData['Teach_name']; ?> | วันที่: <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <!-- Header Section -->
    <div class="mb-8 animate-fadeIn">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-10 relative overflow-hidden shadow-2xl border-t border-white/40">
            <div class="absolute top-0 right-0 w-80 h-80 bg-indigo-500/10 rounded-full -mr-40 -mt-40 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-rose-500/10 rounded-full -ml-40 -mb-40 blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="relative group">
                        <div class="absolute inset-0 bg-indigo-500 rounded-3xl blur-xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-rose-600 rounded-3xl flex items-center justify-center text-white shadow-xl relative transform group-hover:rotate-6 transition-transform">
                            <i class="fas fa-chart-pie text-3xl"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight leading-none">
                            <span class="text-indigo-600 italic">รายงานสรุป</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 font-bold mt-2 uppercase tracking-widest text-[11px] italic">
                            Statistics & Comprehensive Reports Center
                        </p>
                    </div>
                </div>

                <!-- Print Action -->
                <button onclick="window.print()" class="px-8 py-3.5 bg-slate-900 dark:bg-slate-800 text-white rounded-2xl font-black text-sm shadow-xl hover:scale-105 active:scale-95 transition-all flex items-center gap-3 group no-print">
                    <i class="fas fa-print text-indigo-400 group-hover:text-indigo-300"></i> พิมพ์รายงาน
                </button>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="mb-10 animate-fadeIn relative z-40" style="animation-delay: 0.1s">
        <div class="flex flex-wrap items-center gap-3 no-print">
            <?php foreach ($mainTabs as [$key, $label, $color]): 
                $isActive = $tab === $key;
                $colors = [
                    'indigo' => ['bg' => 'bg-indigo-500', 'text' => 'text-indigo-600', 'light' => 'bg-indigo-50'],
                    'emerald' => ['bg' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'light' => 'bg-emerald-50'],
                    'rose' => ['bg' => 'bg-rose-500', 'text' => 'text-rose-600', 'light' => 'bg-rose-50'],
                    'pink' => ['bg' => 'bg-pink-500', 'text' => 'text-pink-600', 'light' => 'bg-pink-50'],
                ];
                $c = $colors[$color] ?? $colors['indigo'];
            ?>
                <a href="?tab=<?php echo $key; ?>" 
                   class="tab-transition px-6 py-3.5 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3 border shadow-sm
                   <?php echo $isActive 
                        ? "{$c['bg']} text-white border-transparent shadow-lg shadow-{$color}-500/20 scale-105" 
                        : "bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-{$color}-500" ?>">
                    <?php echo $label; ?>
                    <?php if($isActive): ?>
                        <span class="w-1.5 h-1.5 rounded-full bg-white opacity-50"></span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>

                <!-- More Tabs Dropdown -->
                <div class="relative no-print" id="more-tabs-dropdown">
                    <button id="more-tabs-btn" class="px-6 py-3.5 bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-800 rounded-2xl font-black text-xs uppercase tracking-widest flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
                        รายงานเพิ่มเติม
                        <i class="fas fa-chevron-down text-[10px] transition-transform" id="more-tabs-icon"></i>
                    </button>
                    <div id="more-tabs-menu" class="absolute left-0 mt-3 w-72 bg-white dark:bg-slate-900 rounded-[2rem] shadow-2xl border border-slate-100 dark:border-slate-800 p-3 z-[100] hidden">
                        <div class="grid grid-cols-1 gap-1 max-h-[60vh] overflow-y-auto">
                            <?php foreach ($moreTabs as [$key, $label, $color]): 
                                $isActive = $tab === $key;
                            ?>
                                <a href="?tab=<?php echo $key; ?>" 
                                   class="px-4 py-3 rounded-xl font-bold text-[13px] flex items-center justify-between transition-all
                                   <?php echo $isActive 
                                        ? "bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400" 
                                        : "text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-indigo-500" ?>">
                                    <?php echo $label; ?>
                                    <?php if($isActive): ?>
                                        <i class="fas fa-check-circle text-xs"></i>
                                    <?php endif; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <!-- Report Content Container -->
    <div class="animate-fadeIn relative z-10" style="animation-delay: 0.2s">
        <div class="glass-effect rounded-[2.5rem] p-8 md:p-12 shadow-2xl border border-white/50 dark:border-slate-700/50 report-content">
            <?php
            // Include target report file
            if (isset($tabFiles[$tab]) && file_exists(__DIR__ . '/../../officer/' . $tabFiles[$tab])) {
                include(__DIR__ . '/../../officer/' . $tabFiles[$tab]);
            } else {
                echo '
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-24 h-24 bg-slate-50 dark:bg-slate-900 rounded-full flex items-center justify-center text-slate-300 mb-6">
                        <i class="fas fa-folder-open text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white">ไม่พบรายงานที่เลือก</h3>
                    <p class="text-sm text-slate-400 mt-2 font-bold italic">โปรดเลือกประเภทรายงานจากรายการด้านบน</p>
                </div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Helper to extract clean labels from headers
    function updateMobileLabels() {
        $('.report-content table:not(.no-card-mobile)').each(function() {
            const $table = $(this);
            const $thead = $table.find('thead');
            if ($thead.length === 0) return;

            // Simple column mapping for card labels
            const lastHeaderRow = $thead.find('tr').last();
            const labels = [];
            lastHeaderRow.find('th').each(function() {
                labels.push($(this).text().trim());
            });

            $table.find('tbody tr').each(function() {
                $(this).find('td').each(function(index) {
                    if (labels[index]) {
                        $(this).attr('data-label', labels[index]);
                    }
                });
            });
        });
    }

    // Custom Print Function
    window.printReport = function() {
        // Try to find the title of the current active tab or report
        const activeTab = $('a.bg-indigo-500').first().text().trim() || 
                         $('.report-content h2').first().text().trim() || 
                         'รายงานสรุป';
        $('#print-report-name').text(activeTab);
        window.print();
    };

    // Replace the button trigger
    $('.no-print button[onclick="window.print()"]').attr('onclick', 'printReport()');

    // Run on load and after AJAX content is updated
    updateMobileLabels();
    
    // Monitor for changes in report-content
    const observer = new MutationObserver(updateMobileLabels);
    const targetNode = document.querySelector('.report-content');
    if (targetNode) observer.observe(targetNode, { childList: true, subtree: true });

    // Dropdown Toggle for More Tabs
    const $dropdownBtn = $('#more-tabs-btn');
    const $dropdownMenu = $('#more-tabs-menu');
    const $dropdownIcon = $('#more-tabs-icon');

    $dropdownBtn.on('click', function(e) {
        e.stopPropagation();
        const isHidden = $dropdownMenu.hasClass('hidden');
        if (isHidden) {
            $dropdownMenu.removeClass('hidden').addClass('animate-fadeIn');
            $dropdownIcon.addClass('rotate-180');
        } else {
            $dropdownMenu.addClass('hidden').removeClass('animate-fadeIn');
            $dropdownIcon.removeClass('rotate-180');
        }
    });

    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#more-tabs-dropdown').length) {
            $dropdownMenu.addClass('hidden');
            $dropdownIcon.removeClass('rotate-180');
        }
    });
});
</script>


<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/officer_app.php';
?>
