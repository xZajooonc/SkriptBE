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

class Options {
	
	private $plugin;
	
	public $options = [];
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
    
    public function putOption(string $scriptName, string $key, string $value) : void {
		$this->options[$scriptName] = [];
		$this->options[$scriptName][$key] = $value;
	}
	
	public function completeOptions(string $scriptName, array $function) : array {
		if(isset($this->options[$scriptName])){
			foreach($this->options[$scriptName] as $key => $value){
				$function = json_decode(str_replace("{@$key}", $value, json_encode($function)), true);;
			}
			return $function;
		}
		return $function;
	}
    
}