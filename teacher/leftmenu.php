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
    ['href' => 'index.php', 'icon' => 'fa-home', 'text' => 'หน้าหลัก'],
    ['href' => 'check_student.php', 'icon' => 'fa-clock', 'text' => 'บันทึกเวลาเรียน'],
    ['href' => 'home_room.php', 'icon' => 'fa-home', 'text' => 'บันทึกโฮมรูม'], // ใช้ icon ที่เหมาะสมกับโฮมรูม
    ['href' => 'take_care.php', 'icon' => 'fa-hand-holding-heart', 'text' => 'ระบบการดูแล'],
    ['href' => 'behavior.php', 'icon' => 'fa-star', 'text' => 'คะแนนพฤติกรรม'],
    ['href' => 'data_student.php', 'icon' => 'fa-user-graduate', 'text' => 'ข้อมูลนักเรียน'],
    ['href' => 'data_parent.php', 'icon' => 'fa-user-friends', 'text' => 'ข้อมูลผู้ปกครอง'],
    ['href' => 'search_data.php', 'icon' => 'fa-search', 'text' => 'ค้นหาข้อมูล'],
    ['href' => 'report.php', 'icon' => 'fa-file-alt', 'text' => 'รายงานข้อมูล'],
    ['href' => 'information.php', 'icon' => 'fa-chalkboard-teacher', 'text' => 'ข้อมูลครู'],
    ['href' => '../logout.php', 'icon' => 'fa-sign-out-alt', 'text' => 'ออกจากระบบ'],
];

foreach ($menuItems as $item) {
    echo createNavItem($item['href'], $item['icon'], $item['text']);
}

?>