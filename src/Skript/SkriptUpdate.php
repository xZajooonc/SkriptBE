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

class SkriptUpdate {
    
    private $plugin;
	
	private $url = "skriptbe.ga";
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
    }
    
    public function isConnection() : bool {
		if($this->getUrl("http://" . $this->url . "/connection.php") == "1"){
			return true;
		}
		return false;
	}
	
	public function isUpdate() : bool {
        $this->plugin->logger($this->plugin->lang->translate("checking.update")); 
        $ver = $this->getUrl("http://" . $this->url . "/update/getVersion.php");
        if($ver !== $this->plugin->getVersionSkript()){
			$this->plugin->logger($this->plugin->lang->translate("update.available")); 
			return true;
        }else{
			$this->plugin->logger($this->plugin->lang->translate("running.latest")); 
			return false;
        }
    }
    
    public function doUpdate() : void {
        $this->plugin->logger($this->plugin->lang->translate("update.incomming")); 
		$ver = $this->getUrl("http://" . $this->url . "/update/getVersion.php");
        file_put_contents("plugins/SkriptBE_v" . $ver . ".phar", $this->getUrl("http://" . $this->url . "/release/SkriptBE_v" . $ver . ".phar"));
        unlink("plugins/SkriptBE_v" . $this->plugin->getVersionSkript() . ".phar");
        $this->plugin->logger($this->plugin->lang->translate("update.done")); 
        $this->plugin->getServer()->shutdown();
    }
	
	public function getUrl(string $url) : string {
		$arrContextOptions = array(
			"ssl" => array(
				"verify_peer" => false,
				"verify_peer_name" => false
			)
		);  

		$response = file_get_contents($url, false, stream_context_create($arrContextOptions));
		return $response;
	}
}