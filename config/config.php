<?php
declare(strict_types=1);

const APP_NAME = 'SNW ART STUDIO';
const BASE_URL = 'http://localhost/SNW%20ART%20STUDIO';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'snw_art_studio';
const DB_USER = 'root';
const DB_PASS = '';
const WHATSAPP_NUMBER = '94765237415';
date_default_timezone_set('Asia/Colombo');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/helpers.php';
