<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['login']['GET']             = 'AuthController/login';
$route['login-process']['POST']    = 'AuthController/loginProcess';
$route['login-process']['GET']     = 'AuthController/login';    // fallback anti 405

$route['logout-process']['POST']   = 'AuthController/logoutProcess'; // ✅ tambahkan ini
// (Opsional) jika ingin tetap izinkan GET juga:
$route['logout-process']['GET']    = 'AuthController/logoutProcess';

$route['dashboard']['GET']         = 'HomeController/dashboard';

$route['config']['GET']            = 'ConfigController/index';
$route['config']['POST']           = 'ConfigController/save';

$route['api/kpi-dashboard']['GET'] = 'HomeController/kpiDashboard';

/* ============================= TRANSACTION ============================= */
$route['transaction']                                = 'TransactionController';
$route['transaction-list']                           = 'TransactionController/list';
$route['transaction/getTransactions']                = 'TransactionController/getTransactions';
$route['transaction/redirect/(:any)']               = 'TransactionController/redirectTransaction/$1'; // no_order (string)
$route['transaction/delete-transaction/(:any)']     = 'TransactionController/deleteTransaction/$1';   // no_order (string)
$route['transaction/confirm-edit']                   = 'TransactionController/confirmEdit';
$route['transaction/updateLive']                     = 'TransactionController/updateLive';

$route['transaction/buy']                            = 'TransactionController/buy';
$route['transaction/buy/lm/select']                  = 'TransactionController/lm';
$route['transaction/buy/platinum/select']            = 'TransactionController/platinum';
$route['transaction/buy/paladium/select']            = 'TransactionController/paladium';
$route['transaction/buy/iridium/select']             = 'TransactionController/iridium';
$route['transaction/buy/rhodium/select']             = 'TransactionController/rhodium';
$route['transaction/buy/ruthenium/select']           = 'TransactionController/ruthenium';
$route['transaction/buy/silver/select']              = 'TransactionController/silver';
$route['transaction/buy/(:num)']                     = 'TransactionController/buyCart/$1';            // idMaterial
$route['transaction/buy-add-to-cart']                = 'TransactionController/buyAddToCart';
$route['transaction/buy-add-to-cart-reset']          = 'TransactionController/buyAddToCartReset';
$route['transaction/buy-checkout']                   = 'TransactionController/buyCheckout';
$route['transaction/buy-delete-transaction/(:num)']  = 'TransactionController/buyDeleteTransaction/$1';
$route['transaction/chart-destroy']                  = 'TransactionController/chartDestroy';

$route['transaction/sell']                           = 'TransactionController/sell';
$route['transaction/sell/(:num)']                    = 'TransactionController/sellCart/$1';           // idMaterial
$route['transaction/sell-add-to-cart']               = 'TransactionController/sellAddToCart';
$route['transaction/sell-add-to-cart-reset']         = 'TransactionController/sellAddToCartReset';
$route['transaction/sell-checkout']                  = 'TransactionController/sellCheckout';
$route['transaction/sell-delete-transaction/(:num)'] = 'TransactionController/sellDeleteTransaction/$1';

$route['transaction/updateAllStatus']                = 'TransactionController/updateAllStatus';
$route['transaction/select-customer/(:any)']         = 'TransactionController/selectCustomer/$1';
$route['transaction/new-customer']                   = 'TransactionController/newCustomer';
$route['transaction/new-customer-process']           = 'TransactionController/newCustomerProcess';

// === Simpan metadata print + generate & simpan PDF ===
$route['transaction/savePrint/(:any)/(:num)']['POST'] = 'TransactionController/savePrint/$1/$2';
// (opsional) izinkan GET juga agar kalau diketik manual tetap masuk lalu dikembalikan 405 oleh controllernya
$route['transaction/savePrint/(:any)/(:num)']['GET']  = 'TransactionController/savePrint/$1/$2';

// === Unduh file PDF yang sudah tersimpan ===
$route['transaction/print-file/(:any)/(:num)']['GET'] = 'TransactionController/printFile/$1/$2';

$route['diagnose/test-chrome'] = 'TransactionController/testChromePing';

/* =============================== ARCHIVE =============================== */
$route['archive']                = 'MasterController/archive';
$route['archive/buy']            = 'MasterController/buy';
$route['archive/buy/save']       = 'MasterController/buySave';
$route['archive/sell']           = 'MasterController/sell';
$route['archive/sell/save']      = 'MasterController/sellSave';

/* ================================ MASTER =============================== */
$route['master']                                 = 'MasterController/master';
$route['master/customer']                        = 'MasterController/customer';
$route['transaction/customers']                  = 'TransactionController/getCustomers';
$route['master/customer-dt']                     = 'MasterController/customer_dt';
$route['master/delete-customer-process/(:any)']  = 'MasterController/deleteCustomerProcess/$1';
$route['master/edit-customer-process']           = 'MasterController/editCustomerProcess';
$route['master/customer/(:any)']                 = 'MasterController/detailCustomer/$1';

$route['master/memo']                            = 'MasterController/memo';
$route['master/memo-dt']                         = 'MasterController/memo_dt';
$route['master/addMemo']                         = 'MasterController/addmemo';
$route['master/save-memo']                       = 'MasterController/saveMemo';
$route['master/detailMemo/(:any)']               = 'MasterController/detailMemo/$1';
$route['master/update-memo']                     = 'MasterController/saveUpdateMemo';
$route['master/deleteMemo/(:any)']               = 'MasterController/deleteMemo/$1';

$route['master/potongan']                        = 'MasterController/potongan';
$route['master/addpotongan']                     = 'MasterController/addPotongan';
$route['master/save-potongan']                   = 'MasterController/savePotongan';
$route['master/detailPotongan/(:any)']           = 'MasterController/detailPotongan/$1';
$route['master/save-update-potongan']            = 'MasterController/saveUpdatePotongan';
$route['master/deletePotongan/(:any)']           = 'MasterController/deletePotongan/$1';

$route['master/cabang']                          = 'MasterController/cabang';
$route['master/cabang-dt']                       = 'MasterController/cabang_dt';
$route['master/addcabang']                       = 'MasterController/addCabang';
$route['master/save-cabang']                     = 'MasterController/saveCabang';
$route['master/detailCabang/(:any)']             = 'MasterController/detailCabang/$1';
$route['master/save-update-cabang']              = 'MasterController/saveUpdateCabang';
$route['master/deleteCabang/(:any)']             = 'MasterController/deleteCabang/$1';

// REPORT
$route['report']                   = 'ReportController/report';

// SELL (urutkan yang spesifik dulu)
$route['report/sell-dt']          = 'ReportController/sell_dt';
$route['report/sell-graph']       = 'ReportController/sellGraph';
$route['report/sell-print/(:num)']= 'ReportController/sellPrint/$1';
$route['report/sell-print-action/(:num)'] = 'ReportController/sellPrintAction/$1';
$route['report/sell/(:num)']      = 'ReportController/sellDetail/$1';
$route['report/sell']             = 'ReportController/sell';
$route['report/sell-items-json/(:num)'] = 'ReportController/sell_items_json/$1';

// BUY (biarkan seperti sebelumnya)
$route['report/buy-dt']           = 'ReportController/buy_dt';
$route['report/buy-graph']        = 'ReportController/buyGraph';
$route['report/buy-print/(:num)'] = 'ReportController/buyPrint/$1';
$route['report/buy-print-action/(:num)'] = 'ReportController/buyPrintAction/$1';
$route['report/buy/(:num)']       = 'ReportController/buyDetail/$1';
$route['report/buy']              = 'ReportController/buy';
$route['report/buy-items-json/(:num)']  = 'ReportController/buy_items_json/$1';

$route['report/test-dompdf']      = 'ReportController/testDompdf';

// Maintenance (admin-only)
$route['maintenance/truncate']['get']  = 'MaintenanceController/truncate';
$route['maintenance/truncate']['post'] = 'MaintenanceController/truncateRun';

/* =============================== DEFAULT =============================== */
$route['default_controller'] = 'HomeController';
$route['404_override']       = 'HomeController/error';
$route['translate_uri_dashes'] = FALSE;
