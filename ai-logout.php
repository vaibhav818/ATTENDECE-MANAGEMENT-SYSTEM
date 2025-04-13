<?php
session_start();
session_destroy();
header("Location: ai-login.php");
exit();
?>