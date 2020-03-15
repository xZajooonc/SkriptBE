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
use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddActorPacket as AddEntityPacket;
use pocketmine\network\mcpe\protocol\BossEventPacket;
use pocketmine\network\mcpe\protocol\RemoveActorPacket as RemoveEntityPacket;
use pocketmine\network\mcpe\protocol\SetActorDataPacket as SetEntityDataPacket;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;
use pocketmine\Server;

class BossBar {
	
	public $bossBars = [];
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function setBossBar($player, string $title) : void {
		$eid = Entity::$entityCount++;
		$packet = new AddEntityPacket();
		$packet->entityRuntimeId = $eid;
		$packet->type = 37;
		$packet->metadata = [Entity::DATA_LEAD_HOLDER_EID => [Entity::DATA_TYPE_LONG, -1], Entity::DATA_FLAGS => [Entity::DATA_TYPE_LONG, 0 ^ 1 << Entity::DATA_FLAG_SILENT ^ 1 << Entity::DATA_FLAG_INVISIBLE ^ 1 << Entity::DATA_FLAG_NO_AI], Entity::DATA_SCALE => [Entity::DATA_TYPE_FLOAT, 0],
			Entity::DATA_NAMETAG => [Entity::DATA_TYPE_STRING, $title], Entity::DATA_BOUNDING_BOX_WIDTH => [Entity::DATA_TYPE_FLOAT, 0], Entity::DATA_BOUNDING_BOX_HEIGHT => [Entity::DATA_TYPE_FLOAT, 0]];
		foreach(array($player) as $player){
			$pk = clone $packet;
			$pk->position = $player->getPosition()->asVector3()->subtract(0, 28);
			$player->dataPacket($pk);
		}
		$bpk = new BossEventPacket();
		$bpk->bossEid = $eid;
		$bpk->eventType = BossEventPacket::TYPE_SHOW;
		$bpk->title = $title;
		$bpk->healthPercent = 1;
		$bpk->unknownShort = 0;
		$bpk->color = 0;
		$bpk->overlay = 0;
		$bpk->playerEid = 0;
		$this->plugin->getServer()->broadcastPacket(array($player), $bpk);
		$this->bossBars[$player->getName()] = $eid;
	}
	
	public function wipeBossBar($player) : void {
		if(isset($this->bossBars[$player->getName()])){
			$pk = new RemoveEntityPacket();
			$pk->entityUniqueId = $this->bossBars[$player->getName()];
			$this->plugin->getServer()->broadcastPacket(array($player), $pk);
		}
	}
    
}