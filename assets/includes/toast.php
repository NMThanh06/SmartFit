<?php
function showToast($message, $type = 'success', $redirect = null) {
    $color = ($type === 'success') ? '#4CAF50' : '#f44336';
    $icon  = ($type === 'success') ? '✅' : '';
    $redirect_js = $redirect ? "window.location.href = '$redirect';" : 'window.history.back();';

    echo '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { margin:0; padding:0; font-family:Arial,sans-serif; background:#f1f1f1; }
            .toast {
                position: fixed; top:20px; right:20px; min-width:250px; padding:16px 24px;
                background: ' . $color . '; color:white; border-radius:6px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size:15px; font-weight:500;
                z-index:9999; animation: slideIn 0.3s ease, fadeOut 0.5s 2.5s forwards;
            }
            @keyframes slideIn { from { right:-100%; opacity:0; } to { right:20px; opacity:1; } }
            @keyframes fadeOut { to { opacity:0; visibility:hidden; } }
        </style>
    </head>
    <body>
        <div class="toast">' . $icon . ' ' . $message . '</div>
        <script> setTimeout(function() { ' . $redirect_js . ' }, 2000); </script>
    </body>
    </html>';
    exit;
}
?>