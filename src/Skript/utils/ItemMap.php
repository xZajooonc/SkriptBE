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
use pocketmine\item\Item;

class ItemMap {
    
    public function get(string $item) : int {
		if(Item::fromString($item)){
			return Item::fromString($item)->getId();
		}else{
			return 0;
		}
	}
}