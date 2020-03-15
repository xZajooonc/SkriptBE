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
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;

class Scoreboard {
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function displayScoreboard($player, string $title) : void {
		$pk = new SetDisplayObjectivePacket();
		$pk->displaySlot = "sidebar";
		$pk->objectiveName = "ObjectiveName";
		$pk->displayName = $title;
		$pk->criteriaName = "dummy";
		$pk->sortOrder = 0;
		$player->sendDataPacket($pk);
	}
	
	public function setScore($player, string $name, int $score) : void {
		$entry = new ScorePacketEntry();
		$entry->objectiveName = "ObjectiveName";
		$entry->type = $entry::TYPE_FAKE_PLAYER;
		$entry->customName = $name;
		$entry->score = $score;
		$entry->scoreboardId = $score;
		$pk = new SetScorePacket();
		$pk->type = $pk::TYPE_CHANGE;
		$pk->entries[] = $entry;
		$player->sendDataPacket($pk);
	}
	
	public function wipeScoreboard($player) : void {
		$pk = new RemoveObjectivePacket();
		$pk->objectiveName = "ObjectiveName";
		$player->sendDataPacket($pk);
	}
}