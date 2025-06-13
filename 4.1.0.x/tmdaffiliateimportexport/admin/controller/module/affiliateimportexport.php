<?php
namespace Opencart\Admin\Controller\Extension\Tmdaffiliateimportexport\Module;

require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/system.php');

require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/Psr/autoloader.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/myclabs/Enum.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/ZipStream/autoloader.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/ZipStream/ZipStream.php');
require_once(DIR_EXTENSION.'/tmdaffiliateimportexport/system/library/tmd/PhpSpreadsheet/autoloader.php');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Xls;

class affiliateimportexport extends \Opencart\System\Engine\Controller {
	public function index(): void {
		
		$this->registry->set('tmd', new  \Tmdaffiliateimportexport\System\Library\Tmd\System($this->registry));
		$keydata=array(
		'code'=>'tmdkey_affiliateimportexport',
		'eid'=>'MjUzODI=',
		'route'=>'extension/tmdaffiliateimportexport/module/affiliateimportexport',
		);
		$orderimpexp=$this->tmd->getkey($keydata['code']);
		$data['getkeyform']=$this->tmd->loadkeyform($keydata);
		
			
		$this->load->language('extension/tmdaffiliateimportexport/module/affiliateimportexport');

		$this->document->setTitle($this->language->get('heading_title1'));
		
		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
		
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module')
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/tmdaffiliateimportexport/module/affiliateimportexport', 'user_token=' . $this->session->data['user_token'])
		];
		

		if(VERSION>='4.0.2.0'){
			$data['save'] = $this->url->link('extension/tmdaffiliateimportexport/module/affiliateimportexport.save', 'user_token=' . $this->session->data['user_token']);
		}
		else{
			$data['save'] = $this->url->link('extension/tmdaffiliateimportexport/module/affiliateimportexport|save', 'user_token=' . $this->session->data['user_token']);
		}

		


		$data['back'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module');

		$data['module_affiliateimportexport_status'] = $this->config->get('module_affiliateimportexport_status');
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/tmdaffiliateimportexport/module/affiliateimportexport', $data));
	}
	
	public function install(): void{
		// Fix permissions
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/module/affiliateimportexport');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/module/affiliateimportexport');
		
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/tool/affimport');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/tool/affimport');

		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/tmd/affimpexp');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/tmd/affimpexp');


		// TMD Affiliate menu
		if(VERSION>='4.0.2.0')
		{
			$eventaction='extension/tmdaffiliateimportexport/module/affiliateimportexport.menu';
		}
		else{
			$eventaction='extension/tmdaffiliateimportexport/module/affiliateimportexport|menu';
		}
		$this->model_setting_event->deleteEventByCode('tmd_affiliateimportexportmenu');
		
		$eventrequest=[
				'code'=>'tmd_affiliateimportexportmenu',
				'description'=>'TMD Affiliate list Menu',
				'trigger'=>'admin/view/common/column_left/before',
				'action'=>$eventaction,
				'status'=>'1',
				'sort_order'=>'1',
			];
		if(VERSION=='4.0.0.0')
		{
		$this->model_setting_event->addEvent('tmd_affiliateimportexportmenu', 'TMD Affiliate list Menu', 'admin/view/common/column_left/before','extension/tmdaffiliateimportexport/module/affiliateimportexport|menu', true, 1);
		}else{
			$this->model_setting_event->addEvent($eventrequest);
		}
	}
	


	public function uninstall(): void{
		// Register events
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('tmd_affiliateimportexportmenu');

		$this->load->model('user/user_group');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/module/affiliateimportexport');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/module/affiliateimportexport');
		
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/tool/affimport');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/tool/affimport');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'access', 'extension/tmdaffiliateimportexport/tmd/affimpexp');
		$this->model_user_user_group->removePermission($this->user->getGroupId(), 'modify', 'extension/tmdaffiliateimportexport/tmd/affimpexp');


	}	

	public function save(): void {
		$this->load->language('extension/tmdaffiliateimportexport/module/affiliateimportexport');

		$json = [];

		if (!$this->user->hasPermission('modify', 'extension/tmdaffiliateimportexport/module/affiliateimportexport')) {
			$json['error'] = $this->language->get('error_permission');
		}
		
		$affiliateimportexport=$this->config->get('tmdkey_affiliateimportexport');
		if (empty(trim($affiliateimportexport))) {			
		$json['error'] ='Module will Work after add License key!';
		}
	

		if (!$json) {
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting('module_affiliateimportexport', $this->request->post);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function keysubmit() {
		$json = array(); 
		
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$keydata=array(
			'code'=>'tmdkey_affiliateimportexport',
			'eid'=>'MjUzODI=',
			'route'=>'extension/tmdaffiliateimportexport/module/affiliateimportexport',
			'moduledata_key'=>$this->request->post['moduledata_key'],
			);
			$this->registry->set('tmd', new  \Tmdaffiliateimportexport\System\Library\Tmd\System($this->registry));
		
            $json=$this->tmd->matchkey($keydata);       
		} 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function menu(string&$route, array&$args, mixed&$output):void {
		$this->load->language('extension/tmdaffiliateimportexport/module/affiliateimportexport');
		$modulestatus=$this->config->get('module_affiliateimportexport_status');

		if(!empty($modulestatus)){
			$tmdaffimpexp = [];

			if ($this->user->hasPermission('access', 'extension/tmdaffiliateimportexport/tmd/affimpexp')) {
				$tmdaffimpexp[] = [
					'name'     => $this->language->get('text_affimpexp'),
					'href'     => $this->url->link('extension/tmdaffiliateimportexport/tmd/affimpexp', 'user_token='.$this->session->data['user_token']),
					'children' => []
				];
			}

		

							
			if ($tmdaffimpexp) {
				$args['menus'][] = [
					'id'       => 'menu-Affimpexp',
					'icon'     => 'fa fa-file',
					'name'     => $this->language->get('text_affimpexp'),
					'href'     => '',
					'children' => $tmdaffimpexp
				];
			}
		}		  
	}
	
}