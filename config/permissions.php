<?php
/**
 * config/permissions.php
 * Hệ thống Phân quyền Tập trung (RBAC)
 */

$acl = [
    // [Trang] => [Danh sách Role được phép]
    'manage_products.php' => ['admin', 'sales'],
    'manage_orders.php' => ['admin', 'sales'],
    'manage_users.php' => ['admin'],
    'admin_dashboard.php' => ['admin', 'sales'],
    // Mặc định: Nếu trang không có trong danh sách này, mọi role đều được vào (Public/Common)
];

/**
 * Hàm kiểm tra quyền truy cập
 * @param string $page_url : Tên file hiện tại (ví dụ: 'add-outfit.php')
 * @param string $user_role : Role của người dùng hiện tại
 * @return bool : true nếu được phép, false nếu bị chặn
 */
function can_access($page_url, $user_role) {
    global $acl;
    
    // Nếu trang không được định nghĩa trong ACL, mặc định cho phép truy cập (Public)
    if (!isset($acl[$page_url])) {
        return true;
    }
    
    // Nếu trang có trong ACL, kiểm tra xem role có nằm trong mảng được phép không
    return in_array($user_role, $acl[$page_url]);
}
