# StdCare UX/UI Refactor Skill

This skill defines the standards for refactoring the UX/UI in the StdCare project to ensure consistency across all views and roles.

## Core Pattern: Layout-View-Component

All views must follow the standard PHP output buffering pattern to inject content into a layout.

### 1. View Structure
Every view file (e.g., `views/admin/data_student.php`) should follow this template:

```php
<?php
/**
 * View: [Page Name]
 * Description: [Brief description]
 */
ob_start();
$pageTitle = "[Page Title]";
$activePage = "[sidebar_key]";
?>

<div class="animate-fadeIn">
    <!-- Page Header Component -->
    <?php 
    $headerData = [
        'title' => '[Main Title]',
        'subtitle' => '[Subtitle/English]',
        'icon' => 'fa-[icon-name]',
        'color' => '[color-class]', // e.g., indigo, rose, emerald
        'actions' => [
            ['id' => 'btnAction', 'icon' => 'fa-plus', 'text' => 'Action Text', 'color' => 'indigo']
        ]
    ];
    include __DIR__ . '/../components/ui_header.php'; 
    ?>

    <!-- Main Content Area -->
    <div class="glass-effect rounded-[2.5rem] p-8 shadow-xl border-t border-white/50">
        <!-- Content goes here -->
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/[role]_app.php';
?>
```

### 2. Standard CSS Classes
Use these consistent Tailwind classes for a premium feel:
- **Glassmorphism**: `glass-effect` (Defined in layout CSS).
- **Animations**: `animate-fadeIn`, `animate-slide-up`, `hover:scale-105 transition-all`.
- **Containers**: `rounded-[2rem]` or `rounded-[2.5rem]` for large cards.
- **Typography**: `font-black` for headers, `text-[10px] tracking-widest uppercase` for labels.

### 3. Layout Standardization
All role-specific layouts (e.g., `admin_app.php`, `teacher_app.php`) should be minimized to just session handling and variable configuration, then include `base_app.php`.

**Example Layout (`views/layouts/admin_app.php`):**
```php
<?php
session_start();
if (!isset($_SESSION['Admin_login'])) { header('Location: ../login.php'); exit; }
$userData = $_SESSION['admin_data'] ?? [];

$role = 'admin';
$themeColor = 'rose';
include __DIR__ . '/base_app.php';
?>
```

### 4. Shared Components
Always use shared components located in `views/components/` instead of hardcoding HTML:
- `ui_header.php`: For the top title and action buttons.
- `ui_stat_card.php`: For dashboard summary cards.
- `[role]_navbar.php` / `[role]_sidebar.php`: Managed via the layout.

## Refactoring Checklist
1. [ ] Wrap content in `ob_start()` and `$content = ob_get_clean()`.
2. [ ] Set `$pageTitle` and `$activePage` at the top of the view.
3. [ ] Replace hardcoded headers with `ui_header.php`.
4. [ ] Ensure `glass-effect` and consistent rounding are used for cards.
5. [ ] Minimize role-specific layouts to use `base_app.php`.
6. [ ] Update Sidebar/Navbar to reflect the active page using `$activePage`.
