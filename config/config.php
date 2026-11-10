<?php
declare(strict_types=1);

const APP_NAME = 'SNW ART STUDIO';
const BASE_URL = 'http://localhost/SNW%20ART%20STUDIO';
const DB_HOST = '127.0.0.1';
const DB_NAME = 'snw_art_studio';
const DB_USER = 'root';
const DB_PASS = '';
const WHATSAPP_NUMBER = '94765237415';
const STUDIO_EMAIL = 'studio@snwartstudio.com';
const STUDIO_HOURS = 'Monday — Saturday: 09:00 AM — 06:00 PM (IST)';
const CURRENCY_SYMBOL = '$'; // Change to 'Rs. ' or 'LKR ' or '$' as needed

// Studio Bank Account Details (Payment Method)
const BANK_NAME = 'Commercial Bank of Ceylon';
const BANK_ACCOUNT_NAME = 'SNW ART STUDIO';
const BANK_ACCOUNT_NUMBER = '8001234567';
const BANK_BRANCH = 'Colombo Main Branch';
const BANK_SWIFT = 'CCEYLKKL';
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
