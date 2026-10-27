<?php
require_once __DIR__ . '/config/app.php';
redirect(isLoggedIn() ? '/jeweltrack/dashboard.php' : '/jeweltrack/login.php');
