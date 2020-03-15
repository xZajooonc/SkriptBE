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

namespace Skript;

use pocketmine\utils\Config;

class SkriptLang {
	
	private $plugin;
	private $langYaml;
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
		$this->langYaml = new Config($this->plugin->getDataFolder() . "lang.yml", Config::YAML);
		if($this->langYaml->get($this->plugin->config->get("language")) == null){
			$this->plugin->logger("Language " . $this->plugin->config->get("language") . " not found!", true);
		}
	}
	
	public function translate(string $string) : string {
		if($this->langYaml->get($this->plugin->config->get("language"))){
			if(isset($this->langYaml->get($this->plugin->config->get("language"))[$string])){
				return $this->langYaml->get($this->plugin->config->get("language"))[$string];
			}else{
				$this->plugin->logger("Not found " . $string . " sentence in language " . $this->plugin->config->get("language"), true);
				return $string;
			}
		}else{
			return $string;
		}
	}
}