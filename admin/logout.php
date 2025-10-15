<?php
require_once '../config.php';
unset($_SESSION['admin']);
header('Location: login.php');
exit;
