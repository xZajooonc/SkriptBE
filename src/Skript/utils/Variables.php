<?php

/*
     		 _         _       _   _          
			| |       (_)     | | | |         
		 ___| | ___ __ _ _ __ | |_| |__   ___ 
		/ __| |/ / '__| | '_ \| __| '_ \ / _ \
		\__ \   <| |  | | |_) | |_| |_) |  __/
		|___/_|\_\_|  |_| .__/ \__|_.__/ \___|
						| |                   
						|_|            


		Port version of Bukkit Skript for PocketMine-MP
		@Homepage: https://skriptbe.ga
		
*/

declare(strict_types=1);

namespace Skript\utils;

use Skript\SkriptMain;

class Variables{
    
    private $plugin;
	
	public $variables = [];
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function saveVariables() : void {
		if($this->plugin->config->get("save variables") == true){
			$variables = "";
			foreach($this->variables as $key => $value){
				$variables = $variables . $key . "=" . $value . PHP_EOL;
			}
			file_put_contents($this->getDataFolder() . "variables.txt", $variables);
		}
	}
	
	public function loadVariables() : void {
		foreach(file($this->getDataFolder() . "variables.txt") as $var){
			if(strlen($var) > 1){
				$key = explode("=", $var)[0];
				$value = explode("=", $var)[1];
			
				$this->variables[$key] = $value;
			}
		}
	}
	
	public function putVariable(string $key, string $value) : void {
		$this->variables[$key] = $value;
	}
	
	public function deleteVariable(string $key) : void {
		unset($this->variables[$key]);
	}
	
	public function addToVariable(string $key, string $value) : void {
		if(is_numeric($value)){
			$this->variables[$key] = $this->variables[$key]+$value;
		}else{
			$this->variables[$key] = $value;
		}
	}
	
	public function completeVariables(string $line) : string {
		foreach($this->variables as $key => $value){
			$line = str_replace("%{" . $key . "}%", $value, $line);
			//$line = str_replace("{" . $key . "}", $value, $line);
		}
		return $line;
	}
}