<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MaintenanceModel extends CI_Model
{
    /** Hitung baris per tabel */
    public function countTables(array $tables)
    {
        $out = [];
        foreach ($tables as $t) {
            try {
                $q = $this->db->query("SELECT COUNT(*) AS c FROM `{$t}`");
                $out[$t] = (int) $q->row('c');
            } catch (\Throwable $e) {
                log_message('error', 'countTables error for '.$t.' : '.$e->getMessage());
                $out[$t] = null; // unknown
            }
        }
        return $out;
    }

    /**
     * Truncate urutan tabel. 
     * Catatan: TRUNCATE adalah DDL dan auto-commit. Jika gagal (FK), fallback ke DELETE + reset AI.
     */
    public function truncateSequence(array $tables)
    {
        $result = [];

        // Matikan FK agar TRUNCATE tidak mental (akan dikembalikan lagi)
        try { $this->db->query('SET FOREIGN_KEY_CHECKS=0'); } catch (\Throwable $e) {}

        foreach ($tables as $t) {
            $ok = false; $err = null;

            try {
                $this->db->query("TRUNCATE TABLE `{$t}`");
                $ok = true;
            } catch (\Throwable $e) {
                // Fallback: DELETE + reset AI
                try {
                    $this->db->query("DELETE FROM `{$t}`");
                    try { $this->db->query("ALTER TABLE `{$t}` AUTO_INCREMENT = 1"); } catch (\Throwable $e2) {}
                    $ok = true;
                } catch (\Throwable $e3) {
                    $err = $e3->getMessage();
                    log_message('error', 'truncateSequence gagal di '.$t.' : '.$err);
                }
            }

            $result[$t] = ['ok' => $ok, 'error' => $err];
        }

        try { $this->db->query('SET FOREIGN_KEY_CHECKS=1'); } catch (\Throwable $e) {}

        return $result;
    }
}
