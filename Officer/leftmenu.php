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
            <p class="text-white font-bold">ตำแหน่ง : เจ้าหน้าที่</p>
        </div>
    </li>';
}

echo createNavItemName(htmlspecialchars($setting->getImgProfile().$userData['Teach_photo']), htmlspecialchars($userData['Teach_name']));

// echo "<hr style='border: 1px solid #ffffff;'>";
echo "<br>";

// เมนูสำหรับเจ้าหน้าที่
$menuItems = [
    ['href' => 'index.php', 'icon' => 'fa-home', 'text' => 'หน้าหลัก'],
    ['href' => 'data_student.php', 'icon' => 'fa-user-graduate', 'text' => 'ข้อมูลนักเรียน'],
    ['href' => 'data_teacher.php', 'icon' => 'fa-chalkboard-teacher', 'text' => 'ครูและบุคลากร'],
    ['href' => 'data_parent.php', 'icon' => 'fa-users', 'text' => 'ข้อมูลผู้ปกครอง'],
    ['href' => 'data_behavior.php', 'icon' => 'fa-frown', 'text' => 'หักคะแนนพฤติกรรม'],
    ['href' => 'rfid.php', 'icon' => 'fa-credit-card', 'text' => 'จัดการ rfid'],
    ['href' => 'report.php', 'icon' => 'fa-file-alt', 'text' => 'รายงานข้อมูล'],
    ['href' => '../logout.php', 'icon' => 'fa-sign-out-alt', 'text' => 'ออกจากระบบ'],
];

foreach ($menuItems as $item) {
    echo createNavItem($item['href'], $item['icon'], $item['text']);
}

?>