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

namespace Skript\commands;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\CommandData;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
use Skript\SkriptMain;
use pocketmine\utils\TextFormat;

class Command {
    
    private $plugin;
    public $commands = [];
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
    }
    
    public function isCommand(string $command) : bool {
        if(isset($this->commands[$command])){
            return true;
        }else{
            return false;     
        }
    }
    
    public function sendCommands($player) : void {
        $pk = new AvailableCommandsPacket();
        foreach ($this->commands as $command => $all) {
            $data = new CommandData();
            $data->commandName = substr(strtolower($command), 1);
            $data->commandDescription = $this->commands[$command]["description"];
            $data->flags = 0;
            $data->permission = 0;
            $parameter = new CommandParameter();
            $parameter->paramName = "args";
            $parameter->paramType = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_RAWTEXT;
            $parameter->isOptional = true;
            $data->overloads[0][0] = $parameter;
            $pk->commandData[$command] = $data;
        }
    
        $player->dataPacket($pk);
    }
    
    public function addCommand(string $command, array $code) : void {
        if(isset($this->commands[$command])){
            $this->plugin->getServer()->getLogger()->warning("[Skript] Error " . $command . " command is exists!");
        }else{
            $this->commands[$command] = [];
            $this->commands[$command]["code"] = $code;
        }
    }
	
	public function removeCommand(string $command) : void {
		unset($this->commands[$command]);
	}
    
    public function addDescription(string $command, string $desc) : void {
        $this->commands[$command]["description"] = $desc;
    }
    
    public function addPermission(string $command, string $permission) : void {
        $this->commands[$command]["permission"] = $permission;
    }
    
    public function addUsage(string $command, string $usage) : void {
        $this->commands[$command]["usage"] = $usage;
    }
	
	public function addPermissionMessage(string $command, string $message) : void {
        $this->commands[$command]["permission_message"] = $message;
    }
    
    public function dispatchCommand($player, $command) : void {
		$command = $command . " "; //hack for arguments
		$arguments = explode(" ", $command);
		$command = explode(" ", $command)[0];
		array_shift($arguments);
		if($this->commands[$command]["permission"] == ""){
			$this->plugin->getExecutorClass()->execute($player, $this->commands[$command]["code"], null, $arguments);
		}else{
			if($player->hasPermission($this->commands[$command]["permission"])){
				$this->plugin->getExecutorClass()->execute($player, $this->commands[$command]["code"], null, $arguments);
			}else{
				if($this->commands[$command]["permission_message"] == ""){
					$player->sendMessage(TextFormat::RED . "You do not have access to this command.");
				}else{
					$player->sendMessage($this->commands[$command]["permission_message"]);
				}
			}
		}
		
		if($this->plugin->config->get("log player commands") == true){
			if($this->plugin->config->get("save date usage command") == true){
				file_put_contents("log.txt", file_get_contents("log.txt") . PHP_EOL . "[" . date($this->plugin->config->get("date format")) . "] " . $player->getName() . ": " . $command);
			}else{
				file_put_contents("log.txt", file_get_contents("log.txt") . PHP_EOL . $player->getName() . ": " . $command);
			}
		}
    }
}