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
use pocketmine\utils\Config as PluginConfig;

class Config {
	
	private $plugin;
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
    
    public function get($file, $key) : string {
		$config = new PluginConfig($file, PluginConfig::YAML);
		return $config->get($key);
	}
	
	public function set($file, $key, $value) : void {
		$config = new PluginConfig($file, PluginConfig::YAML);
		$config->set($key, $value);
		$config->save();
	}
    
}