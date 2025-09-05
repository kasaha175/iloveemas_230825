<?php defined('BASEPATH') OR exit('No direct script access allowed');

$config['chrome'] = [
    'bin'     => 'C:\Program Files\Google\Chrome\Application\chrome.exe', // <— path Chrome
    'timeout' => 25,
    'args'    => [
        '--headless=new',
        '--disable-gpu',
        '--no-sandbox',
        '--disable-dev-shm-usage',
        '--run-all-compositor-stages-before-draw',
        '--virtual-time-budget=10000',
        '--print-to-pdf-no-header',
        '--force-device-scale-factor=1'
    ],
];