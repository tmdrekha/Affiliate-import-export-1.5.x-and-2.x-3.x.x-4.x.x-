<?php 
namespace Opencart\Admin\Controller\Extension\tmdaffiliateimportexport\tool;


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


class affimport extends \Opencart\System\Engine\Controller {

	public function index() {	
		
	$totalnewaffiliate=0;
		$totalupdateaffiliate=0;

		$this->language->load('extension/tmdaffiliateimportexport/tool/affimport');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('extension/tmdaffiliateimportexport/tool/affimport');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'extension/tmdaffiliateimportexport/tool/affimport')) {
			
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
				
				$spreadsheet = $reader->load($_FILES['import']['tmp_name']);
				$spreadsheet->setActiveSheetIndex(0);
				$sheetDatas = $spreadsheet->getActiveSheet()->toArray(null,true,true,true);


				$i=0;
				/*
				@ arranging the data according to our need
				*/
				foreach($sheetDatas as $sheetData){
					if($i!=0)
					{
					
					/* Step Customer Collect Data */
					$customer_id=$sheetData['A'];

					$firstname=$sheetData['B'];
					$lastname=$sheetData['C'];
					$email=$sheetData['D'];
					$fax=$sheetData['E'];
					$telephone=$sheetData['F'];
					$address_1=$sheetData['G'];
					$address_2=$sheetData['H'];
					$city=$sheetData['I'];
					/////////////////zone
					$zone=$sheetData['J'];
					$zone_id=0;
					if(!empty($zone))
					{
					$zone_id=$this->model_extension_tmdaffiliateimportexport_tool_affimport->getZonebyname($zone);
					
					}
					/////////////////zone
					/////////////////country
					$country=$sheetData['K'];
					$country_id=0;
					if(!empty($country))
					{
					$country_id=$this->model_extension_tmdaffiliateimportexport_tool_affimport->getCountrybyname($country);
					}
					/////////////////country
					
					//////////////// status
					$status=$sheetData['L'];
					if(strtolower((int)$status)=='enabled'){
						$status=1;
					}else{$status=0;}
					//////////////// status
					$code=(string)$sheetData['M'];
					$commission=$sheetData['N'];
					$tax_id=$sheetData['O'];
					$payment=$sheetData['P'];
					$cheque=$sheetData['Q'];
					$paypal=$sheetData['R'];
					$bankname=$sheetData['S'];
					$branchno=$sheetData['T'];
					$swiftcode=$sheetData['U'];
					$accname=$sheetData['V'];
					$accno=$sheetData['W'];
					$salt=$sheetData['X'];
					$password=$sheetData['Y'];
					$ip=$sheetData['Z'];
					//////////////// approved
					$approved=$sheetData['AA'];
					if(strtolower((string)$approved)=='yes'){
						$approved=1;
					}else{$approved=0;}
					//////////////// approved
					$date_added=$sheetData['AB'];
					$company=$sheetData['AC'];
					/////password status
					$password_status=$sheetData['AD'];
					if(strtolower((string)$password_status)=='no'){
						$password_status=0;
					}else{$password_status=1;}

			
				$data='';
				$data=array(
				'customer_id'=>$customer_id,
				'firstname'=>$firstname,
				'lastname'=>$lastname,
				'email'=>$email,
				'fax'=>$fax,
				'telephone'=>$telephone,
				'address_1'=>$address_1,
				'address_2'=>$address_2,
				'city'=>$city,
				'zone_id'=>$zone_id,
				'country_id'=>$country_id,
				'status'=>$status,
				'code'=>$code,
				'commission'=>$commission,
				'tax'=>$tax_id,
				'payment'=>$payment,
				'cheque'=>$cheque,
				'paypal'=>$paypal,
				'bank_name'=>$bankname,
				'bank_branch_number'=>$branchno,
				'bank_swift_code'=>$swiftcode,
				'bank_account_name'=>$accname,
				'bank_account_number'=>$accno,
				'salt'=>$salt,
				'password'=>$password,
				'ip'=>$ip,
				'approved'=>$approved,
				'date_added'=>$date_added,
				'company'=>$company,
				'password_status'=>$password_status,
				);

				$customerInfos=$this->model_extension_tmdaffiliateimportexport_tool_affimport->getCustomerByEmail((string)$email);

			   	$customer_id_new=0;
			
				if(!empty($customerInfos['customer_id'])){
                   $customer_id_new =$customerInfos['customer_id'];
				}

					if(empty($customer_id_new)){ 

						if(!empty($email) && !empty($firstname) && !empty($lastname)){
							$this->model_extension_tmdaffiliateimportexport_tool_affimport->addAffiliate($data);
							$totalnewaffiliate++;
						}
					}
					else
					{
						$this->model_extension_tmdaffiliateimportexport_tool_affimport->editAffiliate($data,$customer_id_new);
						$totalupdateaffiliate++;
					}
				}
					$i++;
					
				
				}
				 $this->session->data['success']=$totalupdateaffiliate .' :: Total Affiliate update ' .$totalnewaffiliate. ':: Total New Affiliate added';
				
				////////////////////////// Started Import work  //////////////
				$this->response->redirect($this->url->link('extension/tmdaffiliateimportexport/tool/affimport', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			} else {
				$json['error']['warning'] = $this->language->get('error_empty');
			}
		$data['heading_title'] = $this->language->get('heading_title');
		$data['button_import'] = $this->language->get('button_import');
		$data['entry_import'] = $this->language->get('entry_import');
		
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], 'SSL'),     		
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/tmdaffiliateimportexport/tool/affimport', 'user_token=' . $this->session->data['user_token'], 'SSL'),
      		'separator' => ' :: '
   		);
		

		$data['import'] = $this->url->link('extension/tmdaffiliateimportexport/tool/affimport', 'user_token=' . $this->session->data['user_token'], 'SSL');

		$data['user_token']= $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/tmdaffiliateimportexport/tool/affimport', $data));
	}
	
	
}
?>