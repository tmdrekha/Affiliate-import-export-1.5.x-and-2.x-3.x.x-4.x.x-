<?php
namespace Opencart\Admin\Controller\Extension\tmdaffiliateimportexport\Tmd;
// Lib Include 
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/Psr/autoloader.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/myclabs/Enum.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/ZipStream/autoloader.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/ZipStream/ZipStream.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/PhpSpreadsheet/autoloader.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
// // Lib Include

class Affimpexp extends \Opencart\System\Engine\Controller {
    private $error = array();
    public function index() {
        $this->load->language('extension/tmdaffiliateimportexport/tmd/affimpexp');
        $this->load->model('extension/tmdaffiliateimportexport/tmd/affimpexp');
        $this->document->setTitle($this->language->get('heading_title1'));
        $this->load->model('setting/setting');

        // // Ensure the POST request is valid and user has permission
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
           

                $tokenchage = 'user_token=' . $this->session->data['user_token'];

	      
	             // $fileType = pathinfo($this->request->files['import']['name'], PATHINFO_EXTENSION);

	         if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['import']['tmp_name']);
			} else {
				$content = false;
			}


               $path_parts = pathinfo($this->request->files['import']['name']);
				if(isset($path_parts['extension'])){
				$extension = $path_parts['extension'];
				}else{
				$extension = '';
				}
	            // // Validate file type
	            if (!in_array($extension, ['xls', 'xlsx'])) {
	                $this->session->data['warning'] = $this->language->get('error_invalidfiletype');
	            }
	        
	        if(!empty($content)){
			if ($extension == 'xlsx' || $extension == 'xls') { 
				if($extension == 'xlsx'){
			    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			    }elseif($extension == 'xls'){
			    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
               }
				
				$spreadsheet = $reader->load($_FILES['import']['tmp_name']);
				$spreadsheet->setActiveSheetIndex(0);
				$sheetDatas = $spreadsheet->getActiveSheet()->toArray(null,true,true,true);
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
	                        'newsletter' => $sheetData['I'], 
	                        'ip' => $sheetData['J'], 
	                        'status' => $sheetData['K'], 
	                        'safe' => $sheetData['L'], 
	                        'date_added' => $sheetData['M'], 
	                        
	                    );
	                    
	                    // Import customer data
	                 $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importCustomer($data);
	                }
	                $i++;
	            }



	            $spreadsheet->setActiveSheetIndex(1); 
	            $addressDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	            $j = 0;

	            foreach ($addressDatas as $addressData) {
	                if ($j != 0) { 
	                    $addressdataArray = array(
	                        'address_id'  => $addressData['A'], 
	                        'customer_id' => $addressData['B'], 
	                        'firstname'	  => $addressData['C'], 
	                        'lastname' 	  => $addressData['D'], 
	                        'company' 	  => $addressData['E'], 
	                        'address_1'   => $addressData['F'], 
	                        'address_2'   => $addressData['G'],
	                        'city' 	      => $addressData['H'], 
	                        'postcode'    => $addressData['I'], 
	                        'country_id'  => $addressData['J'], 
	                        'zone_id' 	  => $addressData['K'], 
	                    );
	                    
	                    $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importCustomerAddress($addressdataArray);
	                }
	                $j++;
	            }


	            // Process the second sheet (customer_affiliate)
	            $spreadsheet->setActiveSheetIndex(2); 
	            $affiliateDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	            $k = 0;

	            foreach ($affiliateDatas as $affiliateData) {
	                if ($k != 0) { // Skip header row
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
	                        'custom_field' =>[],  
	                    );
	                    
	                    // Import affiliate data
	                    $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importCustomerAffiliate($affiliateDataArray);
	                }
	                $k++;
	            }
	            // Process the third sheet (history)
				$spreadsheet->setActiveSheetIndex(3); 
				$historyDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$l = 0;

				foreach ($historyDatas as $historyData) {
				    if ($l != 0) { // Skip header row
				        $historyDataArray = array(
				            'customer_history_id' => $historyData['A'], // Assuming 'B' is the correct index for customer_id
				            'customer_id' => $historyData['B'], // Assuming 'B' is the correct index for customer_id
				            'comment' => $historyData['C'], 
				            'date_added' => $historyData['D'], 
				        );

				        // Import customer history data
				        $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importhistory($historyDataArray);
				    }
				    $l++;
				}
				
				// Process the third sheet (transaction)
				$spreadsheet->setActiveSheetIndex(4); 
				$transactionDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$m = 0;

				foreach ($transactionDatas as $transactionData) {
				    if ($m != 0) { // Skip header row
				        $transactionDataArray = array(
				            'customer_transaction_id' => $transactionData['A'], // Assuming 'B' is the correct index for customer_id
				            'customer_id' => $transactionData['B'], 
				            'description' => $transactionData['C'], 
				            'amount' => $transactionData['D'], 
				            'date_added' => $transactionData['E'], 
				        );

				        // Import customer transaction data
				        $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importTransaction($transactionDataArray);
				    }
				    $m++;
				}

				// Process the third sheet (reward)
				$spreadsheet->setActiveSheetIndex(5); 
				$rewardDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$n = 0;

				foreach ($rewardDatas as $rewardData) {
				    if ($n != 0) { 
				        $rewardDataArray = array(
				            'customer_reward_id' => $rewardData['A'], 
				            'customer_id' => $rewardData['B'], 
				            'order_id' => $rewardData['C'], 
				            'description' => $rewardData['D'], 
				            'points' => $rewardData['E'], 
				            'date_added' => $rewardData['F'], 
				        );

				        $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importReward($rewardDataArray);
				    }
				    $n++;
				}
				$spreadsheet->setActiveSheetIndex(5); 
				$ipDatas = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				$o = 0;

				foreach ($ipDatas as $ipData) {
				    if ($n != 0) { 
				        $ipDataArray = array(
				            'customer_id' => $ipData['B'], 
				            'ip' => $ipData['C'], 
				            'date_added' => $ipData['D'],
				        );

				        $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importIp($ipDataArray);
				    }
				    $n++;
				}



			
	            if (empty($this->error)) {
	                $this->session->data['success'] = $this->language->get('text_importsuccess');
	            }
	}
	        } else {
	           $this->session->data['warning'] = $this->language->get('error_emptyfile');
         // Import customer data
	                   // $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->importCustomer($data);
	        }

	        // Redirect to the import page
	        $this->response->redirect($this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', $tokenchage, true));
	    
        }

        $data['heading_title'] = $this->language->get('heading_title');
       
        if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
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
            'href' => $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL')
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        // Ensure you use user_token here
        $data['action'] = $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['export'] = $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp.export', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['import'] = $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', 'user_token=' . $this->session->data['user_token'], 'SSL');
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/tmdaffiliateimportexport/tmd/affimpexp', $data));

    }

    // Import/Export start
	public function export() {
	    $this->load->language('extension/tmdaffiliateimportexport/tmd/affimpexp');
	    $this->load->model('extension/tmdaffiliateimportexport/tmd/affimpexp');

	        // Fetch affiliate and customer data 
	    $addressResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllAddress();
	    $affiliateResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllAffiliates();


	    $customerResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllCustomers();


	    $historyResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllHistory();

	    $transactionResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAlltransaction();

	    $rewardResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllreward();

	    $ipResults = $this->model_extension_tmdaffiliateimportexport_tmd_affimpexp->getAllip();

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
		$spreadsheet->getActiveSheet()->setCellValue('I'.$i, 'newsletter');
		$spreadsheet->getActiveSheet()->setCellValue('J'.$i, 'ip');
		$spreadsheet->getActiveSheet()->setCellValue('K'.$i, 'status');
		$spreadsheet->getActiveSheet()->setCellValue('L'.$i, 'safe');
		$spreadsheet->getActiveSheet()->setCellValue('M'.$i, 'date_added');



		// Loop through customer data
		foreach ($customerResults as $customer) {
			 if(!empty($customer['customer_id'])){
			 $customername = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id ='".$customer['customer_id']."'");
			}
		    $i++;

		    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($customer['customer_id']) ? $customer['customer_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($customer['customer_group_id']) ? $customer['customer_group_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($customer['store_id']) ? $customer['store_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($customer['language_id']) ? $customer['language_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($customername->row['firstname']) ? $customer['firstname'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('F'.$i, isset($customername->row['lastname']) ? $customer['lastname'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('G'.$i, isset($customer['email']) ? $customer['email'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('H'.$i, isset($customer['telephone']) ? $customer['telephone'] :'');
		    $spreadsheet->getActiveSheet()->setCellValue('I'.$i, isset($customer['newsletter']) ? $customer['newsletter'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('J'.$i, isset($customer['ip']) ? $customer['ip'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('K'.$i, isset($customer['status']) ? $customer['status'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('L'.$i, isset($customer['safe']) ? $customer['safe'] : ''); 
		    $spreadsheet->getActiveSheet()->setCellValue('M'.$i, isset($customer['date_added']) ? $customer['date_added'] : ''); 
		}


	    // Create a new sheet for address data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(1);
	    $spreadsheet->getActiveSheet()->setTitle('Address');
	    
	    $i = 1;

	    // Set headers for affiliate data
	    $spreadsheet->getActiveSheet()->setCellValue('A'.$i, 'Address ID');
	    $spreadsheet->getActiveSheet()->setCellValue('B'.$i, 'Customer ID');
		$spreadsheet->getActiveSheet()->setCellValue('C'.$i, 'Firstname');
		$spreadsheet->getActiveSheet()->setCellValue('D'.$i, 'Lastname');
	    $spreadsheet->getActiveSheet()->setCellValue('E'.$i, 'Company');
		$spreadsheet->getActiveSheet()->setCellValue('F'.$i, 'Address 1');
		$spreadsheet->getActiveSheet()->setCellValue('G'.$i, 'Address 2');
		$spreadsheet->getActiveSheet()->setCellValue('H'.$i, 'City');
		$spreadsheet->getActiveSheet()->setCellValue('I'.$i, 'Postcode');
		$spreadsheet->getActiveSheet()->setCellValue('J'.$i, 'Country ID');
		$spreadsheet->getActiveSheet()->setCellValue('K'.$i, 'Zone ID');
	    // Loop through result data
	    foreach ($addressResults as $addressresult) {
	        $i++;
	        $spreadsheet->getActiveSheet()->setCellValue('A'.$i, isset($addressresult['address_id']) ? $addressresult['address_id'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('B'.$i, isset($addressresult['customer_id']) ? $addressresult['customer_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('C'.$i, isset($addressresult['firstname']) ? $addressresult['firstname']:'');
		    $spreadsheet->getActiveSheet()->setCellValue('D'.$i, isset($addressresult['lastname']) ? $addressresult['lastname'] : '');
	        $spreadsheet->getActiveSheet()->setCellValue('E'.$i, isset($addressresult['company']) ? $addressresult['company'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('F'.$i, isset($addressresult['address_1']) ? $addressresult['address_1'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('G'.$i, isset($addressresult['address_2']) ? $addressresult['address_2'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('H'.$i, isset($addressresult['city']) ? $addressresult['city'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('I'.$i, isset($addressresult['postcode']) ? $addressresult['postcode'] : '');
         	$spreadsheet->getActiveSheet()->setCellValue('J'.$i, isset($addressresult['country_id']) ? $addressresult['country_id'] : '');
		    $spreadsheet->getActiveSheet()->setCellValue('K'.$i, isset($addressresult['zone_id']) ? $addressresult['zone_id'] : '');
		    
	    }


	    // Create a new sheet for affiliate data
	    $spreadsheet->createSheet();
	    $spreadsheet->setActiveSheetIndex(2);
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
	    $spreadsheet->setActiveSheetIndex(3);
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
	    $spreadsheet->setActiveSheetIndex(4);
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
	    $spreadsheet->setActiveSheetIndex(5);
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
	    $spreadsheet->setActiveSheetIndex(6);
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
    	$filename = 'Affiliate_and_Customer_data.xls';
		$filename = 'Affiliate_and_Customer_data.xlsx';
		$spreadsheet->getActiveSheet()->setTitle('Customer');
		$writer =new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment; filename="'. urlencode($filename).'"');
		$writer->save('php://output');

	}



    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/tmdaffiliateimportexport/tmd/affimpexp')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}
