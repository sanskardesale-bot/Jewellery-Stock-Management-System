<?php
require_once __DIR__ . '/config/app.php';
logoutUser();
redirect('/jeweltrack/login.php');
