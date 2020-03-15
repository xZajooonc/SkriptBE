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

namespace Skript\events;

use Skript\SkriptMain;
use Skript\SkriptExecutor;

class PlayerPlace {
	
	private $plugin;
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function execCode($player, $code, $event) : void {
		$executor = $this->plugin->getExecutorClass();
		$this->plugin->getExecutorClass()->execute($player, $code, $event);
	}
    
}