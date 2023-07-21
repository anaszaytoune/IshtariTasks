<?php
abstract class Controller {
	Public $id;
	Public $template;
	Public $layout;
	Public $children = array();
	Public $data = array();
	Public $output;
	
	Public function __get($key) {
		return Registry::get($key);
	}
	
	Public function __set($key, $value) {
		Registry::set($key, $value);
	}
			
	Public function forward($class, $method, $args = array()) {
		return new Action($class, $method, $args);
	}

	Public function redirect($url) {
		header('Location: ' . html_entity_decode($url));
		exit();
	}
	
	Public function render() {
		foreach ($this->children as $child) {
			$file  = DIR_APPLICATION . 'controller/' . $child . '.php';
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $child);
		
			if (file_exists($file)) {
				require_once($file);

				$controller = new $class();
				
				$controller->index();
				
				$this->data[$controller->id] = $controller->output;
			} else {
				exit('Error: Could not load controller ' . $child . '!');
			}
		}
		
		$this->output = $this->fetch($this->template);
		
		if ($this->layout) {
			$file  = DIR_APPLICATION . 'controller/' . $this->layout . '.php';
			$class = 'Controller' . preg_replace('/[^a-zA-Z0-9]/', '', $this->layout);
			
			if (file_exists($file)) {
				require_once($file);
				
				$controller = new $class();
				
				$controller->data[$this->id] = $this->output;
				
				$controller->index();
				
				$this->output = $controller->output;
			} else {
				exit('Error: Could not load controller ' . $this->layout . '!');
			}	
		}
		
		$this->response->setOutput($this->output);
	}
	
	Public function fetch($filename) {
		$file = DIR_TEMPLATE . $filename;
    
		if (file_exists($file)) {
			extract($this->data);
			
      		ob_start();
      
	  		require($file);
      
	  		$content = ob_get_contents();

      		ob_end_clean();

      		return $content;
    	} else {
      		exit('Error: Could not load template ' . $file . '!');
    	}
	}
}
?>