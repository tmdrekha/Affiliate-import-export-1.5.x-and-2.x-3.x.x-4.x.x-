<?php 
/**
 * TMD(http://opencartextensions.in/)
 *
 * Copyright (c) 2016 - 2017 TMD
 * This package is Copyright so please us only one domain 
 * 
 */
 
set_time_limit(0);
ini_set('memory_limit','999M');
error_reporting(-1);
require_once(DIR_APPLICATION.'/controller/tool/PHPExcel.php');

class ControllerToolaffimport extends Controller { 
	private $error = array();
	
	public function index() {	
		
		$totalnewaffiliate=0;
		$totalupdateaffiliate=0;
		$this->language->load('tool/affimport');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('tool/affimport');
				
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', 'tool/affimport')) {
			
			if (is_uploaded_file($this->request->files['import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['import']['tmp_name']);
			} else {
				$content = false;
			}
			
			if ($content) {
				////////////////////////// Started Import work  //////////////
				try {
					$objPHPExcel = PHPExcel_IOFactory::load($this->request->files['import']['tmp_name']);
				} catch(Exception $e) {
					die('Error loading file "'.pathinfo($this->path.$files,PATHINFO_BASENAME).'": '.$e->getMessage());
				}
				/*	@ get a file data into $sheetDatas variable */
				$sheetDatas = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
				/*	@ $i variable for getting data. in first iteration of loop we get size and color name of product */
				$i=0;
				/*
				@ arranging the data according to our need
				*/
				foreach($sheetDatas as $sheetData){
					if($i!=0)
					{
					
					/* Step Customer Collect Data */
					$affiliate_id=$sheetData['A'];
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
					$zone_id=$this->model_tool_affimport->getZonebyname($zone);
					
					}
					/////////////////zone
					/////////////////country
					$country=$sheetData['K'];
					$country_id=0;
					if(!empty($country))
					{
					$country_id=$this->model_tool_affimport->getCountrybyname($country);
					}
					/////////////////country
					
					//////////////// status
					$status=$sheetData['L'];
					if(strtolower($status)=='enabled'){
						$status=1;
					}else{$status=0;}
					//////////////// status
					$code=$sheetData['M'];
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
					if(strtolower($approved)=='yes'){
						$approved=1;
					}else{$approved=0;}
					//////////////// approved
					$date_added=$sheetData['AB'];
					$company=$sheetData['AC'];
					/////password status
					$password_status=$sheetData['AD'];
					if(strtolower($password_status)=='no'){
						$password_status=0;
					}else{$password_status=1;}
					
					/////password status
					
					/* Step Customer Collect Data */
			
				$data='';
				$data=array(
				'affiliate_id'=>$affiliate_id,
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
						
						if(empty($affiliate_id))
						{ 
								if(!empty($email) && !empty($firstname) && !empty($lastname)){
									$this->model_tool_affimport->addAffiliate($data);
									$totalnewaffiliate++;
								}
						}
						else
						{
							$this->model_tool_affimport->editAffiliate($data,$affiliate_id);
							$totalupdateaffiliate++;
						}
		}
					$i++;
					
				
				}
				 $this->session->data['success']=$totalupdateaffiliate .' :: Total Affiliate update ' .$totalnewaffiliate. ':: Total New Affiliate added';
				
				////////////////////////// Started Import work  //////////////
				$this->redirect($this->url->link('tool/affimport', 'token=' . $this->session->data['token'], 'SSL'));
			} else {
				$this->error['warning'] = $this->language->get('error_empty');
			}
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_import'] = $this->language->get('button_import');
		$this->data['entry_import'] = $this->language->get('entry_import');
		
		
		if (isset($this->session->data['error'])) {
    		$this->data['error_warning'] = $this->session->data['error'];
    
			unset($this->session->data['error']);
 		} elseif (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),     		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('tool/affimport', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['import'] = $this->url->link('tool/affimport', 'token=' . $this->session->data['token'], 'SSL');

		$this->template = 'tool/affimport.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	
}
?>