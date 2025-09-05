<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChromePrinter
{
    /** @var CI_Controller */
    protected $CI;
    protected $conf;

    public function __construct()
    {
        $this->CI =& get_instance();

        // MUDAH & AMAN: load tanpa section (true)
        $this->CI->load->config('chrome');                 // <— perbaiki di sini (hapus "true")
        $this->conf = $this->CI->config->item('chrome') ?: [];

        log_message('debug', 'Chrome conf: '.json_encode($this->conf));
    }

    /**
     * Render HTML jadi PDF via Chrome Headless.
     *
     * @param string $html      HTML siap render (tanpa <html> boleh, akan dibungkus)
     * @param string $pdfDest   Full path tujuan (contoh: FCPATH.'uploads/prints/buy/2025/09/INV.pdf')
     * @param string $paper     'A4'|'A5'|'Letter' dll (dipakai di CSS @page)
     * @param string $orientation 'portrait'|'landscape'
     * @return array            ['ok'=>true,'out'=>log]
     */
    public function htmlToPdf(string $html, string $pdfDest, string $paper='A4', string $orientation='portrait'): array
    {
        $chrome = (string)($this->conf['bin'] ?? '');
        if (!$chrome || !is_file($chrome)) {
            throw new RuntimeException('Chrome tidak ditemukan di: '.$chrome);
        }

        $args = (array)($this->conf['args'] ?? []);
        $timeout = (int)($this->conf['timeout'] ?? 25);

        // Pastikan folder tujuan ada
        $this->ensureDir(dirname($pdfDest));

        // Simpan HTML sementara (file:///)
        $tmpDir = FCPATH.'uploads/tmp';
        $this->ensureDir($tmpDir);
        $tmpHtml = $tmpDir.'/print_'.uniqid().'.html';

        // Bungkus HTML → tambahkan base href supaya asset relatif (/assets/...) resolve ke base_url
        $size = strtoupper($paper).' '.strtolower($orientation);
        $wrapped = '<!doctype html><html><head><meta charset="utf-8">'.
            '<base href="'.base_url().'">'.
            '<style>@page{size: '.$size.'; margin:0} html,body{margin:0;padding:0}</style>'.
            '</head><body>'.$html.'</body></html>';

        file_put_contents($tmpHtml, $wrapped);

        // Bangun command
        // NB: pakai file:/// supaya Chrome load local HTML, asset lain akan ke base_url (HTTP) via <base>
        $src = 'file:///' . str_replace('\\', '/', realpath($tmpHtml));
        $cmdParts = [];
        $cmdParts[] = '"'.$chrome.'"';
        foreach ($args as $a) $cmdParts[] = escapeshellarg($a);
        $cmdParts[] = escapeshellarg('--print-to-pdf='.$pdfDest);
        $cmdParts[] = escapeshellarg($src);
        $cmd = implode(' ', $cmdParts) . ' 2>&1';
        log_message('debug', 'Chrome CMD: '.$cmd);

        // Eksekusi (blocking). Alternatif: proc_open dengan timeout manual.
        $out = shell_exec($cmd);
        log_message('debug', 'Chrome OUT: '.$out);

        // Validasi hasil
        if (!is_file($pdfDest) || filesize($pdfDest) < 1024) {
            @unlink($tmpHtml);
            throw new RuntimeException('Gagal generate PDF via Chrome. Log: '.$out);
        }

        // Bersihkan tmp
        @unlink($tmpHtml);

        return ['ok' => true, 'out' => $out];
    }

    private function ensureDir(string $dir)
    {
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
    }
}
