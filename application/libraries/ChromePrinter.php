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
        $this->CI->load->config('chrome');                 // pakai section 'chrome'
        $this->conf = $this->CI->config->item('chrome') ?: [];
        log_message('debug', 'Chrome conf: '.json_encode($this->conf));
    }

    /**
     * Render HTML jadi PDF via Chrome Headless (CLI).
     *
     * @param string $html          HTML siap render (boleh tanpa <html>, akan dibungkus)
     * @param string $pdfDest       Full path tujuan file PDF
     * @param string $paper         'A4'|'A5'|'Letter'...
     * @param string $orientation   'portrait'|'landscape'
     * @return array                ['ok'=>true,'out'=><log teks>]
     */
    public function htmlToPdf(string $html, string $pdfDest, string $paper='A4', string $orientation='portrait'): array
    {
        $chrome = (string)($this->conf['bin'] ?? '');
        if (!$chrome || !is_file($chrome)) {
            throw new RuntimeException('Chrome tidak ditemukan di: '.$chrome);
        }

        // Argumen default aman; bisa ditambah via config/chrome.php
        $defaultArgs = [
            '--headless=new',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--print-to-pdf-no-header',
            '--force-device-scale-factor=1',
            // Bila layout berbasis px, viewport ini membantu 1:1; jika sudah mm penuh, boleh dihapus
            '--window-size=918,1188',
        ];
        $args = array_values(array_unique(array_merge(
            $defaultArgs,
            (array)($this->conf['args'] ?? [])
        )));
        $timeout = (int)($this->conf['timeout'] ?? 30);

        // Pastikan folder tujuan & tmp ada
        $this->ensureDir(dirname($pdfDest));
        $tmpDir  = FCPATH.'uploads/tmp';
        $this->ensureDir($tmpDir);
        $tmpHtml = $tmpDir.'/print_'.uniqid().'.html';

        // Bungkus HTML + base href + CSS injeksi MINIMAL (khusus print)
        $size = strtoupper($paper).' '.strtolower($orientation);
        $injectCss = <<<CSS
<style id="__print_fix__">
  @page { size: {$size}; margin: 0; }
  html,body{ margin:0; padding:0; -webkit-print-color-adjust:exact; print-color-adjust:exact; }

  /* CETAK SAJA */
  @media print {
    /* Stabilkan tabel */
    table{ display:table !important; width:100% !important; border-collapse:collapse !important; }
    thead{ display:table-header-group; } tfoot{ display:table-footer-group; }
    tr{ display:table-row !important; }
    th,td{ display:table-cell !important; box-sizing:border-box; min-width:0 !important; }

    /* === RATAKAN 2 KOLOM: Vendor & Purchase Payment === */
    /* 1) Jadikan keduanya float kiri, samakan top-offset, dan netralkan padding-top */
    #printNow div[style*="display:inline-block"][style*="width:48%"],
    #printNow div[style*="display:inline-block"][style*="width: 48%"]{
      float: left !important;
      width: 48% !important;
      vertical-align: top !important;
      margin-top: 15px !important;
      padding-top: 0 !important;
    }
    /* 2) Hapus margin-left 15px di kolom kanan agar tidak jatuh ke baris baru */
    #printNow div[style*="margin-left:15px"][style*="width:48%"],
    #printNow div[style*="margin-left: 15px"][style*="width:48%"],
    #printNow div[style*="margin-left:15px"][style*="width: 48%"],
    #printNow div[style*="margin-left: 15px"][style*="width: 48%"]{
      margin-left: 0 !important;
    }
    /* 3) Clear float setelah kedua kolom supaya layout berikutnya rapi */
    #printNow div[style*="width:100%"]::after{
      content:""; display:block; clear:both;
    }

    /* Sembunyikan elemen non-print jika ada */
    .no-print, .no-print * { display:none !important; }
  }
</style>

CSS;

        if (preg_match('~<head[^>]*>~i', $html)) {
            $wrapped = preg_replace('~(<head[^>]*>)~i', '$1'.$injectCss.'<base href="'.base_url().'">', $html, 1);
        } else {
            $wrapped = '<!doctype html><html><head><meta charset="utf-8">'.
                       '<base href="'.base_url().'">'.$injectCss.
                       '</head><body>'.$html.'</body></html>';
        }
        file_put_contents($tmpHtml, $wrapped);

        // Bangun command (quoting lintas OS)
        $src = $this->fileUrl($tmpHtml); // file:///C:/... atau file:///var/...
        $cmdParts   = [];
        $cmdParts[] = $this->q($chrome);
        foreach ($args as $a) { $cmdParts[] = $a; } // arg ini tidak butuh quoting tambahan
        $cmdParts[] = $this->q('--print-to-pdf='.$pdfDest);
        $cmdParts[] = $this->q($src);
        $cmd = implode(' ', $cmdParts).' 2>&1';

        // Eksekusi sederhana (stabil di shared host). Jangan paksa proc_open bila diblokir.
        $out = shell_exec($cmd);
        log_message('debug', 'Chrome CMD: '.$cmd);
        log_message('debug', 'Chrome OUT: '.$out);

        // Validasi hasil
        if (!is_file($pdfDest) || filesize($pdfDest) < 1024) {
            @unlink($tmpHtml);
            throw new RuntimeException('Gagal generate PDF via Chrome. Log: '.$out);
        }

        @unlink($tmpHtml);
        return ['ok'=>true, 'out'=>$out];
    }

    // ---------- Helpers ----------

    private function ensureDir(string $dir)
    {
        if (!is_dir($dir)) @mkdir($dir, 0775, true);
    }

    private function fileUrl(string $path): string
    {
        $real = str_replace('\\', '/', realpath($path));
        return (stripos(PHP_OS_FAMILY, 'Windows') !== false)
            ? 'file:///'.ltrim($real, '/')
            : 'file://'.$real;
    }

    /** Quote arg lintas OS: Windows → "double quotes", Unix → escapeshellarg() */
    private function q(string $s): string
    {
        if (stripos(PHP_OS_FAMILY, 'Windows') !== false) {
            // Hindari tanda kutip dalam string; jika ada, ganti dengan escaped
            return '"'.str_replace('"', '\"', $s).'"';
        }
        return escapeshellarg($s);
    }
}
