<?php  
class ControllerModuleLanguage extends Controller {
	protected function index() {
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && (isset($this->request->post['language']))) {
			$this->language->set($this->request->post['language']);
			
			$this->redirect($this->url->http('common/home'));
    	}
		
		$this->load->language('module/language');
		 
    	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['entry_language'] = $this->language->get('entry_language');
    	
		$this->data['action'] = $this->url->http('common/home');
    	
		$this->data['default'] = $this->language->getCode();	
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->id       = 'language';
		$this->template = 'module/language.tpl';
		
		$this->render(); 
	}
}
?>