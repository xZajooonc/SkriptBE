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

namespace Skript\utils;

use Skript\SkriptMain;

class FileSystem {
    
    public function newFile($dir, $content) : void {
		$folders = explode("/", $dir);
		$current = "";
		foreach($folders as $folder){
			if(isset(explode(".", $folder)[1])){
				file_put_contents(substr($current, 1) . "/" . $folder, "--- []" . PHP_EOL . "...");
			}else{
				$current = $current . "/" . $folder;
				if(!is_file(substr($current, 1))){
					@mkdir(substr($current, 1));
				}
			}
		}
	}
}