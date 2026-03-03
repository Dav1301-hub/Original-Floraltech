<?php
$host = getenv('DB_HOST') ?: "localhost";
$db = getenv('DB_NAME') ?: "flores";
$user = getenv('DB_USER') ?: "root";
$pass = getenv('DB_PASS') ?: "";