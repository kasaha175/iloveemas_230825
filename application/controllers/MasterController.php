<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MasterController extends CI_Controller
{
    /** payload umum ke view */
    protected $data = [];

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        // Models
        $this->load->model('UserModel');
        $this->load->model('MasterModel');
        $this->load->model('MaterialModel');
        $this->load->model('ConfigModel'); // <-- penting untuk tema

        // Libs + helper
        $this->load->library('cart');
        $this->load->helper(['url','form']);

        $this->data['title'] = '';
    }

    /** Pastikan login, siapkan data umum (title, userData, config, sidebar) */
    private function boot(string $title)
    {
        if ($this->session->userdata('authUser') !== true) {
            redirect(base_url()); // akan ke halaman login (HomeController@index)
            exit;
        }

        $idUser = $this->session->userdata('idUser');

        $this->data['title']    = $title;
        $this->data['userData'] = $this->UserModel->userDataById($idUser)->result();
        $this->data['config']   = $this->ConfigModel->getAllAssoc();

        // optional: jika layout Anda menampilkan sidebar
        $this->data['sidebar']  = $this->load->view('Sidebar', $this->data, true);
    }

    /** Render helper */
    private function render(string $view)
    {
        $this->data['content'] = $this->load->view($view, $this->data, true);
        $this->load->view('UserTemplate', $this->data);
    }

    /* =========================
     * MENU MASTER (relayout)
     * ========================= */
    public function master()
    {
        $this->boot('MASTER');
        // gunakan view baru yang modern/responsif
        $this->render('Master'); // <--- sebelumnya 'Master'
    }

    /* ========== CUSTOMER ========== */
    public function customer()
    {
        $auth = $this->session->userdata('authUser');
        if(!$auth) return redirect(base_url());
        $id = $this->session->userdata('idUser');

        $data['title']    = 'MASTER CUSTOMER';
        $data['userData'] = $this->UserModel->userDataById($id)->result();
        $data['config']   = $this->ConfigModel->getAllAssoc();        // <— agar BG, warna, logo pakai config
        $data['content']  = $this->load->view('MasterCustomer', $data, true);
        $this->load->view('UserTemplate', $data);
    }

    public function customer_dt()
    {
        if ($this->session->userdata('authUser') !== true) { show_404(); return; }

        // Ambil POST dgn aman (beri default)
        $draw   = (int) ($this->input->post('draw')   ?? 0);
        $start  = (int) ($this->input->post('start')  ?? 0);
        $length = (int) ($this->input->post('length') ?? 10);

        $postSearch = $this->input->post('search');
        $search = (is_array($postSearch) && isset($postSearch['value'])) ? $postSearch['value'] : '';

        $postOrder = $this->input->post('order');
        $order0    = (is_array($postOrder) && isset($postOrder[0])) ? $postOrder[0] : ['column'=>2,'dir'=>'asc'];

        $cols    = ['c_id', null, 'c_no_order','c_name','c_address','c_resident_address','c_phone','c_date_created','u_name'];
        $orderBy = isset($cols[$order0['column']]) ? $cols[$order0['column']] : 'c_no_order';
        $dir     = (isset($order0['dir']) && strtolower($order0['dir'])==='desc') ? 'DESC' : 'ASC';

        $res = $this->MasterModel->dtCustomers($start, $length, $search, $orderBy, $dir);

        $data = [];
        $no = $start;
        foreach ($res['rows'] as $r) {
            $no++;
            $aksi = '<a href="'.base_url('master/customer/'.$r->c_id.'/').'" class="btn btn-primary btn-circle btn-sm mr-1"><i class="fas fa-edit"></i></a>'
                . '<a href="'.base_url('master/delete-customer-process/'.$r->c_id.'/').'" class="btn btn-danger btn-circle btn-sm js-del"><i class="fas fa-trash"></i></a>';

            $data[] = [
                $no, $aksi,
                $r->c_no_order, $r->c_name, $r->c_address, $r->c_resident_address,
                $r->c_phone, $r->c_date_created, $r->u_name
            ];
        }

        $json = [
            'draw'            => $draw,
            'recordsTotal'    => (int) $res['total'],
            'recordsFiltered' => (int) $res['filtered'],
            'data'            => $data,
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        ];

        $this->output
            ->set_content_type('application/json','utf-8')
            ->set_output(json_encode($json, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES))
            ->_display();
        exit; // cegah output lain ikut terkirim
    }

    public function detailCustomer()
    {
        $this->boot('MASTER CUSTOMER DETAIL');
        $id = $this->uri->segment(3);
        $this->data['detail'] = $this->data['customerDetail'] = $this->MasterModel->customerDetail($id)->result();
        $this->render('CustomerDetail');
    }

    public function editCustomerProcess()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $idUser = $this->session->userdata('idUser');

        $idCustomer = $this->input->post('idCustomer');
        $data = [
            'c_name'            => strtoupper($this->input->post('name')),
            'c_id_number'       => strtoupper($this->input->post('idNumber')),
            'c_address'         => strtoupper($this->input->post('address')),
            'c_resident_address'=> strtoupper($this->input->post('resident_address')),
            'c_phone'           => $this->input->post('phone'),
            'c_u_id'            => $idUser,
            'c_no_order'        => $this->input->post('noOrder'),
        ];
        $this->MasterModel->editCustomerProces($data, $idCustomer);
        $this->session->set_userdata(['status'=>'success','message'=>'Edit customer is success!']);
        redirect(base_url("master/customer/$idCustomer/"));
    }

    public function deleteCustomerProcess()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $id = $this->uri->segment(3);
        $this->MasterModel->deleteCustomerProcess($id);
        $this->session->set_userdata(['status'=>'success','message'=>'Delete customer is success!']);
        redirect(base_url('master/customer/'));
    }

    /* ========== ARCHIVE BUY ========== */
    public function archive()
    {
        $this->boot('ARCHIVE');
        $this->render('Archive');
    }

    public function buy()
    {
        $this->boot('ARCHIVE BUY');

        $key  = $this->input->get('key');
        $type = $this->input->get('type');

        if (in_array($key, ["rti-au","rti-pt","rti-ag","rti-lm","rti-ru","rti-ta"], true)) {
            if ($key === 'rti-au') {
                if ($type === 'change') {
                    $this->data['data']    = $this->MasterModel->formulasData($key)->result();
                    return $this->render('ArchiveBuyKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_au');
                return $this->render('ArchiveBuyKey');
            }
            if ($key === 'rti-pt') {
                if ($type === 'change') {
                    $this->data['data']  = $this->MasterModel->formulasData($key)->result();
                    $this->data['data2'] = $this->MasterModel->formulasData('rti-pt-low')->result();
                    return $this->render('ArchiveBuyKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_pt');
                return $this->render('ArchiveBuyKey');
            }
            if ($key === 'rti-ag') {
                if ($type === 'change') {
                    $this->data['data']  = $this->MasterModel->formulasData($key)->result();
                    $this->data['data2'] = $this->MasterModel->formulasData('rti-ag-low')->result();
                    return $this->render('ArchiveBuyKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_ag');
                return $this->render('ArchiveBuyKey');
            }
            if ($key === 'rti-ru') {
                if ($type === 'change') {
                    $this->data['data']  = $this->MasterModel->formulasData($key)->result();
                    $this->data['data2'] = $this->MasterModel->formulasData('rti-ru-low')->result();
                    return $this->render('ArchiveBuyKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_ru');
                return $this->render('ArchiveBuyKey');
            }

            // default
            if ($type === 'change') {
                $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                return $this->render('ArchiveBuyKeyChange');
            }
            $parameter           = 'f_' . str_replace('-', '_', $key);
            $this->data['value'] = $this->MaterialModel->formulaData()->row($parameter);
            return $this->render('ArchiveBuyKey');
        }

        $this->render('ArchiveBuy');
    }

    public function buySave()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());

        $key   = $this->input->get('key');
        $value = $this->input->get('value');
        $type  = $this->input->get('type');

        if (in_array($key, ["rti-au","rti-pt","rti-ag","rti-lm","rti-ru","rti-ta"], true)) {
            if ($key === 'rti-au') {
                $parameter = 'f_' . str_replace('-', '_', $key);
                if ($type === 'change') {
                    $data = [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),'c'=>$this->input->get('c'),
                        'd'=>$this->input->get('d'),'e'=>$this->input->get('e'),'f'=>$this->input->get('f'),
                        'g'=>$this->input->get('g'),'h'=>$this->input->get('h'),
                        'gb_99'=>$this->input->get('gb_99'),'gb_99_9'=>$this->input->get('gb_99_9'),
                        'potongan_lm'=>json_encode($this->input->get('potongan_lm')),
                    ];
                    $this->MasterModel->formulasUpdate($key, $data);
                    return redirect(base_url("archive/buy/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                $this->MaterialModel->formulaUpdate('f_rti_au_sell', $value);
                return redirect(base_url("archive/buy/?key=$key"));
            }

            if ($key === 'rti-ag') {
                $parameter = 'f_' . str_replace('-', '_', $key);
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),
                        'c'=>$this->input->get('c'),'d'=>$this->input->get('d'),'e'=>$this->input->get('e')
                    ]);
                    $this->MasterModel->formulasUpdate('rti-ag-low', [
                        'a'=>$this->input->get('aa'),'b'=>$this->input->get('bb'),
                        'c'=>$this->input->get('cc'),'d'=>$this->input->get('dd'),'e'=>$this->input->get('ee')
                    ]);
                    return redirect(base_url("archive/buy/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                $this->MaterialModel->formulaUpdate('f_rti_ag_sell', $value);
                return redirect(base_url("archive/buy/?key=$key"));
            }

            if ($key === 'rti-pt') {
                $parameter = 'f_' . str_replace('-', '_', $key);
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),
                        'c'=>$this->input->get('c'),'d'=>$this->input->get('d'),'e'=>$this->input->get('e')
                    ]);
                    $this->MasterModel->formulasUpdate('rti-pt-low', [
                        'a'=>$this->input->get('aa'),'b'=>$this->input->get('bb'),
                        'c'=>$this->input->get('cc'),'d'=>$this->input->get('dd'),'e'=>$this->input->get('ee')
                    ]);
                    return redirect(base_url("archive/buy/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                return redirect(base_url("archive/buy/?key=$key"));
            }

            if ($key === 'rti-ru') {
                $parameter = 'f_' . str_replace('-', '_', $key);
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, ['a'=>$this->input->get('a')]);
                    $this->MasterModel->formulasUpdate('rti-ru-low', ['a'=>$this->input->get('aa')]);
                    return redirect(base_url("archive/buy/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                return redirect(base_url("archive/buy/?key=$key"));
            }

            // default
            $parameter = 'f_' . str_replace('-', '_', $key);
            if ($type === 'change') {
                $this->MasterModel->formulasUpdate($key, ['a'=>$this->input->get('a')]);
                return redirect(base_url("archive/buy/?key=$key&type=change"));
            }
            $this->MaterialModel->formulaUpdate($parameter, $value);
            return redirect(base_url("archive/buy/?key=$key"));
        }

        // fallback
        $this->boot('ARCHIVE BUY');
        $this->render('ArchiveBuy');
    }

    /* ========== ARCHIVE SELL ========== */
    public function sell()
    {
        $this->boot('ARCHIVE SELL');

        $key  = $this->input->get('key');
        $type = $this->input->get('type');

        if (in_array($key, ["lm","material-au","material-ag","material-ubs"], true)) {
            if ($key === 'lm') {
                if ($type === 'change') {
                    $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                    return $this->render('ArchiveSellKeyChange');
                }
                $f = $this->MaterialModel->formulaData();
                $this->data["f_nol5"]     = $f->row("f_nol5");
                $this->data["f_1"]        = $f->row("f_1");
                $this->data["f_2"]        = $f->row("f_2");
                $this->data["f_2_coma_5"] = $f->row("f_2_coma_5");
                $this->data["f_3"]        = $f->row("f_3");
                $this->data["f_5"]        = $f->row("f_5");
                $this->data["f_10"]       = $f->row("f_10");
                $this->data["f_25"]       = $f->row("f_25");
                $this->data["f_50"]       = $f->row("f_50");
                $this->data["f_100"]      = $f->row("f_100");
                $this->data["f_250"]      = $f->row("f_250");
                $this->data["f_500"]      = $f->row("f_500");
                $this->data["f_1000"]     = $f->row("f_1000");
                return $this->render('ArchiveSellKey');
            }
            if ($key === 'material-au') {
                if ($type === 'change') {
                    $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                    return $this->render('ArchiveSellKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_au_sell');
                return $this->render('ArchiveSellKey');
            }
            if ($key === 'material-ag') {
                if ($type === 'change') {
                    $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                    return $this->render('ArchiveSellKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_rti_ag_sell');
                return $this->render('ArchiveSellKey');
            }
            if ($key === 'material-ubs') {
                if ($type === 'change') {
                    $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                    return $this->render('ArchiveSellKeyChange');
                }
                $this->data['value'] = $this->MaterialModel->formulaData()->row('f_material_ubs_sell');
                return $this->render('ArchiveSellKey');
            }

            // default:
            if ($type === 'change') {
                $this->data['data'] = $this->MasterModel->formulasData($key)->result();
                return $this->render('ArchiveSellKeyChange');
            }
            $parameter           = 'f_' . str_replace('-', '_', $key);
            $this->data['value'] = $this->MaterialModel->formulaData()->row($parameter);
            return $this->render('ArchiveSellKey');
        }

        $this->render('ArchiveSell');
    }

    public function sellSave()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());

        $key   = $this->input->get('key');
        $value = $this->input->get('value');
        $type  = $this->input->get('type');

        if (in_array($key, ["lm","material-au","material-ag","material-ubs"], true)) {
            if ($key === 'lm') {
                if ($type === 'change') {
                    $data = [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),'c'=>$this->input->get('c'),
                        'd'=>$this->input->get('d'),'e'=>$this->input->get('e'),'f'=>$this->input->get('f'),
                        'g'=>$this->input->get('g'),'h'=>$this->input->get('h'),
                        'potongan_lm'=>json_encode($this->input->get('potongan_lm')),
                    ];
                    $this->MasterModel->formulasUpdate($key,$data);
                    return redirect(base_url("archive/sell/?key=$key&type=change"));
                }
                $data = [
                    "f_nol5"=>$this->input->get("f_nol5"),"f_1"=>$this->input->get("f_1"),
                    "f_2"=>$this->input->get("f_2"),"f_3"=>$this->input->get("f_3"),
                    "f_2_coma_5"=>$this->input->get("f_2_coma_5"),
                    "f_5"=>$this->input->get("f_5"),"f_10"=>$this->input->get("f_10"),
                    "f_25"=>$this->input->get("f_25"),"f_50"=>$this->input->get("f_50"),
                    "f_100"=>$this->input->get("f_100"),"f_250"=>$this->input->get("f_250"),
                    "f_500"=>$this->input->get("f_500"),"f_1000"=>$this->input->get("f_1000"),
                ];
                $this->MaterialModel->formulaUpdateArray($data);
                return redirect(base_url("archive/sell/?key=$key"));
            }

            if ($key === 'material-au') {
                $parameter = 'f_rti_au_sell';
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),'c'=>$this->input->get('c'),
                        'd'=>$this->input->get('d'),'e'=>$this->input->get('e'),
                        'f'=>$this->input->get('f'),'g'=>$this->input->get('g')
                    ]);
                    return redirect(base_url("archive/sell/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                return redirect(base_url("archive/sell/?key=$key"));
            }

            if ($key === 'material-ag') {
                $parameter = 'f_rti_ag_sell';
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, [
                        'a'=>$this->input->get('a'),'b'=>$this->input->get('b'),
                        'c'=>$this->input->get('c'),'d'=>$this->input->get('d'),'e'=>$this->input->get('e')
                    ]);
                    return redirect(base_url("archive/sell/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                return redirect(base_url("archive/sell/?key=$key"));
            }

            if ($key === 'material-ubs') {
                $parameter = 'f_material_ubs_sell';
                if ($type === 'change') {
                    $this->MasterModel->formulasUpdate($key, ['a'=>$this->input->get('a')]);
                    return redirect(base_url("archive/sell/?key=$key&type=change"));
                }
                $this->MaterialModel->formulaUpdate($parameter, $value);
                return redirect(base_url("archive/sell/?key=$key"));
            }
        }

        // fallback
        $this->boot('ARCHIVE SELL');
        $this->render('ArchiveSell');
    }

    /* ========== MEMO ========== */
    public function memo()
    {
        $this->boot('MASTER');
        $this->render('Memo');
    }

    public function addmemo()
    {
        $this->boot('MASTER');
        $this->render('addMemo');
    }

    public function saveMemo()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $this->db->insert('tb_memo', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Syarat & Ketentuan Berhasil Disimpan']);
        redirect(base_url('master/memo'));
    }

    public function detailMemo($idmemo = '')
    {
        $this->boot('MASTER');
        $this->db->where('tm_id', $idmemo);
        $this->data['memo'] = $this->db->get('tb_memo')->row();
        $this->render('editMemo');
    }

    public function saveUpdateMemo()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $this->db->where('tm_id', $datapost['id']);
        $this->db->update('tb_memo', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Syarat & Ketentuan Berhasil Disimpan']);
        redirect(base_url('master/memo'));
    }

    public function deleteMemo($idmemo = '')
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $this->db->where('tm_id', $idmemo)->delete('tb_memo');
        $this->session->set_userdata(['status'=>'success','message'=>'Memo Is Deleted!']);
        redirect(base_url('master/memo'));
    }

    /* ========== POTONGAN ========== */
    public function potongan()
    {
        $this->boot('MASTER');
        $this->render('Potongan');
    }

    public function addPotongan()
    {
        $this->boot('MASTER');
        $this->render('addPotongan');
    }

    public function savePotongan()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $datapost['dt']['status']     = 'ENABLE';
        $datapost['dt']['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('tb_potongan', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Potongan Berhasil Disimpan']);
        redirect(base_url('master/potongan'));
    }

    public function deletePotongan($idpotongan = '')
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $this->db->update('tb_potongan', ['status'=>'DISABLE'], ['id'=>$idpotongan]);
        $this->session->set_userdata(['status'=>'success','message'=>'Potongan Berhasil Dihapus']);
        redirect(base_url('master/memo'));
    }

    public function detailPotongan($idpotongan = '')
    {
        $this->boot('MASTER');
        $this->db->where('id', $idpotongan);
        $this->data['potongan'] = $this->db->get('tb_potongan')->row();
        $this->render('editPotongan');
    }

    public function saveUpdatePotongan()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $datapost['dt']['status']     = 'ENABLE';
        $datapost['dt']['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $datapost['id'])->update('tb_potongan', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Potongan Berhasil Disimpan']);
        redirect(base_url('master/potongan'));
    }

    /* ========== CABANG ========== */
    public function cabang()
    {
        $this->boot('MASTER');
        $this->render('Cabang');
    }

    public function addCabang()
    {
        $this->boot('MASTER');
        $this->render('addCabang');
    }

    public function saveCabang()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $datapost['dt']['status']     = 'ENABLE';
        $datapost['dt']['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('tb_cabang', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Cabang Berhasil Disimpan']);
        redirect(base_url('master/cabang'));
    }

    public function deleteCabang($idcabang = '')
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $this->db->update('tb_cabang', ['status'=>'DISABLE'], ['id'=>$idcabang]);
        $this->session->set_userdata(['status'=>'success','message'=>'Cabang Berhasil Dihapus']);
        redirect(base_url('master/memo'));
    }

    public function detailCabang($idcabang = '')
    {
        $this->boot('MASTER');
        $this->db->where('id', $idcabang);
        $this->data['cabang'] = $this->db->get('tb_cabang')->row();
        $this->render('editCabang');
    }

    public function saveUpdateCabang()
    {
        if ($this->session->userdata('authUser') !== true) return redirect(base_url());
        $datapost = $this->input->post();
        $datapost['dt']['status']     = 'ENABLE';
        $datapost['dt']['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $datapost['id'])->update('tb_cabang', $datapost['dt']);
        $this->session->set_userdata(['status'=>'success','message'=>'Cabang Berhasil Disimpan']);
        redirect(base_url('master/cabang'));
    }

    public function memo_dt()
    {
        if ($this->session->userdata('authUser') !== true) show_404();

        $draw   = (int) $this->input->post('draw');
        $start  = (int) $this->input->post('start');
        $length = (int) $this->input->post('length');
        $search = $this->input->post('search')['value'] ?? '';
        $order  = $this->input->post('order')[0] ?? ['column'=>2,'dir'=>'asc'];
        // index kolom di tabel: 0=No, 1=Aksi, 2=tm_value, 3=tm_priority
        $cols   = ['tm_id', null, 'tm_value', 'tm_priority'];
        $orderBy= $cols[$order['column']] ?? 'tm_priority';
        $dir    = strtolower($order['dir']) === 'desc' ? 'DESC' : 'ASC';

        $res = $this->MasterModel->dtMemos($start, $length, $search, $orderBy, $dir);

        $data = [];
        $no = $start;
        foreach ($res['rows'] as $r) {
            $no++;
            $aksi =
                '<a href="'.base_url('master/detailMemo/'.$r->tm_id.'/').'" '.
                'class="btn btn-primary btn-circle btn-sm mr-2" title="Detail"><i class="fas fa-edit"></i></a>'.
                '<a href="'.base_url('master/deleteMemo/'.$r->tm_id.'/').'" '.
                'class="btn btn-danger btn-circle btn-sm js-del" title="Hapus"><i class="fas fa-trash"></i></a>';

            $data[] = [
                $no,
                $aksi,
                $r->tm_value,
                $r->tm_priority
            ];
        }

        echo json_encode([
            'draw'            => $draw,
            'recordsTotal'    => $res['total'],
            'recordsFiltered' => $res['filtered'],
            'data'            => $data,
            // putar CSRF agar request berikutnya valid
            $this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
        ]);
        // tidak perlu exit; json_encode sudah terakhir
    }
}
