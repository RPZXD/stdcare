
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

echo createNavItem('index.php', 'fas fa-home', 'หน้าหลัก');
echo createNavItem('statistics.php', 'fas fa-chart-bar ', 'สถิติการเข้าเรียน');
echo createNavItem('show_attendance.php', 'fas fa-credit-card ', 'SCANRFID');
echo createNavItem('annouce.php', 'fas fa-tasks', 'การดำเนินการ');
echo createNavItem('login.php', 'fas fa-sign-in-alt', 'ลงชื่อเข้าสู่ระบบ');
echo createNavItem('', 'fas fa-ticket-alt', 'ระบบกิจกรรม');

?>