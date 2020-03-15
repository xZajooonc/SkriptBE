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

namespace Skript;
	
use Skript\utils\Variables;
use Skript\utils\WaitTask;
use Skript\utils\FileSystem;
use Skript\experimental\Crafting;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\math\Vector3;
use pocketmine\entity\Entity;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\item\Item;

class SkriptExecutor {
	
	private $plugin;
	
	public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function execute($player = null, $code = null, $event = null, $args = array()) : void {
		$allCode = $code;
		$lines = 0;
		$loop = array(false, 0);
		$structures = $this->structureArrayMap($code);
		$structure = 0;
		$players = array($player);
		foreach($code as $line){
			if($loop[0] == true){
				goto start;
			}
			
			$line = $this->plugin->getVariablesClass()->completeVariables($line);
			for($i = 1; $i <= count($args); $i++){
				$line = str_replace("%arg $i%", $args[$i-1], $line);
				$line = str_replace("%argument $i%", $args[$i-1], $line);
				$line = str_replace("%arguments $i%", $args[$i-1], $line);
			}
			
			array_shift($allCode);
			$lines++;
			$inter = explode(" ", $line);
			if($inter[0] == "loop"){
				if($inter[2] == "times:"){
					$count = (int) $inter[1];
					$loop = array(true, $count);
					goto end;
				}
				
				if($inter[2] == "players"){
					if($inter[1] == "all"){
						$count = 1;
						$loop = array(true, $count);
						$players = $this->plugin->getServer()->getOnlinePlayers();
						goto end;
					}
				}
			}
			
			if($inter[0] == "wait"){
				$time = (int) $inter[1];
				switch($inter[2]){
					case "tick":
                        $time = $time;
                    break;
                        
                    case "ticks":
                        $time = $time;
                    break;
                        
                    case "second":
                        $time = $time*20;
                    break;
                        
                    case "seconds":
                        $time = $time*20;
                    break;
                            
                    case "minute":
                        $time = $time*1200;
                    break;
                        
                    case "minutes":
                        $time = $time*1200;
                    break;
                        
                    case "hour":
                        $time = $time*72000;
                    break;
                        
                    case "hours":
                        $time = $time*72000;
                    break;
				}
					
				$this->plugin->getScheduler()->scheduleDelayedTask(new WaitTask($this->plugin, $player, $allCode), $time);
				goto stop;
			}
			
			if($inter[0] == "if"){
				$structure++;
				if($inter[1] == "name"){
					if($inter[2] == "of"){
						if($inter[3] == "player's"){
							if($inter[4] == "tool"){
								if($inter[5] == "is"){
									$name = str_replace("'", "", $inter[6]);
									$name = str_replace(":", "", $name);
									if($name == $player->getInventory()->getItemInHand()->getName()){
										goto start;
									}else{
										if(count($structures)-$structure >= 0){
											foreach($structures[count($structures)-$structure] as $line){
												$this->executeCode($player, $line, $event);	
											}
											goto stop;
										}
										goto stop;
									}
								}
							}
						}
					}
				}
				
				if($inter[1] == "file" or $inter[1] == "folder"){
					$dir = explode("'", $line)[1];
					if(explode("'", $line)[2] == " doesn"){
						if(!file($dir)){
							goto start;
						}else{
							foreach($structures[count($structures)-$structure] as $line){
								$this->executeCode($player, $line, $event);	
							}
							goto stop;
						}
					}else{
						if(file($dir)){
							goto start;
						}else{
							foreach($structures[count($structures)-$structure] as $line){
								$this->executeCode($player, $line, $event);	
							}
							goto stop;
						}
					}
				}
				
				if(isset($inter[1]{0}) and $inter[1]{0} == "'"){
					$key = explode("'", $line)[1];
					if($inter[2] == "is"){
						if($key == $inter[3]){
							goto start;
						}else{
							foreach($structures[count($structures)-$structure] as $line){
								$this->executeCode($player, $line, $event);	
							}
							goto stop;
						}
					}
					
					if($inter[2] == "isn't"){
						if($key !== $inter[3]){
							goto start;
						}else{
							foreach($structures[count($structures)-$structure] as $line){
								$this->executeCode($player, $line, $event);	
							}
							goto stop;
						}
					}
				}else{
					if($inter[2] == "is"){
						if(str_replace("'", "", $inter[1]) == str_replace("'", "", $inter[3])){
							goto start;
						}else{
							if(count($structures)-$structure >= 0){
								foreach($structures[count($structures)-$structure] as $line){
									$this->executeCode($player, $line, $event);	
								}
								goto stop;
							}
							goto stop;
						}
					}
				}
				
				if($inter[3] == "(yaml"){
					$yamlA = explode(")", explode("(", $line)[1])[0];
					$yamlB = explode(" ", $yamlA);
					if($yamlB[0] == "yaml"){
						if($yamlB[1] == "value"){
							if($yamlB[3] == "from"){
								if($yamlB[3] == "file"){
									
									$key = explode("'", $yamlB[2])[1];
									$file = explode("'", $yamlB[5])[1];
									if($inter[1] == $this->plugin->getConfigClass()->get($file, $key)){
										goto start;
									}else{
										foreach($structures[count($structures)-$structure] as $line){
											$this->executeCode($player, $line, $event);	
										}
										goto stop;
									}
								}
							}
						}
					}
				}
				
				if($inter[1] == "player"){
					if($inter[2] == "is"){
						$argument = explode("'", explode("'", $inter[3])[1])[0];
						if($player !== null){
							if($player->getName() === $argument){
								goto start;
							}else{
								foreach($structures[count($structures)-$structure] as $line){
									$this->executeCode($player, $line, $event);	
								}
								goto stop;
							}
						}
					}
					
					if($inter[2] == "has"){
						if($inter[3] == "permission"){
							$permission = explode("'", $line)[1];
							if($player->hasPermission($permission)){
								goto start;
							}else{
								foreach($structures[count($structures)-$structure] as $line){
									$this->executeCode($player, $line, $event);	
								}
								goto stop;
							}
						}
					}
				}
				
				if($inter[1] == "argument" or $inter[1] == "arg"){
					if($inter[3] == "is"){
						if($inter[4] == "online"){ 
							if($this->plugin->getServer()->getPlayer($args[0])){
								goto start;
							}else{
								foreach($structures[count($structures)-$structure] as $line){
									$this->executeCode($player, $line, $event);	
								}
								goto stop;
							}
						}
						
						if($inter[4] == "not"){
							if($inter[5] == "set"){
								if(isset($args[$inter[2]-1])){
									foreach($structures[count($structures)-$structure] as $line){
										$this->executeCode($player, $line, $event);	
									}
									goto stop;
								}else{
									goto start;
								}
							}
						}else{
							if($inter[2] == str_replace("'", "", $inter[4])){
								goto start;
							}else{
								foreach($structures[count($structures)-$structure] as $line){
									$this->executeCode($player, $line, $event);	
								}
								goto stop;
							}
						}
					}
				}
				
				if($inter[1] == "event-item"){
					if($inter[2] == "is"){
						$item = explode("is ", $line)[1];
						if($player->getInventory()->getItemInHand()->getId() == $this->plugin->getItemMap()->get($item)){
							goto start;
						}else{
							foreach($structures[count($structures)-$structure] as $line){
								$this->executeCode($player, $line, $event);	
							}
							goto stop;
						}
					}
				}
				
			}

			if($line == "stop"){
				goto stop;
			}
			
			if($line == "else:"){
				goto stop;
			}
			
			start:
			if($loop[0] == true){
				$times = $loop[1];
			}else{
				$times = 1;
			}
			
			for($i = 1; $i <= $times; $i++){
				if(count($players) > 1){
					foreach($players as $player){
						$this->executeCode($player, $line, $event);	
					}
				}else{
					$this->executeCode($player, $line, $event);	
				}
			}
			$loop = array(false, 0);
			end:
		}
		stop:
	}
	
	public function executeCode($player = null, $line = null, $event = null) : void {
		$inter = explode(" ", $line);
		if($player !== null){
			$line = str_replace("%player%", $player->getName(), $line);
			$line = str_replace("%player's display name%", $player->getName(), $line);
			if(isset($this->plugin->getPluginApi()["FactionsPro"])){
				$line = str_replace("%player's faction%", $this->plugin->getPluginApi()["FactionsPro"]->getPlayerFaction($player->getName()), $line);
			}
			
			if(isset($this->plugin->getPluginApi()["EconomyAPI"])){
				$line = str_replace("%player's balance%", $this->plugin->getPluginApi()["EconomyAPI"]->myMoney($player), $line);
			}
			$line = str_replace("%x-coordinate of player%", floor($player->getX()), $line);
			$line = str_replace("%y-coordinate of player%", floor($player->getY()), $line);
			$line = str_replace("%z-coordinate of player%", floor($player->getZ()), $line);
			$line = str_replace("%now%", date("d/m/Y H:i"), $line);
		}
		$line = $this->plugin->getVariablesClass()->completeVariables($line);
		if($inter[0] == "send"){
			if($inter[1] == "player"){
				if($inter[2] == "title"){
					$title = explode("'", $line)[1];
					$subtitle = "";
					if(explode("'", $line)[2] == " with subtitle "){
						$subtitle = explode("'", $line)[3];
						$player->addTitle($title, $subtitle, 10, 20, 10);
					}
				}
			}else{
				$message = explode("'", $line)[1];
				if(explode("'", $line)[2] == " to player" or explode("'", $line)[2] == ""){
					if($player !== null){
						$player->sendMessage($message);
					}
				}
				
				if(explode($message . "'", $line)[1] == "to ops" or explode($message . "'", $line)[1] == "to all ops"){
					foreach($this->plugin->getServer()->getOnlinePlayers() as $players){
						if($players->isOp()){
							$players->sendMessage($message);
						}
					}
				}
			
				if(explode($message . "'", $line)[1] == "to all players"){
					foreach($this->plugin->getServer()->getOnlinePlayers() as $players){
						$players->sendMessage($message);
					}
				}
				
				if(explode($message . "'", $line)[1] == "to console"){
					$this->plugin->getServer()->getLogger()->info($message);
				}
			}
		}
		
		if($inter[0] == "broadcast"){
			$message = explode("broadcast ", $line)[1];
			$message = str_replace("'", "", $message);
			$this->plugin->getServer()->broadcastMessage($message);
		}
		
		if($inter[0] == "set"){
			if($inter[1] == "time"){
				if($inter[2] == "to"){
					if($inter[3] == "day"){
						$player->getLevel()->setTime(0);
					}
					
					if($inter[3] == "night"){
						$player->getLevel()->setTime(15000);
					}
				}
			}
			
			if($inter[2] == "to"){
				if($inter[1] !== "time"){
					$key = str_replace("{", "", str_replace("}", "", $inter[1]));
					$value = explode("to ", str_replace("'", "", $line))[1];
					if($value == "location of player"){
						$value = floor($player->getX()) . "," . floor($player->getY()) . "," . floor($player->getZ());
					}
					$this->plugin->getVariablesClass()->putVariable($key, $value);
				}
			}
			
			if(isset($inter[5])){
				if($inter[5] == "yaml"){
					if($inter[6] == "file"){
						if($inter[2] == "to"){
							$file = str_replace("'", "", $inter[7]);
							$key = str_replace("'", "", $inter[1]);
							$value = str_replace("'", "", $inter[3]);
							$this->plugin->getConfigClass()->set($file, $key, $value);
						}
					}
				}
			}
			
			if($inter[1] == "boss"){
				if($inter[2] == "bar"){
					if($inter[3] == "of"){
						if($inter[4] == "player"){
							if($inter[5] == "to"){
								$title = explode("'", $line)[1];
								$this->plugin->getBossbarClass()->setBossBar($player, $title);
								$this->plugin->getLogger()->warning("This function is experimental!");
							}
						}
					}
				}
			}
		}
		
		if($inter[0] == "display"){
			if($inter[1] == "board"){
				if($inter[2] == "named"){
					$title = explode("'", $line)[1];
					$this->plugin->getScoreboardClass()->displayScoreboard($player, $title);
					$this->plugin->getLogger()->warning("This function is experimental!");
				}
			}
		}
		
		if($inter[0] == "make"){
			if($inter[1] == "score"){
				$name = explode("'", $line)[1];
				$score = (int) explode("to ", $line)[1];
				$this->plugin->getScoreboardClass()->setScore($player, $name, $score);
				$this->plugin->getLogger()->warning("This function is experimental!");
			}
		}
		
		if($inter[0] == "wipe"){
			if($inter[1] == "player's"){
				if($inter[2] == "sidebar"){
					$this->plugin->getScoreboardClass()->wipeScoreboard($player);
					$this->plugin->getLogger()->warning("This function is experimental!");
				}
				
				if($inter[2] == "bossbar"){
					$this->plugin->getBossbarClass()->wipeBossbar($player);
					$this->plugin->getLogger()->warning("This function is experimental!");
				}
			}
		}
		
		if($inter[0] == "add"){
			$key = str_replace("{", "", str_replace("}", "", $inter[1]));
			$value = $inter[1];
			$this->plugin->getVariablesClass()->addToVariable($key, $value);
		}
		
		if($inter[0] == "delete"){
			$key = str_replace("{", "", str_replace("}", "", $inter[1]));
			$this->plugin->getVariablesClass()->deleteVariable($key);
			
		}
		
		if($inter[0] == "execute"){
			if($inter[1] == "console"){
				$command = str_replace("execute console command ", "", $line);
				$command = str_replace("'", "", $line);
				$this->plugin->getServer()->dispatchCommand(new ConsoleCommandSender(), $command);
			}
			
			if($inter[1] == "player"){
				$command = str_replace("execute player command ", "", $line);
				$command = str_replace("'", "", $line);
				$this->plugin->getServer()->dispatchCommand($player, $command);
			}
		}
		
		if($inter[0] == "teleport"){
			if($inter[1] == "player"){
				if($inter[2] == "to"){
					if($inter[3] == "location"){
						$loc = explode("at ", $line)[1];
						$loc = explode(" in", $line)[0];
						$loc = explode(", ", $line);
						$x = (int)$loc[0];
						$y = (int)$loc[1];
						$z = (int)$loc[2];
					}else{
						$x = (int)explode(",", $inter[3])[0];
						$y = (int)explode(",", $inter[3])[1];
						$z = (int)explode(",", $inter[3])[2];
					}
					$player->teleport(new Vector3($x, $y, $z));
				}
			}
		}
			   
		if($inter[0] == "heal"){ 
			if($inter[1] == "player"){
				if($inter[2] == "by"){
					$hearts = (int) $inter[3];
					$player = $player->setHealth($player->getHealth() + ($hearts * 2));
				}
			}
		}	

		if($inter[0] == "clear"){  
			if($inter[1] == "inventory"){
				if($inter[2] == "of"){
					if($inter[3] == "player"){
						$player->getInventory()->clearAll();
					}
				}
			}
		}
				
		if($inter[0] == "open"){
			if($inter[1] == "chest"){
				if($inter[2] == "with"){
					if($inter[4] == "rows"){
						if($inter[5] == "named"){
							$name = explode("'", $line)[1];
							$this->plugin->getGuiClass()->open($player, $name);
							$this->plugin->getLogger()->warning("This function is experimental!");
						}
					}
				}
			}
			
			if($inter[1] == "form"){ 
				if($inter[2] == "name"){
					$name = explode("'", $line)[1];
					$description = explode("'", $line)[3];
					$this->plugin->getFormClass()->open($player, $name, $description);
					$this->plugin->getLogger()->warning("This function is experimental!");
				}
			}
		}
		
		if($inter[0] == "format"){ 
			if($inter[1] == "slot"){
				$slot = (int) $inter[2];
				if($inter[3] == "of"){
					if($inter[4] == "player"){
						if($inter[5] == "with"){
							$count = (int) $inter[6];
							if($inter[7] == "of"){
								$item = $this->plugin->getItemMap()->get($inter[8]);
								if($inter[9] == "named"){
									if(strpos($line, "to be unstealable") !== false){
										$unstealable = true;
									}else{
										$unstealable = false;
									}
									
									$command = "";
									if(count(explode("[", $line)) > 1){
										if(count(explode("[", $line)) > 1){
											$exec = explode("]", explode("[", $line)[1])[0];
											$command = "";
											if(strpos($exec, "make player execute command ") !== false){
												$command = explode("'", $exec)[1];
											}
										}
									}
									
									$name = explode("'", $line)[1];
									$lore = explode("'", $line)[3];
									$item = Item::get($item, 0, $count);
									$item->setCustomName($name);
									$item->setLore(array($lore));
									$this->plugin->getGuiClass()->formatSlot($player, $slot, $item, true, $command);
									$this->plugin->getLogger()->warning("This function is experimental!");
								}
							}
						}
					}
				}
			}
		}
		
		if($inter[0] == "drop"){
			if($inter[1] == "a"){
				$item = $this->plugin->getItemMap()->get($inter[2]);
				if($inter[3] == "at"){
					if($inter[4] == "event-block"){
						$player->getLevel()->dropItem($event->getBlock(), Item::get($item, 0, 1));
					}
				}
			}
		}
		
		if($inter[0] == "give"){
			$item = str_replace("'", "", $this->plugin->getItemMap()->get($inter[3]));
			if($inter[2] == "of"){
				$count = (int) $inter[1];
				if($inter[8] == "all"){
					foreach($this->plugin->getServer()->getOnlinePlayers() as $players){
						$players->getInventory()->addItem(Item::get($item, 0, $count));
					}
				}else{
					$player->getInventory()->addItem(Item::get($item, 0, $count));
				}
			}
		}
		
		if($inter[0] == "remove"){
			$count = (int) $inter[1];
			$item = (int) $this->plugin->getItemMap()->get($inter[2]);
			if($inter[3] == "from"){
				if($inter[4] == "player"){
					$player->getInventory()->removeItem(Item::get($item, 0, $count));
				}
			}
		}
		
		if($inter[0] == "shoot"){
			if($inter[1] == "a"){
				if($inter[2] == "tnt"){
					if($inter[3] == "from"){
						if($inter[4] == "player"){
							$speed = 1;
							if($inter[5] == "with"){
								if($inter[6] == "speed"){
									$speed = (float) $inter[7];
								}
							}
							
							$nbt = Entity::createBaseNBT(new Vector3($player->getX(), $player->getY(), $player->getZ()), new Vector3(-sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI), -sin($player->pitch / 180 * M_PI), cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI)), $player->yaw, $player->pitch);
							$tnt = Entity::createEntity(65, $player->getLevel(), $nbt);
							$tnt->setMotion($tnt->getMotion()->multiply($speed)); 
							$tnt->spawnToAll();
						}
					}
				}
			}
		}
		
		if($inter[0] == "create"){
			if($inter[1] == "file"){
				$dir = explode("'", $line)[1];
				$file = new FileSystem();
				$file->newFile($dir, "");
			}
		}
		
		if($inter[0] == "replace"){
			$toReplace = str_replace("'" , "", $inter[1]);
			$withReplace = str_replace("'" , "", $inter[3]);
			if($event){
				if($event->getMessage()){
					$event->setMessage(str_replace($toReplace, $withReplace, $event->getMessage()));
				}
			}
		}
		
		if($inter[0] == "set"){
			if($inter[1] == "message"){
				if($inter[2] == "format"){
					if($inter[3] == "to"){
						if($event){
							if($event->getMessage()){
								$format = explode("set message format to ", $line)[1];
								$format = str_replace("%message%", $event->getMessage(), $format);
								$event->setCancelled(true);
								$this->plugin->getServer()->broadcastMessage(str_replace("'", "", $format));
							}
						}
					}
				}
			}
		}
		
		if($inter[0] == "apply"){
			if($inter[1] == "potion"){
				if($inter[2] == "of"){
					if($inter[4] == "of"){
						if($inter[5] == "tier"){
							if($inter[7] == "to"){
								if($inter[8] == "player"){
									if($inter[9] == "for"){
										$effect = Effect::getEffectByName($inter[3]);
										$amplification = $inter[6];
										if($inter[11] = "ticks"){
											$time = (int) $inter[10];
										}
										if($inter[11] = "seconds"){
											$time = (int) $inter[10]*20;
										}
										if($inter[11] = "minutes"){
											$time = (int) $inter[10]*20*60;
										}
												
										$player->addEffect(new EffectInstance($effect, $time, $amplification, true));
									}
								}
							}
						}
					}
				}
			}
		}
		
		if($inter[0] == "strike"){
			if($inter[1] == "lightning"){
				if($inter[2] == "at"){
					if($inter[3] == "player"){
						$lightning = new AddActorPacket();
						$lightning->type = 93;
						$lightning->entityRuntimeId = mt_rand(0, 9999999);
						$lightning->metadata = array();
						$lightning->motion = null;
						$lightning->yaw = $player->getYaw();
						$lightning->pitch = $player->getPitch();
						$lightning->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
						$this->plugin->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $lightning);
					}
				}
			}
		}
		
		if($inter[0] == "register"){ 
			if($inter[1] == "new"){
				if($inter[2] == "shaped"){
					if($inter[3] == "recipe"){
						if($inter[4] == "for"){
							$customName = explode("'", $line)[1];
							$result = Item::get($this->plugin->getItemMap()->get(explode(" named", explode("for ", $line)[1])[0]), 0)->setCustomName($customName);
							$itemsA = explode("using ", $line)[1];
							$itemsB = explode(", ", $itemsA);
							$items = array();
							foreach($itemsB as $item){
								array_push($items, $this->plugin->getItemMap()->get($item));
							}
							$crafting = new Crafting();
							$crafting->newRecipe($result, $items);
							$this->plugin->getLogger()->warning("This function is experimental!");
						}
					}
				}
			}
		}
		
		if($inter[0] == "cancel" and $inter[1] == "event"){
			$event->setCancelled(true);
		}
	}
	
	public function structureArrayMap(array $code) : array {
		$makeMap = false;
		$map = array();
		$currentMap = array();
		foreach($code as $line){
			if($makeMap == true){
				if($line == "else:" or $code[count($code)-1] == $line){
					if($code[count($code)-1] !== "else:"){
						array_push($currentMap, $line);
					}
			
					array_push($map, $currentMap);
					$makeMap = false;
					goto elseStart;
				}
				
				array_push($currentMap, $line);
			}
			
			elseStart:
			if($line == "else:"){
				$makeMap = true;
				$currentMap = array();
			}
		}
		return $map;
	}
}