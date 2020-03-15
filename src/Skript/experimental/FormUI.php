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
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class FormUI {
	
	private $plugin;
	private $forms = [];
	private $form = 0;
    
    public function __construct(SkriptMain $plugin) {
        $this->plugin = $plugin;
	}
	
	public function open($player, $name, $description = "", $buttons = array()) : void {
		$this->form = $this->form+1;
		$pk = new ModalFormRequestPacket();
        $form = array();
        $form["title"] = $name;
        $form["type"] = "form";
        $form["content"] = $description;
		$form["buttons"] = $buttons;
        $pk->formId = $this->form;
        $pk->formData = json_encode($form);
        $player->dataPacket($pk);
	}
    
}