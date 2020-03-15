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

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SkriptCommand {
    
    private $plugin;
	private $tag = TextFormat::WHITE . "[" . TextFormat::YELLOW . "Skript" . TextFormat::WHITE . "]";
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
    }
    
    public function onCommand(CommandSender $player, Command $command, string $label, array $args) : bool {
        if($command == "skript"){
			if($player->isOp()){
				if(empty($args)){
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript help " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.help"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript load [script] " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.enable"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript unload [script] " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.disable"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript reload " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.reload"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript list " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.list"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript update " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.update"));
					return true;
				}
				
				if($args[0] == "help"){
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript help " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.help"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript load [script] " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.enable"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript unload [script] " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.disable"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript reload " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.reload"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript list " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.list"));
					$player->sendMessage($this->tag . " " . TextFormat::AQUA . "/skript update " . TextFormat::DARK_GRAY . "- " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("command.update"));
					return true;
				}
				
				if($args[0] == "load"){
					if(isset($args[1])){
						if(strlen($args[1]) > 0){
							if($this->plugin->getLoaderClass()->isLoaded($args[1])){
								$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("skript.enable.error"));
							}else{
								$this->plugin->getLoaderClass()->loadScript($args[1] . ".sk");
								$player->sendMessage($this->tag . " " . TextFormat::WHITE . str_replace("%script", TextFormat::GOLD . $args[1] . TextFormat::WHITE, $this->plugin->getLangClass()->translate("skript.enable")));  
							}
						}
					}
				}
				
				if($args[0] == "unload"){
					if(isset($args[1])){
						if(strlen($args[1]) > 0){
							if($this->plugin->getLoaderClass()->isLoaded($args[1]) == true){
								$this->plugin->getLoaderClass()->unloadScript($args[1]);
								$player->sendMessage($this->tag . " " . TextFormat::WHITE . str_replace("%script", TextFormat::GOLD . $args[1] . TextFormat::WHITE, $this->plugin->getLangClass()->translate("skript.disable")));
							}else{
								$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("skript.disable.error"));
							}
						}
					}
				}
				
				if($args[0] == "reload"){
					$this->plugin->getLoaderClass()->unloadAll();
					$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("skript.reload"));
					$this->plugin->getLoaderClass()->loadAll();
					$this->plugin->config->reload();
					$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("loading.finish"));
				}
				
				if($args[0] == "list"){
					$list = array();
					foreach($this->plugin->getLoaderClass()->loadedScripts as $script){
						if($script["error"] == false){
							array_push($list, TextFormat::GREEN . $script["name"]);
						}else{
							array_push($list, TextFormat::RED . $script["name"]);
						}
					}
					$player->sendMessage("Scripts (" . count($list) . "): " . implode(TextFormat::GREEN . ", ", $list));
				}
				
				if($args[0] == "update"){
					$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("checking.update"));
					if($this->plugin->getUpdaterClass()->isConnection()){
						if($this->plugin->getUpdaterClass()->isUpdate()){
							$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("update.available"));
						}else{
							$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("running.latest"));
						}
					}else{
						$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("error.connection"));
					}
				}
			}else{
				$player->sendMessage($this->tag . " " . TextFormat::WHITE . $this->plugin->getLangClass()->translate("no.perm"), true);
			}
        }
        return true;
    }
}