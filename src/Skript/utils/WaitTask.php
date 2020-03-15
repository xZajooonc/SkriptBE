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
use pocketmine\scheduler\Task as PluginTask;

class WaitTask extends PluginTask {
	
	private $plugin;
	private $code;
	public $player;

    public function  __construct($plugin, $player, $code) {
		$this->plugin = $plugin;
		$this->player = $player;
		$this->code = $code;
    }

    public function onRun($currentTick) {
		foreach($this->code as $line){
			$this->plugin->executor->executeCode($this->player, $line, null);
		}
	}
}