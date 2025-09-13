<?php defined('BASEPATH') OR exit('No direct script access allowed');

class HealthController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Matikan profiler jika aktif
        if (function_exists('profiler_disable')) { profiler_disable(); }

        // Tutup session agar tidak lock file session di ping
        if (session_status() === PHP_SESSION_ACTIVE) { session_write_close(); }
    }

    public function ping()
    {
        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_output(json_encode([
                'ok' => true,
                'serverTime' => round(microtime(true) * 1000) // waktu server (ms)
            ]));
    }
}
