<?php
session_start();

// تدمير الجلسة
session_destroy();

// إعادة التوجيه لصفحة تسجيل الدخول
header('Location: /auth/login.php');
exit();
?>
