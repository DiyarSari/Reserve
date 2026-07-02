<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

session_unset();
session_destroy();
redirect('../views/login.php');
