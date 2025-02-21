<?php
require_once(DIR_SYSTEM.'/library/tmd/system.php');
require_once(DIR_SYSTEM.'/library/tmd/Psr/autoloader.php');
require_once(DIR_SYSTEM.'/library/tmd/myclabs/Enum.php');
require_once(DIR_SYSTEM.'/library/tmd/ZipStream/autoloader.php');
require_once(DIR_SYSTEM.'/library/tmd/ZipStream/ZipStream.php');
require_once(DIR_SYSTEM.'/library/tmd/PhpSpreadsheet/autoloader.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ControllerExtensionAffimpExp extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('extension/affimpexp');
        $this->document->setTitle($this->language->get('heading_title1'));
        $this->load->model('setting/setting');

        // Ensure the POST request is valid and user has permission
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('importexport', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            // Redirect with user_token
            $this->response->redirect($this->url->link('extension/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL'));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_select'] = $this->language->get('text_select');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_export'] = $this->language->get('text_export');
        $data['text_import'] = $this->language->get('text_import');
        $data['text_importfile'] = $this->language->get('text_importfile');
        $data['text_exportfile'] = $this->language->get('text_exportfile');
        $data['text_importsuccess'] = $this->language->get('text_importsuccess');

        

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        // Ensure you use user_token here
        $data['action'] = $this->url->link('extension/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['export'] = $this->url->link('extension/affimpexp/export', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['import'] = $this->url->link('extension/affimpexp/import', 'user_token=' . $this->session->data['user_token'], 'SSL');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/affimpexp', $data));
    }

    // Import/Export start
	public function export() {
	    $this->load->language('extension/affimpexp');
	    $this->load->model('extension/affimpexp');

	        // Fetch affiliate and customer data
	    $affiliateResults = $this->model_extension_affimpexp->getAllAffiliates();
	    $customerResults = $this->model_extension_affimpexp->getAllCustomers();
	    $historyResults = $this->model_extension_affimpexp->getAllHistory();
	    $transactionResults = $this->model_extension_affimpexp->getAlltransaction();
	    $rewardResults = $this->model_extension_affimpexp->getAllreward();
	    $ipResults = $this->model_extension_affimpexp->getAllip();

	    // Create a new Spreadsheet object
	    $spreadsheet = new Spreadsheet();
	    $spreadsheet->getProperties()->setTitle("Affiliate and Customer Data")
	        ->setSubject("Affiliate and Customer Data")
	        ->setLastModifiedBy("affiliates")
	        ->setDescription("Affiliate and Customer Data");
	    $spreadsheet->getActiveSheet()->setTitle('Customer');

	    // Create the first sheet for customer data
	    $spreadsheet->setActiveSheetIndex(0);
	    $i = 1;
		// Set headers for customer data
		$spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'customer_id');
		$spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'customer_group_id');
		$spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'store_id');
		$spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'language_id');
		$spreadsheet->getActiveSheet()->setCellValue('E'.$i, 'firstname');
		$spreadsheet->getActiveSheet()->setCellValue('F'.$i, 'lastname');
		$spreadsheet->getActiveSheet()->setCellValue('G'.$i, 'email');
		$spreadsheet->getActiveSheet()->setCellValue('H'.$i, 'telephone');
		$spreadsheet->getActiveSheet()->setCellValue('I'.$i, 'fax');
		$spreadsheet->getActiveSheet()->setCellValue('J'.$i, 'salt');
		$spreadsheet->getActiveSheet()->setCellValue('K'.$i, 'address_id');
		$spreadsheet->getActiveSheet()->setCellValue('L'.$i, 'ip');
		$spreadsheet->getActiveSheet()->setCellValue('M'.$i, 'status');
		$spreadsheet->getActiveSheet()->setCellValue('N'.$i, 'safe');
		$spreadsheet->getActiveSheet()->setCellValue('O'.$i, 'company');
		$spreadsheet->getActiveSheet()->setCellValue('P'.$i, 'address_1');
		$spreadsheet->getActiveSheet()->setCellValue('Q'.$i, 'address_2');
		$spreadsheet->getActiveSheet()->setCellValue('R'.$i, 'city');
		$spreadsheet->getActiveSheet()->setCellValue('S'.$i, 'postcode');
		$spreadsheet->getActiveSheet()->setCellValue('T'.$i, 'newsletter');
		$spreadsheet->getActiveSheet()->setCellValue('U'.$i, 'country_id');
		$spreadsheet->getActiveSheet()->setCellValue('V'.$i, 'zone_id');
		$spreadsheet->getActiveSheet()->setCellValue('W'.$i, 'date_added');


		// Loop through customer data
		foreach ($customerResults as $customer) {
		    $i++;
		    // print_r($customerResults);die();
		    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($customer['customer_id']) ? $customer['customer_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($customer['customer_group_id']) ? $customer['customer_group_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($customer['store_id']) ? $customer['store_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($customer['language_id']) ? $customer['language_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($customer['firstname']) ? $customer['firstname'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('F'.$i, isset($customer['lastname']) ? $customer['lastname'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('G'.$i, isset($customer['email']) ? $customer['email'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('H'.$i, isset($customer['telephone']) ? $customer['telephone'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('I'.$i, isset($customer['fax']) ? $customer['fax'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('J'.$i, isset($customer['salt']) ? $customer['salt'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('K'.$i, isset($customer['address_id']) ? $customer['address_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('L'.$i, isset($customer['ip']) ? $customer['ip'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('M'.$i, isset($customer['status']) ? $customer['status'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('N'.$i, isset($customer['safe']) ? $customer['safe'] : ''); 
		    $spreadsheet->getActiveSheet()->setCellValue('O'.$i, isset($customer['company']) ? $customer['company'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('P'.$i, isset($customer['address_1']) ? $customer['address_1'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('Q'.$i, isset($customer['address_2']) ? $customer['address_2'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('R'.$i, isset($customer['city']) ? $customer['city'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('S'.$i, isset($customer['postcode']) ? $customer['postcode'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('T'.$i, isset($customer['newsletter']) ? $customer['newsletter'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('U'.$i, isset($customer['country_id']) ? $customer['country_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('V'.$i, isset($customer['zone_id']) ? $customer['zone_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('W'.$i, isset($customer['date_added']) ? $customer['date_added'] : '');
		}


	    // Create a new sheet for affiliate data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(1);
	    $spreadsheet->getActiveSheet()->setTitle('Affiliate');
	    
	    $i = 1;

	    // Set headers for affiliate data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Customer ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'company');
	    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'website');
	    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'tracking');
	    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, 'commission');
	    $spreadsheet->getActiveSheet()->setCellValue('F'.$i, 'tax');
	    $spreadsheet->getActiveSheet()->setCellValue('G'.$i, 'payment');
	    $spreadsheet->getActiveSheet()->setCellValue('H'.$i, 'cheque');
	    $spreadsheet->getActiveSheet()->setCellValue('I'.$i, 'status');

	    // Loop through result data
	    foreach ($affiliateResults as $result) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($result['customer_id']) ? $result['customer_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($result['company']) ? $result['company'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($result['website']) ? $result['website'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($result['tracking']) ? $result['tracking'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($result['commission']) ? $result['commission'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('F'.$i, isset($result['tax']) ? $result['tax'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('G'.$i, isset($result['payment']) ? $result['payment'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('H'.$i, isset($result['cheque']) ? $result['cheque'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('I'.$i, isset($result['status']) ? $result['status'] : '');
	    }
	     // Create a new sheet for history data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(2);
	    $spreadsheet->getActiveSheet()->setTitle('History');
	    $i = 1;

	    // Set headers for affiliate data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Customer History ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'Customer ID');
	    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'Comment');
	    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'date_added');

	    // Loop through result data
	    foreach ($historyResults as $history) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($history['customer_history_id']) ? $history['customer_history_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($history['customer_id']) ? $history['customer_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($history['comment']) ? $history['comment'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($history['date_added']) ? $history['date_added'] : '');
	    }
	// Create a new sheet for transaction data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(3);
	     $spreadsheet->getActiveSheet()->setTitle('Transaction');
	    $i = 1;

	    // Set headers for transaction data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Customer Transaction ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'Customer ID');
	    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'Description');
	    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'Amount');
	    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, 'Date Added');

	    // Loop through transaction data
	    foreach ($transactionResults as $transaction) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($transaction['customer_transaction_id']) ? $transaction['customer_transaction_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($transaction['customer_id']) ? $transaction['customer_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($transaction['description']) ? $transaction['description'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($transaction['amount']) ? $transaction['amount'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($transaction['date_added']) ? $transaction['date_added'] : '');
	    }
	    // Create a new sheet for reward data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(4);
	     $spreadsheet->getActiveSheet()->setTitle('Reward');

	    $i = 1;

	    // Set headers for reward data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Customer Reward ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'Customer ID');
	    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'Order ID');
	    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'Description');
	    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, 'Points');
	    $spreadsheet->getActiveSheet()->setCellValue('F'.$i, 'Date Added');

	    // Loop through reward data
	    foreach ($rewardResults as $reward) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($reward['customer_reward_id']) ? $reward['customer_reward_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($reward['customer_id']) ? $reward['customer_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($reward['order_id']) ? $reward['order_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($reward['description']) ? $reward['description'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($reward['points']) ? $reward['points'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('F'.$i, isset($reward['date_added']) ? $reward['date_added'] : '');
	    }

	     // Create a new sheet for ip data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(5);
	    $spreadsheet->getActiveSheet()->setTitle('IP');

	    $i = 1;

	    // Set headers for ip data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Customer IP ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'Customer ID');
	    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'IP');
	    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'Date Added');

	    // Loop through ip data
	    foreach ($ipResults as $ip) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($ip['customer_ip_id']) ? $ip['customer_ip_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($ip['customer_id']) ? $ip['customer_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($ip['ip']) ? $ip['ip'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($ip['date_added']) ? $ip['date_added'] : '');
	    }

	    $spreadsheet->setActiveSheetIndex(0);
	    



	    // Save the file
	    $filename = 'Affiliate_and_Customer_data.xlsx';
	    $filename = 'Affiliate_and_Customer_data.xlsx';
   		$writer = new Xlsx($spreadsheet);

	    // Set headers for download
	    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	    header('Content-Disposition: attachment; filename="'.$filename.'"');
	    $writer->save('php://output');
	    exit();

	}


    public function import() {
	    $this->load->language('extension/affimpexp');
	    $this->load->model('extension/affimpexp');
	    $data['error_invalidfiletype'] = $this->language->get('error_invalidfiletype');

	    $tokenchage = 'user_token=' . $this->session->data['user_token'];

	    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'extension/affimpexp')) {
	        if (empty($this->request->files['import']['name'])) {
	            $this->error['warning'] = $this->language->get('error_emptyfile');
	            $this->index();
	            return;
	        }

	        if (isset($this->request->files['import']) && is_uploaded_file($this->request->files['import']['tmp_name'])) {
	            $fileType = pathinfo($this->request->files['import']['name'], PATHINFO_EXTENSION);

	            // Validate file type
	            if (!in_array($fileType, ['xls', 'xlsx'])) {
	                $this->error['warning'] = $this->language->get('error_invalidfiletype');
	                $this->index();
	                return;
	            }

	            try {
	                // Load the spreadsheet
	                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->files['import']['tmp_name']);
	            } catch (Exception $e) {
	                $this->error['warning'] = 'Error loading file: ' . $e->getMessage();
	                $this->index();
	                return;
	            }

	            // Process the first sheet (customers)
	            $spreadsheet->setActiveSheetIndex(0); 
	            $sheetDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	            $i = 0;

	            foreach ($sheetDatas as $sheetData) {
	                if ($i != 0) { // Skip header row
	                    $data = array(
	                        'customer_id' => $sheetData['A'], 
	                        'customer_group_id' => $sheetData['B'], 
	                        'store_id' => $sheetData['C'], 
	                        'language_id' => $sheetData['D'], 
	                        'firstname' => $sheetData['E'], 
	                        'lastname' => $sheetData['F'], 
	                        'email' => $sheetData['G'], 
	                        'telephone' => $sheetData['H'], 
	                        'fax' => $sheetData['I'], 
	                        'salt' => $sheetData['J'], 
	                        'address_id' => $sheetData['K'], 
	                        'ip' => $sheetData['L'], 
	                        'status' => $sheetData['M'], 
	                        'safe' => $sheetData['N'], 
	                        'company' => $sheetData['O'], 
	                        'address_1' => $sheetData['P'], 
	                        'address_2' => $sheetData['Q'], 
	                        'city' => $sheetData['R'], 
	                        'postcode' => $sheetData['S'], 
	                        'newsletter' => $sheetData['T'], 
	                        'country_id' => $sheetData['U'], 
	                        'zone_id' => $sheetData['V'], 
	                        'date_added' => $sheetData['W'], 
	                    );
	                    
	                    // Import customer data
	                    $this->model_extension_affimpexp->importCustomer($data);
	                }
	                $i++;
	            }

	            // Process the second sheet (customer_affiliate)
	            $spreadsheet->setActiveSheetIndex(1); 
	            $affiliateDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	            $j = 0;

	            foreach ($affiliateDatas as $affiliateData) {
	                if ($j != 0) { // Skip header row
	                    $affiliateDataArray = array(
	                        'customer_id' => $affiliateData['A'], 
	                        'company' 	=> $affiliateData['B'], 
	                        'website' 	=> $affiliateData['C'],
	                        'tracking' 	=> $affiliateData['D'], 
	                        'commission' => $affiliateData['E'], 
	                        'tax' 		=> $affiliateData['F'], 
	                        'payment' => $affiliateData['G'], 
                         	'cheque' => $affiliateData['H'], 
	                        'status' => $affiliateData['I'], 
	                    );
	                    
	                    // Import affiliate data
	                    $this->model_extension_affimpexp->importCustomerAffiliate($affiliateDataArray);
	                }
	                $j++;
	            }
	            // Process the third sheet (history)
				$spreadsheet->setActiveSheetIndex(2); 
				$historyDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$k = 0;

				foreach ($historyDatas as $historyData) {
				    if ($k != 0) { // Skip header row
				        $historyDataArray = array(
				            'customer_history_id' => $historyData['A'], // Assuming 'B' is the correct index for customer_id
				            'customer_id' => $historyData['B'], // Assuming 'B' is the correct index for customer_id
				            'comment' => $historyData['C'], 
				            'date_added' => $historyData['D'], 
				        );

				        // Import customer history data
				        $this->model_extension_affimpexp->importhistory($historyDataArray);
				    }
				    $k++;
				}
				
				// Process the third sheet (transaction)
				$spreadsheet->setActiveSheetIndex(3); 
				$transactionDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$l = 0;

				foreach ($transactionDatas as $transactionData) {
				    if ($l != 0) { // Skip header row
				        $transactionDataArray = array(
				            'customer_transaction_id' => $transactionData['A'], // Assuming 'B' is the correct index for customer_id
				            'customer_id' => $transactionData['B'], 
				            'description' => $transactionData['C'], 
				            'amount' => $transactionData['D'], 
				            'date_added' => $transactionData['E'], 
				        );

				        // Import customer transaction data
				        $this->model_extension_affimpexp->importTransaction($transactionDataArray);
				    }
				    $l++;
				}

				// Process the third sheet (reward)
				$spreadsheet->setActiveSheetIndex(4); 
				$rewardDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$m = 0;

				foreach ($rewardDatas as $rewardData) {
				    if ($m != 0) { 
				        $rewardDataArray = array(
				            'customer_reward_id' => $rewardData['A'], // Assuming 'B' is the correct index for customer_id
				            'customer_id' => $rewardData['B'], 
				            'order_id' => $rewardData['C'], 
				            'description' => $rewardData['D'], 
				            'points' => $rewardData['E'], 
				            'date_added' => $rewardData['F'], 
				        );

				        $this->model_extension_affimpexp->importReward($rewardDataArray);
				    }
				    $m++;
				}

				// // Process the third sheet (ip)
				// $spreadsheet->setActiveSheetIndex(5); 
				// $ipDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				// $n = 0;

				// foreach ($rewardDatas as $ipData) {
				//     if ($n != 0) { 
				//         $ipDataArray = array(
				//             'customer_id' => $ipData['B'], 
				//             'ip' => $ipData['C'], 
				//             'date_added' => $ipData['D'],
				//         );

				//         $this->model_extension_affimpexp->importIp($ipDataArray);
				//     }
				//     $n++;
				// }


	            // Check for errors and set success message
	            if (empty($this->error)) {
	                $this->session->data['success'] = $this->language->get('text_importsuccess');
	            }
	        } else {
	            $this->error['warning'] = $this->language->get('error_emptyfile');
	        }

	        // Redirect to the import page
	        $this->response->redirect($this->url->link('extension/affimpexp', $tokenchage, true));
	    }
	    $this->index();
	}

    // Import/Export end

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/affimpexp')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
