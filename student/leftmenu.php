<?php
function createNavItem($href, $iconClass, $text) {
    return '
    <li class="nav-item">
        <a href="' . htmlspecialchars($href) . '" class="nav-link">
            <i class="nav-icon fas ' . htmlspecialchars($iconClass) . '"></i>
            <p>' . htmlspecialchars($text) . '</p>
        </a>
    </li>';
}

function createNavItemName($avatar, $text) {
    return '
    <li class="nav-item">
        <div class="nav-link text-center">
            <img src="' . $avatar .'" alt="User Avatar" class="user-avatar rounded-full w-28 h-28 mx-auto">
        </div>
        <div class="nav-link text-center">
            <p class="text-white font-bold">'. $text . '</p>
        </div>
        <div class="nav-link text-center">
            <p class="text-white font-bold">ตำแหน่ง : นักเรียน</p>
        </div>
    </li>';
}

function createNavSubMenu($iconClass, $text, $subItems) {
    $html = '
    <li class="nav-item has-treeview">
        <a href="#" class="nav-link">
            <i class="nav-icon fas ' . htmlspecialchars($iconClass) . '"></i>
            <p>
                ' . htmlspecialchars($text) . '
                <i class="right fas fa-angle-left"></i>
            </p>
        </a>
        <ul class="nav nav-treeview">';
    foreach ($subItems as $sub) {
        $html .= '
            <li class="nav-item">
                <a href="' . htmlspecialchars($sub['href']) . '" class="nav-link">
                    <i class="far fa-circle nav-icon"></i>
                    <p>' . htmlspecialchars($sub['text']) . '</p>
                </a>
            </li>';
    }
    $html .= '
        </ul>
    </li>';
    return $html;
}

echo createNavItemName(htmlspecialchars($setting->getImgProfileStudent().$student['Stu_picture']), htmlspecialchars($student['Stu_name'] . ' ' . $student['Stu_sur']));

// echo "<hr style='border: 1px solid #ffffff;'>";
echo "<br>";

$menuItems = [
    ['href' => 'index.php', 'icon' => 'fas fa-home', 'text' => 'หน้าหลัก'],
    ['href' => 'std_information.php', 'icon' => 'fas fa-user-graduate', 'text' => 'ข้อมูลนักเรียน'], // เพิ่มเป็นเมนูหลัก
    // Sub-menu for "ข้อมูล" (ลบ 'ข้อมูลนักเรียน' ออก)
    ['submenu' => true, 'icon' => 'fas fa-database', 'text' => 'ข้อมูล', 'items' => [
        ['href' => 'std_checktime.php', 'text' => 'ข้อมูลเวลาเรียน'],
        ['href' => 'std_roomdata.php', 'text' => 'ข้อมูลห้องเรียน'],
        ['href' => 'std_behavior.php', 'text' => 'คะแนนพฤติกรรม'],
        ['href' => 'std_search_data.php', 'text' => 'ค้นหาข้อมูล'],
    ]],
    // Sub-menu for "บันทึก"
    ['submenu' => true, 'icon' => 'fas fa-plus-circle', 'text' => 'บันทึก', 'items' => [
        ['href' => 'std_visit_home.php', 'text' => 'บันทึกเยี่ยมบ้าน'],
        ['href' => 'std_sdq.php', 'text' => 'บันทึก SDQ'],
        ['href' => 'std_eq.php', 'text' => 'บันทึก EQ'],
        ['href' => 'std_screen11.php', 'text' => 'บันทึกแบบคัดกรองนักเรียน 11 ด้าน'],
        ]],
    ['href' => '../logout.php', 'icon' => 'fas fa-sign-out-alt', 'text' => 'ออกจากระบบ']
];

foreach ($menuItems as $item) {
    if (isset($item['submenu']) && $item['submenu'] === true) {
        echo createNavSubMenu($item['icon'], $item['text'], $item['items']);
    } else {
        echo createNavItem($item['href'], $item['icon'], $item['text']);
    }
}

?>