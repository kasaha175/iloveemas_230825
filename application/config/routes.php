<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['login-process'] = 'AuthController/loginProcess';
$route['logout-process'] = 'AuthController/logoutProcess';
$route['dashboard'] = 'HomeController/dashboard';

//transaction
$route['transaction'] = 'TransactionController';
$route['transaction-list'] = 'TransactionController/list';
$route['transaction/getTransactions'] = 'TransactionController/getTransactions';
$route['transaction/redirect/(:any)'] = 'TransactionController/redirectTransaction/$1';
$route['transaction/delete-transaction/(:any)'] = 'TransactionController/deleteTransaction/$1';
$route['transaction/confirm-edit'] = 'TransactionController/confirmEdit';


$route['transaction/buy'] = 'TransactionController/buy';
$route['transaction/buy/lm/select'] = 'TransactionController/lm';
$route['transaction/buy/platinum/select'] = 'TransactionController/platinum';
$route['transaction/buy/paladium/select'] = 'TransactionController/paladium';
$route['transaction/buy/iridium/select'] = 'TransactionController/iridium';
$route['transaction/buy/rhodium/select'] = 'TransactionController/rhodium';
$route['transaction/buy/ruthenium/select'] = 'TransactionController/ruthenium';
$route['transaction/buy/silver/select'] = 'TransactionController/silver';
$route['transaction/buy/(:any)'] = 'TransactionController/buyCart/$1';
$route['transaction/buy-add-to-cart'] = 'TransactionController/buyAddToCart';
$route['transaction/buy-add-to-cart-reset'] = 'TransactionController/buyAddToCartReset';
$route['transaction/buy-checkout'] = 'TransactionController/buyCheckout';
$route['report/buy-print/(:any)'] = 'TransactionController/buyPrint/$1';
$route['transaction/buy-delete-transaction/(:any)'] = 'TransactionController/buyDeleteTransaction/$1';
$route['transaction/chart-destroy'] = 'TransactionController/chartDestroy';
$route['transaction/buy-void/(:any)'] = 'TransactionController/buyVoid/$1';
$route['transaction/buy-revision/(:any)'] = 'TransactionController/buyRevision/$1';


$route['transaction/sell'] = 'TransactionController/sell';
$route['transaction/sell/(:any)'] = 'TransactionController/sellCart/$1';
$route['transaction/sell-add-to-cart'] = 'TransactionController/sellAddToCart';
$route['transaction/sell-add-to-cart-reset'] = 'TransactionController/sellAddToCartReset';
$route['transaction/sell-checkout'] = 'TransactionController/sellCheckout';
$route['transaction/sell-delete-transaction/(:any)'] = 'TransactionController/sellDeleteTransaction/$1';

$route['transaction/updateAllStatus'] = 'TransactionController/updateAllStatus';
$route['transaction/select-customer/(:any)'] = 'TransactionController/selectCustomer/$1';
$route['transaction/new-customer'] = 'TransactionController/newCustomer';
$route['transaction/new-customer-process'] = 'TransactionController/newCustomerProcess';
$route['report/sell-print/(:any)'] = 'TransactionController/sellPrint/$1';
$route['transaction/sell-void/(:any)'] = 'TransactionController/sellVoid/$1';
$route['transaction/sell-revision/(:any)'] = 'TransactionController/sellRevision/$1';


//archive
$route['archive'] = 'MasterController/archive';
$route['archive/buy'] = 'MasterController/buy';
$route['archive/buy/save'] = 'MasterController/buySave';
$route['archive/sell'] = 'MasterController/sell';
$route['archive/sell/save'] = 'MasterController/sellSave';

//master
$route['master'] = 'MasterController/master';
$route['master/customer'] = 'MasterController/customer';
$route['master/delete-customer-process/(:any)'] = 'MasterController/deleteCustomerProcess/$1';
$route['master/edit-customer-process'] = 'MasterController/editCustomerProcess';
$route['master/customer/(:any)'] = 'MasterController/detailCustomer/$1';

// master Memo
$route['master/memo'] = 'MasterController/memo';
$route['master/addMemo'] = 'MasterController/addmemo';
$route['master/save-memo'] = 'MasterController/saveMemo';
$route['master/detailMemo/(:any)'] = 'MasterController/detailMemo/$1';
$route['master/update-memo'] = 'MasterController/saveUpdateMemo';
$route['master/deleteMemo/(:any)'] = 'MasterController/deleteMemo/$1';

// potongan-lm
$route['master/potongan'] = 'MasterController/potongan';
$route['master/addpotongan'] = 'MasterController/addPotongan';
$route['master/save-potongan'] = 'MasterController/savePotongan';
$route['master/detailPotongan/(:any)'] = 'MasterController/detailPotongan/$1';
$route['master/save-update-potongan'] = 'MasterController/saveUpdatePotongan';
$route['master/deletePotongan/(:any)'] = 'MasterController/deletePotongan/$1';

// 
$route['master/cabang'] = 'MasterController/cabang';
$route['master/addcabang'] = 'MasterController/addCabang';
$route['master/save-cabang'] = 'MasterController/saveCabang';
$route['master/detailCabang/(:any)'] = 'MasterController/detailCabang/$1';
$route['master/save-update-cabang'] = 'MasterController/saveUpdateCabang';
$route['master/deleteCabang/(:any)'] = 'MasterController/deleteCabang/$1';

//report
$route['report'] = 'ReportController/report';
$route['report/buy'] = 'ReportController/buy';
$route['report/buy-graph'] = 'ReportController/buyGraph';
$route['report/buy/(:any)'] = 'ReportController/buyDetail/$1';
$route['report/sell'] = 'ReportController/sell';
$route['report/sell-graph'] = 'ReportController/sellGraph';
$route['report/sell/(:any)'] = 'ReportController/sellDetail/$1';

//default
$route['default_controller'] = 'HomeController';
$route['404_override'] = 'HomeController/error';
$route['translate_uri_dashes'] = FALSE;

?>