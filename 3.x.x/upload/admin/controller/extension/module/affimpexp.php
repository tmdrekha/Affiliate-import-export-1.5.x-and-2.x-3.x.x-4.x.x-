<?php
// Lib Include 
require_once(DIR_SYSTEM.'library/tmd/system.php');
// Lib Include 
class ControllerExtensionModuleAffImpExp extends Controller {
	private $error = array();

	public function index() {
		$this->registry->set('tmd', new TMD($this->registry));
			$keydata=array(
			'code'=>'tmdkey_affimpexp',
			'eid'=>'MjUzODI=',
			'route'=>'extension/module/affimpexp',
			);
			$affimpexp=$this->tmd->getkey($keydata['code']);
			$data['getkeyform']=$this->tmd->loadkeyform($keydata);
		
		$this->load->language('extension/module/affimpexp');

		$this->document->setTitle($this->language->get('heading_title1'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_affimpexp', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
		
			unset($this->session->data['warning']);
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/affimpexp', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/affimpexp', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_affimpexp_status'])) {
			$data['module_affimpexp_status'] = $this->request->post['module_affimpexp_status'];
		} else {
			$data['module_affimpexp_status'] = $this->config->get('module_affimpexp_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/affimpexp', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/affimpexp')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$affimpexp=$this->config->get('tmdkey_affimpexp');
		if (empty(trim($affimpexp))) {			
		$this->session->data['warning'] ='Module will Work after add License key!';
		$this->response->redirect($this->url->link('extension/module/affimpexp', 'user_token=' . $this->session->data['user_token'], true));
		}

		return !$this->error;
	}
	
	public function keysubmit() {
		$json = array(); 
		
      	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$keydata=array(
			'code'=>'tmdkey_affimpexp',
			'eid'=>'MjUzODI=',
			'route'=>'extension/module/affimpexp',
			'moduledata_key'=>$this->request->post['moduledata_key'],
			);
			$this->registry->set('tmd', new TMD($this->registry));
            $json=$this->tmd->matchkey($keydata);       
		} 
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}