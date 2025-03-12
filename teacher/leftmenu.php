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
            <p class="text-white font-bold">ตำแหน่ง : ครู</p>
        </div>
    </li>';
}

echo createNavItemName(htmlspecialchars($setting->getImgProfile().$userData['Teach_photo']), htmlspecialchars($userData['Teach_name']));

// echo "<hr style='border: 1px solid #ffffff;'>";
echo "<br>";

$menuItems = [
    ['href' => 'index.php', 'icon' => 'fas fa-home', 'text' => 'หน้าหลัก'],
    ['href' => 'check_student.php', 'icon' => 'fas fa-check', 'text' => 'บันทึกเวลาเรียน'],
    ['href' => 'home_room.php', 'icon' => 'fas fa-plus-circle', 'text' => 'บันทึกโฮมรูม'],
    ['href' => 'take_care.php', 'icon' => 'fas fa-hand-holding-medical', 'text' => 'ระบบการดูแล'],
    ['href' => 'behavior.php', 'icon' => 'fas fa-child', 'text' => 'คะแนนพฤติกรรม'],
    ['href' => 'data_student.php', 'icon' => 'fas fa-users', 'text' => 'ข้อมูลนักเรียน'],
    ['href' => 'data_parent.php', 'icon' => 'fas fa-users', 'text' => 'ข้อมูลผู้ปกครอง'],
    ['href' => 'search_data.php', 'icon' => 'fas fa-search', 'text' => 'ค้นหาข้อมูล'],
    ['href' => 'report.php', 'icon' => 'fas fa-pen-square', 'text' => 'รายงานข้อมูล'],
    ['href' => 'information.php', 'icon' => 'fas fa-chalkboard-teacher', 'text' => 'ข้อมูลครู'],
    ['href' => '../logout.php', 'icon' => 'fas fa-sign-out-alt', 'text' => 'ออกจากระบบ'],
];

foreach ($menuItems as $item) {
    echo createNavItem($item['href'], $item['icon'], $item['text']);
}

?>