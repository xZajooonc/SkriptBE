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

use Exception;
use pocketmine\command\Command as PluginCommand;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Skript\commands\Command;
use Skript\utils\Options;
use Skript\utils\Variables;
use Skript\utils\ItemMap;
use Skript\utils\Config as Configs;
use Skript\experimental\ChestGUI;
use Skript\experimental\FormUI;
use Skript\experimental\Crafting;
use Skript\experimental\BossBar;
use Skript\experimental\Scoreboard;

class SkriptMain extends PluginBase implements Listener {
    
	public $config;
    public $command;
	public $commands;
    public $lang;
    public $updater;
    public $loader;
	public $executor;
	public $options;
	public $variables;
	public $configs;
	public $gui;
	public $form;
	public $bossbar;
	public $scoreboard;
    
    public function onEnable() : void {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "scripts");
		if(!file_exists($this->getDataFolder() . "config.yml")){
            $this->saveDefaultConfig();
        }
        $this->saveResource("config.yml");
		if(!file_exists($this->getDataFolder() . "lang.yml")){
            $this->saveDefaultConfig();
        }
        $this->saveResource("lang.yml");
		
		if(!is_file($this->getDataFolder(). "variables.txt")){
			foreach($this->getResources() as $path => $resource){
				$this->saveResource($path);
			}
		}
		
		$this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if(!is_file($this->getDataFolder(). "variables.txt")){
			if($this->getServer()->getLanguage()->getName() == "Polski"){
				$this->config->set("language", "polish");
				$this->config->save();
			}else{
				$this->config->set("language", "english");
				$this->config->save();
			}
        }
		$this->getServer()->getPluginManager()->registerEvents(new SkriptListener($this), $this);
		$this->makeVariabesFile();
		$this->makeLogFile();
		
		$this->commands = new Command($this);
		$this->options = new Options($this);
		$this->configs = new Configs($this);
		$this->variables = new Variables($this);
		$this->gui = new ChestGUI($this);
		$this->form = new FormUI($this);
		$this->bossbar = new BossBar($this);
		$this->scoreboard = new Scoreboard($this);
		
		$this->executor = new SkriptExecutor($this);
        $this->command = new SkriptCommand($this);
        $this->lang = new SkriptLang($this);
		$this->updater = new SkriptUpdate($this);
		if($this->config->get("check for new version") == true){	
			if($this->updater->isConnection()){
				if($this->updater->isUpdate()){
					$this->updater->doUpdate();
				}else{
					$this->loadScripts();
				}
			}else{
				$this->loadScripts();
				$this->logger($this->lang->translate("error.connection")); 
			}
		}else{
			$this->loadScripts();
		}
    }
	
	public function loadScripts() : void {
		$this->loader = new SkriptLoader($this);
        $this->loader->loadAll();
	}
	
	public function makeVariabesFile() : void {
		if(!is_file($this->getDataFolder(). "variables.txt")){
			file_put_contents($this->getDataFolder() . "variables.txt", "");
		}
	}
	
	public function makeLogFile() : void {
		if(!is_file("log.txt")){
			file_put_contents("log.txt", "");
		}
	}
    
    public function logger(string $string) : void {
        $this->getServer()->getLogger()->info($this->format($string));
    }
    
    public function format(string $string) : string {
		return TextFormat::WHITE . "[" . TextFormat::YELLOW . "Skript" . TextFormat::WHITE . "] " . $string;
    }
    
    public function getVersionSkript() : string {
        return "1.0.0";
    }
    
    public function getCommandsClass() : Command {
        return $this->commands;
    }
    
    public function getCommandClass() : SkriptCommand {
        return $this->command;
    }
    
    public function getLangClass() : SkriptLang {
        return $this->lang;
    }
    
    public function getUpdaterClass() : SkriptUpdate {
        return $this->updater;
    }
    
    public function getLoaderClass() : SkriptLoader {
        return $this->loader;
    }
	
	public function getOptionsClass() : Options {
        return $this->options;
    }
	
	public function getVariablesClass() : Variables {
        return $this->variables;
    }
	
	public function getConfigClass() : Configs {
        return $this->configs;
    }
	
	public function getExecutorClass() : SkriptExecutor {
        return $this->executor;
    }
	
	public function getGuiClass() : ChestGUI {
        return $this->gui;
    }
	
	public function getFormClass() : FormUI {
        return $this->form;
    }
	
	public function getBossbarClass() : BossBar {
        return $this->bossbar;
    }
	
	public function getScoreboardClass() : Scoreboard {
        return $this->scoreboard;
    }
	
	public function getItemMap() : ItemMap {
        return new ItemMap();
    }
	
	public function getPluginApi() : array {
		$plugins = [];
		if($this->getServer()->getPluginManager()->getPlugin("FactionsPro")){
			$plugins["FactionsPro"] = $this->getServer()->getPluginManager()->getPlugin("FactionsPro");
		}
		
		if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")){
			$plugins["EconomyAPI"] = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
		}
		return $plugins;
	}
    
    public function onCommand(CommandSender $sender, PluginCommand $command, string $label, array $args) : bool {
        $this->command->onCommand($sender, $command, $label, $args);
        return true;
    }
}