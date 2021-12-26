<?php

declare(strict_types=1);

namespace J0k3rrWild\JailMate\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\plugin\{PluginOwned, PluginOwnedTrait};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use J0k3rrWild\JailMate\Main;
use pocketmine\entity\Entity;
use pocketmine\world\Position;
use pocketmine\player\Player;


class Setjail extends Command implements PluginOwned{
    use PluginOwnedTrait;

    public function __construct(Main $plugin){
		parent::__construct("setjail", "Set jail position", "/setjail confirm");
		$this->setPermission("jail.setjail");
		$this->plugin = $plugin;
	}



    




    public function execute(CommandSender $p, string $label, array $args){
        if(!isset($args[0])){ 
            throw new InvalidCommandSyntaxException;
            return false;
    }

    if($p->hasPermission("jail.setjail") || $p->hasPermission("jail")){
        if($args[0]==="confirm"){

        if(!($p instanceof Player)){
            $p->sendMessage(TF::RED."[JailMate] > You cannot set spawn while in the console!");
            return false;
        }
        
      
        $level = $p->getWorld()->getFolderName();
         
        

        $conf = $this->plugin->getConfig();
        $getx = round($p->getPosition()->getX());
        $gety = round($p->getPosition()->getY());
        $getz = round($p->getPosition()->getZ());
        
            
        $conf->set("jail-X", $getx);
        $conf->set("jail-Y", $gety);
        $conf->set("jail-Z", $getz);
        $conf->set("world", $level);
        $conf->save();
        $p->sendMessage(TF::GREEN."[JailMate] > The arrest was setted in position X:{$getx}/Y:{$gety}/Z:{$getz} World: $level");
        }else{
        $p->sendMessage(TF::RED."[JailMate] > You need to confirm the establishment of a new arrest spawn, enter /setjail confirm");
        }
    }else{
        $p->sendMessage(TF::RED."[JailMate] > You are not authorized to use this command!");
    }
  
    }
}