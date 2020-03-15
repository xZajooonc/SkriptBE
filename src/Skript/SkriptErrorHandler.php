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

class SkriptErrorHandler {
    
    public $plugin;
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
    }
    
    public function isError(string $scriptName, string $line, int $lineNumber) : void {
        if(strpos($line, "'") !== false){
			if(substr_count($line, "'") % 2 !== 0){
				if(strpos($line, "player's") == false and strpos($line, "doesn't") == false and strpos($line, "isn't") == false){
					$this->throwError($scriptName, $lineNumber, 1, $line);
				}
			}
        }
        
        if(strpos($line, "}") !== false){
			if(strpos($line, "{") !== false){
				if(substr_count($line, "}") !== substr_count($line, "}")){
					$this->throwError($scriptName, $lineNumber, 2, $line);
				}
			}
        }
    }
    
    public function throwError(string $scriptName, int $line, int $error, string $code) : bool {
        switch($error) {
            case 1:
            $this->plugin->logger(str_replace("%line", $line, str_replace("%reason", "unexped ' in line", str_replace("%code", $code, str_replace("%name", $scriptName . ".sk", $this->plugin->lang->translate("unexpect.error"))))));   
            break;
            
            case 2:
            $this->plugin->logger(str_replace("%line", $line, str_replace("%reason", "unexped } or { in line", str_replace("%code", $code, str_replace("%name", $scriptName . ".sk", $this->plugin->lang->translate("unexpect.error"))))));
            break;
            
        }
        return true;
    }
}