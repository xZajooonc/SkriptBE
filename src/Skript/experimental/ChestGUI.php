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
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\block\Block;

class ChestGUI {
	
	private $plugin;
	public $chests = [];
	public $items = [];
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function open($player, $name) : void {
		$block = Block::get(54);
		$block->x = (int) $player->getFloorX();
		$block->y = (int) $player->getFloorY()-3;
		$block->z = (int) $player->getFloorZ();
		$block->level = $player->getLevel();
		$block->level->sendBlocks([$player], [$block]);
				
		$chest = Tile::createTile(Tile::CHEST, $player->getLevel(), Chest::createNBT($block));
		$chest->name = $name;	
		$player->addWindow($chest->getInventory());
		$this->chests[$player->getName()] = $chest;
		$this->items[$player->getName()] = [];
	}
	
	public function formatSlot($player, $slot = 0, $item, $unstealabe = false, $command = "") : void {
		$chest = $this->chests[$player->getName()];
		$chest->getInventory()->setItem($slot, $item);
		$this->items[$player->getName()][$item->getId()] = [];
		$this->items[$player->getName()][$item->getId()]["unstealabe"] = $unstealabe;
		$this->items[$player->getName()][$item->getId()]["command"] = $command;
	}
	
	public function unsetChest($player) : void {
		unset($this->chests[$player]);
		unset($this->items[$player]);
	}
	
	public function isOpen($player) : bool {
		if(isset($this->chests[$player])){
			return true;
		}
		return false;
	}
    
}