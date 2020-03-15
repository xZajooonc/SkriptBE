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

use Skript\utils\EveryTimeTask;

class SkriptLoader {
    
    private $plugin;
    public $scripts = [];
	private $errorHandler;
	private $lang;
	public $loadedScripts = array();
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
        $this->scripts["onCommand"] = [];
        $this->scripts["onJoin"] = [];
        $this->scripts["onFirstJoin"] = [];
        $this->scripts["onQuit"] = [];
        $this->scripts["onChat"] = [];
        $this->scripts["onBreak"] = [];
        $this->scripts["onPlace"] = [];
		$this->scripts["onClick"] = [];
		$this->scripts["Options"] = [];
        $this->scripts["Variables"] = [];
        $this->scripts["EveryTimer"] = [];
		$this->errorHandler = new SkriptErrorHandler($plugin);
		$this->lang = $this->plugin->getLangClass();
    }
    
    public function loadAll() : void {
        $countScripts = 0;
        $fileList = glob("plugin_data/SkriptBE/scripts/*");
		$isError = false;
        foreach($fileList as $filePath){
            if(explode(".", $filePath)[count(explode(".", $filePath))-1] == "sk"){
                $fileName = explode("/", $filePath)[count(explode("/", $filePath))-1];
				$scriptName = explode(".", $fileName)[0];
                if($this->loadScript($fileName) == true){
					$countScripts++;
					array_push($this->loadedScripts, array("name" => $scriptName, "error" => false));
				}else{
					array_push($this->loadedScripts, array("name" => $scriptName, "error" => true));
					$isError = true;
				}
            }
        }
		
		if($isError == false){
			$this->plugin->logger($this->plugin->lang->translate("all.loaded")); 
		}
		$this->plugin->logger(str_replace("%count", $countScripts, $this->plugin->lang->translate("load.done"))); 
    }
	
	public function unloadAll() : void {
        $fileList = glob("plugin_data/SkriptBE/scripts/*");
        foreach($fileList as $filePath){
            if(explode(".", $filePath)[count(explode(".", $filePath))-1] == "sk"){
                $fileName = explode("/", $filePath)[count(explode("/", $filePath))-1];
				$scriptName = explode(".", $fileName)[0];
                $this->unloadScript($scriptName);
            }
        }
		$this->loadedScripts = array();
    }
    
    public function loadScript(string $name) : bool {
        $fileName = "plugin_data/SkriptBE/scripts/" . $name;
        if($name{0} !== "-" and $name{1} !== "-"){
            $scriptName = explode(".", $name)[0];
			$this->plugin->logger(str_replace("%script", $name, $this->lang->translate("enabling"))); 
			$this->scripts["onCommand"][$scriptName] = [];
            $this->scripts["onJoin"][$scriptName] = [];
            $this->scripts["onFirstJoin"][$scriptName] = [];
            $this->scripts["onQuit"][$scriptName] = [];
            $this->scripts["onChat"][$scriptName] = [];
            $this->scripts["onBreak"][$scriptName] = [];
            $this->scripts["onPlace"][$scriptName] = [];
			$this->scripts["onLoad"][$scriptName] = [];
			$this->scripts["onClick"][$scriptName] = [];
            $this->scripts["Options"][$scriptName] = [];
            $this->scripts["Variables"][$scriptName] = [];
            $this->scripts["EveryTimer"][$scriptName] = [];
			
            $functions = array();
            $function = array();
            $lineNumber = 0;
			$isError = false;
            foreach($this->formatScript($fileName) as $line){
				$lineNumber++;
				$line = $this->convertLine($line);
				$isError = $this->errorHandler->isError($scriptName, $line, $lineNumber);
				if($isError == true){
					return false;
				}
				
				if(strlen($line) > 2){
					if($line{0} !== "#"){
						array_push($function, $line);
					}
				}
				
				if(strlen($line) == 0){
					array_push($functions, $function);
					$function = array();
				}
            }
            
            foreach($functions as $f){
				$f = $this->plugin->getOptionsClass()->completeOptions($scriptName, $f);
				if(count($f) > 1){
					$pid = mt_rand(1, 100000);
					if(explode(" ", $f[0])[0] == "command"){
						$command = str_replace(":", "", explode(" ", $f[0])[1]);
						$orginalF = $f;
						$aliases = "";
						$usage = "";
						$description = "";
						$permission = "";
						$permission_message = "";
						foreach($f as $f1){
							if(explode(" ", $f1)[0] == "aliases:"){
								$aliases = explode(" ", $f1)[1];
								array_shift($orginalF);
							}	
						
							if(explode(" ", $f1)[0] == "usage:"){
								$usage = explode(" ", $f1)[1];
								array_shift($orginalF);
							}
						
							if(explode(" ", $f1)[0] == "description:"){
								$description = explode("description: ", $f1)[1];
								array_shift($orginalF);
							}
						
							if(explode(" ", $f1)[0] == "permission:" and $f1 !== "permission_message: "){
								$permission = explode(" ", $f1)[1];
								array_shift($orginalF);
							}
						
							if(explode(" ", $f1)[0] == "permission_message:"){
								$permission_message = explode("permission_message: ", $f1)[1];
								array_shift($orginalF);
							}
						}
						array_shift($orginalF);
						array_shift($orginalF);
					
						$this->plugin->getCommandsClass()->addCommand($command, $orginalF);
						$this->plugin->getCommandsClass()->addUsage($command, $usage);
						$this->plugin->getCommandsClass()->addDescription($command, $description);
						$this->plugin->getCommandsClass()->addPermission($command, $permission);
						$this->plugin->getCommandsClass()->addPermissionMessage($command, $permission_message);
						$this->scripts["onCommand"][$scriptName][$pid] = $command;
					}
				
					if($f[0] == "on join:"){
						array_shift($f);
						$this->scripts["onJoin"][$scriptName][$pid] = $f;
					}
                
					if($f[0] == "on first join:"){
						array_shift($f);
						$this->scripts["onFirstJoin"][$scriptName][$pid] = $f;
					}
                
					if($f[0] == "on quit:"){
						array_shift($f);
						$this->scripts["onQuit"][$scriptName][$pid] = $f;
					}
                
					if($f[0] == "on chat:"){
						array_shift($f);
						$this->scripts["onChat"][$scriptName][$pid] = $f;
					}
                
					if($f[0] == "on break:"){
						array_shift($f);
						$this->scripts["onBreak"][$scriptName][$pid] = [];
						$this->scripts["onBreak"][$scriptName][$pid]["code"] = $f;
					}
                
					if($f[0] == "on place:"){
						array_shift($f);
						$this->scripts["onPlace"][$scriptName][$pid] = [];
						$this->scripts["onPlace"][$scriptName][$pid]["code"] = $f;
					}
					
					if(strpos($f[0], "on place ") !== false){
						if(count(explode(" ", $f[0])) > 2){
							$item = $this->plugin->getItemMap()->get(substr(explode("on place ", $f[0])[1], 0, -1));
						
							$this->scripts["onPlace"][$scriptName][$pid] = [];
							$this->scripts["onPlace"][$scriptName][$pid]["code"] = $f;
							$this->scripts["onPlace"][$scriptName][$pid]["block-id"] = $item;
						}
					}
				
					if(strpos($f[0], "on break ") !== false){
						if(count(explode(" ", $f[0])) > 2){
							$item = $this->plugin->getItemMap()->get(substr(explode("on break ", $f[0])[1], 0, -1));
							$this->scripts["onBreak"][$scriptName][$pid] = [];
							$this->scripts["onBreak"][$scriptName][$pid]["code"] = $f;
							$this->scripts["onBreak"][$scriptName][$pid]["block-id"] = $item;
						}
					}
				
					if($f[0] == "on right click:" or $f[0] == "on left click:" or $f[0] == "on click:"){
						array_shift($f);
						$this->scripts["onClick"][$scriptName][$pid] = $f;
					}
				
					if($f[0] == "options:"){
						array_shift($f);
						foreach($f as $line){
							$key = explode(": ", $line)[0];
							$value = explode(": ", $line)[1];
							$this->plugin->getOptionsClass()->putOption($scriptName, $key, $value);
						}
						$this->scripts["Options"][$scriptName][$pid] = $f;
					}
				
					if($f[0] == "on script load:"){
						array_shift($f);
						$this->scripts["onLoad"][$scriptName][$pid] = $f;
					}
                
					if($f[0] == "variables:"){
						array_shift($f);
						$this->scripts["Variables"][$scriptName][$pid] = $f;
					}
                
					if(explode(" ", $f[0])[0] == "every"){
						$time = (int) explode(" ", $f[0])[1];
						switch(explode(" ", explode(":", $f[0])[0])[2]){
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
						array_shift($f);
						$this->scripts["EveryTimer"][$scriptName][$pid]["execute"] = $f;
						$this->scripts["EveryTimer"][$scriptName][$pid]["time"] = $time;
						$this->scripts["EveryTimer"][$scriptName][$pid]["taskId"] = 0;
					}
				}
			}
			$this->doLoad($scriptName);
			$this->doVariables($scriptName);
			$this->doEveryTimer($scriptName);
			$this->doOptions($scriptName);
			return true;
        }
		return false;
    }
    
    public function unloadScript(string $scriptName) : void {
		if($this->isLoaded($scriptName) == true){
			foreach($this->scripts["onCommand"][$scriptName] as $pid => $command){
				$this->plugin->getCommandsClass()->removeCommand($command);
			}
			unset($this->scripts["onCommand"][$scriptName]);
			unset($this->scripts["onJoin"][$scriptName]);
			unset($this->scripts["onFirstJoin"][$scriptName]);
			unset($this->scripts["onQuit"][$scriptName]);
			unset($this->scripts["onChat"][$scriptName]);
			unset($this->scripts["onBreak"][$scriptName]);
			unset($this->scripts["onPlace"][$scriptName]);
			unset($this->scripts["onLoad"][$scriptName]);
			unset($this->scripts["Options"][$scriptName]);
			unset($this->scripts["Variables"][$scriptName]);
			unset($this->scripts["onClick"][$scriptName]);
			foreach($this->scripts["EveryTimer"][$scriptName] as $script){
				$this->plugin->getScheduler()->cancelTask($script["taskId"]);
			}
			unset($this->scripts["EveryTimer"][$scriptName]);
		}
    }
	
	public function isLoaded(string $name) : bool {
		foreach($this->scripts as $scripts){
			foreach($scripts as $scriptName => $data){
				if($scriptName == $name){
					return true;
				}
			}
		}
		return false;
	}
    
    public function convertLine(string $line) : string {
		if(strlen($line) > 0){
			if(strpos($line, "#") !== false){
				if(strpos($line, '"') !== false){
					$text = explode('"', $line)[1];
					$startText = $text;
					$text = str_replace("#", "[crux]", $text);
					$line = str_replace($startText, $text, $line);
				}
				$line = explode("#", $line)[0];
			}
			$line = preg_replace("/\s+/S", " ", $line);
			if(isset($line{0})){
				if($line{0} == " "){
					$line = preg_replace("/ /", "", $line, 1);
				}
			}
			if(strlen($line) > 1 and $line{strlen($line)-1} == " "){
				$line = substr_replace($line ,"", -1);
			}
			if($this->plugin->config->get("color codes") == true){
				$line = str_replace("<red>", "&c", $line);
				$line = str_replace("<blue>", "&9", $line);
				$line = str_replace("<green>", "&2", $line);
				$line = str_replace("<lime>", "&a", $line);
				$line = str_replace("<orange>", "&6", $line);
				$line = str_replace("<yellow>", "&e", $line);
				$line = str_replace("<pink>", "&d", $line);
				$line = str_replace("<black>", "&0", $line);
			}
			$line = str_replace("&", base64_decode("wqc="), $line);
			$line = str_replace('"', "'", $line);
			$line = str_replace("permission message", "permission_message", $line);
			$line = str_replace(" [<text>]", "", $line);
			$line = str_replace("[crux]", "#", $line);
			return $line;
		}else{
			return "";
		}
    }
	
	public function formatScript($path) : array {
		$script = array();
		foreach(file($path) as $line){
			array_push($script, $line);
		}
		
		array_push($script, " ");
		return $script;
	}
    
    public function doLoad($scriptName) : void {
		foreach($this->scripts["onLoad"][$scriptName] as $pid){
			foreach($pid as $line){
				$this->plugin->getExecutorClass()->executeCode(null, $line, null);
			}
		}
    }
    
    public function doVariables(string $scriptName) : void {
        foreach($this->scripts["Variables"][$scriptName] as $pid){
			foreach($pid as $line){
				$variable = explode(" = ", $line);
				$key = $variable[0];
				$value = $variable[1];
				$this->plugin->getVariablesClass()->putVariable($key, $value);
			}
		}
    }
    
    public function doOptions(string $scriptName) : void {
        foreach($this->scripts["Options"] as $scripts){
			foreach($scripts as $pid){
				foreach($pid as $line){
					$key = explode(": ", $line)[0];
					$value = explode(": ", $line)[1];
					$this->plugin->getOptionsClass()->putOption($scriptName, $key, $value);
				}
			}
		}
    }
	
    public function doEveryTimer(string $scriptName) : void {
        foreach($this->scripts["EveryTimer"] as $scripts){
			foreach($scripts as $pid => $script){
				if(isset($script["execute"])){
					$task = $this->plugin->getScheduler()->scheduleRepeatingTask(new EveryTimeTask($this->plugin, $script["execute"]), $script["time"]);
					$this->scripts["EveryTimer"][$scriptName][$pid]["taskId"] = $task->getTaskId();
				}
			}
		}
    }
}