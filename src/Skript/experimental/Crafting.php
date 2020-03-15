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

namespace Skript\experimental;

use Skript\SkriptMain;
use pocketmine\Server;
use pocketmine\inventory\ShapedRecipe;
use pocketmine\item\Item;

class Crafting {
	
	public function newRecipe($result, $items = array()) : void {
		$recipe = new ShapedRecipe(["abc","def","ghi"], [
		    "a" => Item::get($items[0], 0),
		    "b" => Item::get($items[1], 0),
		    "c" => Item::get($items[2], 0),
		    "d" => Item::get($items[3], 0),
		    "e" => Item::get($items[4], 0),
		    "f" => Item::get($items[5], 0),
		    "g" => Item::get($items[6], 0),
		    "h" => Item::get($items[7], 0),
		    "i" => Item::get($items[8], 0),
	    ], [$result]);
        Server::getInstance()->getCraftingManager()->registerRecipe($recipe);
	}
    
}