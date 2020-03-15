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

namespace Skript;

use pocketmine\event\Listener;

use Skript\events\PlayerJoin;
use Skript\events\PlayerQuit;
use Skript\events\PlayerChat;
use Skript\events\PlayerBreak;
use Skript\events\PlayerPlace;
use Skript\events\PlayerClick;
use pocketmine\event\player\PlayerCommandPreprocessEvent; //commands
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;

class SkriptListener implements Listener{
    
    public $plugin;
	public $joinClass;
	public $quitClass;
	public $chatClass;
	public $breakClass;
	public $placeClass;
	public $clickClass;
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
		$this->registerEventClass();
    }
	
	public function registerEventClass() : void {
		$this->joinClass = new PlayerJoin($this->plugin);
		$this->quitClass = new PlayerQuit($this->plugin);
		$this->chatClass = new PlayerChat($this->plugin);
		$this->breakClass = new PlayerBreak($this->plugin);
		$this->placeClass = new PlayerPlace($this->plugin);
		$this->clickClass = new PlayerClick($this->plugin);
	}
	
	public function sendCommands(PlayerJoinEvent $event) : void {
        $commandClass = $this->plugin->getCommandsClass();
        $commandClass->sendCommands($event->getPlayer());
	}
	
	public function reloadDisable(PlayerCommandPreprocessEvent $event) : void {
		if(explode(" ", $event->getMessage())[0] == "/reload"){
			$event->getPlayer()->sendMessage($this->plugin->format($this->plugin->getLangClass()->translate("reload.disable"), true));
			$event->setCancelled(true);
		}
	}
	
	public function showVersion(PlayerCommandPreprocessEvent $event) : void {
		if(explode(" ", $event->getMessage())[0] == "/version" or explode(" ", $event->getMessage())[0] == "/ver" or explode(" ", $event->getMessage())[0] == "/about"){
			$event->getPlayer()->sendMessage(str_replace("%version", $this->plugin->getVersionSkript(), $this->plugin->getLangClass()->translate("version.info1")));
			$event->getPlayer()->sendMessage(str_replace("%count", count($this->plugin->getLoaderClass()->loadedScripts), $this->plugin->getLangClass()->translate("version.info2")));
			$event->getPlayer()->sendMessage($this->plugin->getLangClass()->translate("version.info3"));
		}
	}
    
    public function playerCommand(PlayerCommandPreprocessEvent $event) : void {
        $commandClass = $this->plugin->getCommandsClass();
        if($commandClass->isCommand(explode(" ", $event->getMessage())[0])){
            $commandClass->dispatchCommand($event->getPlayer(), $event->getMessage());
            $event->setCancelled(true);
        }
    }
    
    public function onJoin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onJoin"] as $scripts){
			foreach($scripts as $exec){
				$this->joinClass->execCode($player, $exec, $event);
			}
		}
		
		if(!$player->hasPlayedBefore()){
			foreach($this->plugin->getLoaderClass()->scripts["onFirstJoin"] as $scripts){
				foreach($scripts as $exec){
					$this->joinClass->execCode($player, $exec, $event);
				}
			}
		}
	}
	
	public function onQuit(PlayerQuitEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onQuit"] as $scripts){
			foreach($scripts as $exec){
				$this->quitClass->execCode($player, $exec, $event);
			}
		}
	}
	
	public function onChat(PlayerChatEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onChat"] as $scripts){
			foreach($scripts as $exec){
				$this->chatClass->execCode($player, $exec, $event);
			}
		}
	}
	
	public function onBreak(BlockBreakEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onBreak"] as $scripts){
			foreach($scripts as $exec){
				if(isset($exec["block-id"])){
					if($event->getBlock()->getId() == (int) $exec["block-id"]){
						$this->placeClass->execCode($player, $exec["code"], $event);
					}
				}else{
					$this->breakClass->execCode($player, $exec["code"], $event);
				}
			}
		}
	}
	
	public function onPlace(BlockPlaceEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onPlace"] as $scripts){
			foreach($scripts as $exec){
				if(isset($exec["block-id"])){
					if($event->getBlock()->getId() == (int) $exec["block-id"]){
						$this->placeClass->execCode($player, $exec["code"], $event);
					}
				}else{
					$this->placeClass->execCode($player, $exec["code"], $event);
				}
			}
		}
	}
	
	public function onClick(PlayerInteractEvent $event){
		$player = $event->getPlayer();
		foreach($this->plugin->getLoaderClass()->scripts["onClick"] as $scripts){
			foreach($scripts as $exec){
				$this->clickClass->execCode($player, $exec, $event);
			}
		}
	}
	
	public function onChestGuiClose(InventoryCloseEvent $event){
		$player = $event->getPlayer();
		if($this->plugin->getGuiClass()->isOpen($player->getName())){
			$this->plugin->getGuiClass()->unsetChest($player->getName());
		}
	}
	
	public function oldChestGuiTransaction(InventoryTransactionEvent $event) { // simple transactions
		$transactions = $event->getTransaction()->getActions(); 
		foreach($transactions as $transaction){
			$player = $event->getTransaction()->getSource();
			if($this->plugin->getGuiClass()->isOpen($player->getName())) {
				if(isset($this->plugin->getGuiClass()->items[$player->getName()])){
					if(isset($this->plugin->getGuiClass()->items[$player->getName()][$transaction->getTargetItem()->getId()])){
						if($this->plugin->getGuiClass()->items[$player->getName()][$transaction->getTargetItem()->getId()]["command"] !== ""){
							$this->plugin->getServer()->dispatchCommand($player, $this->plugin->getGuiClass()->items[$player->getName()][$transaction->getTargetItem()->getId()]["command"]);
							$event->setCancelled(true);
						}
					
						if($this->plugin->getGuiClass()->items[$player->getName()][$transaction->getTargetItem()->getId()]["unstealabe"] == true){
							$event->setCancelled(true);
						}
					}
				}
			}
		}
	}
	
}