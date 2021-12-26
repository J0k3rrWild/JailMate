<?php

declare(strict_types=1);

namespace J0k3rrWild\JailMate\Commands;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\world\Position;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\plugin\{PluginOwned, PluginOwnedTrait};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use J0k3rrWild\JailMate\Main;
use pocketmine\Server;
use pocketmine\math\Vector3;


class Jail extends Command implements PluginOwned{
    use PluginOwnedTrait;

    public function __construct(Main $plugin){
		parent::__construct("jail", "Arrests the player", "/jail <nick>");
		$this->setPermission("jail.jail");
		$this->plugin = $plugin;
	}








public function execute(CommandSender $p, string $label, array $args){
    if(!isset($args[0])){ 
        throw new InvalidCommandSyntaxException;
        return false;
   }
                
             if($p->hasPermission("jail.jail") || $p->hasPermission("jail")){  
                if($player = $this->plugin->getServer()->getPlayerExact($args[0])){
                    $conf = $this->plugin->getConfig();
                    $getx = $conf->get("jail-X");
                     

                if($p===$player){
                    $p->sendMessage(TF::RED."[JailMate] > You can't arrest yourself");
                    return true;
                }
     
                if($player->hasPermission("jail.bypass") || $player->hasPermission("jail")){
                    $p->sendMessage(TF::RED."[JailMate] > You cannot arrest a player ".$player->getName());
                    return true;
                } 

                if(($getx === NULL) || ($getx === false)){
                    $p->sendMessage(TF::RED."[JailMate] > There is no set up arrest postition");
                    return true;
                }
               
                if(in_array($player->getName(), $this->plugin->deco)){
                    $p->sendMessage(TF::RED."[JailMate] > The player is currently incapacitated!");
                    return true;
                }else{
                     //Save jailed player to array and config
                    array_push($this->plugin->deco, $player->getName());
                    file_put_contents($this->plugin->cfg, json_encode($this->plugin->deco));
                    
                }
                        //Get jail pos
                        
                        $gety = $conf->get("jail-Y");
                        $getz = $conf->get("jail-Z");
                        $world = $conf->get("world");
                        $level = Server::getInstance()->getWorldManager()->getWorldByName($world);
                        
                        $vect = new Vector3($getx, $gety, $getz, $level);

                        // $tp = new Position($getx, $gety, $getz, $level);
                        $player->teleport($vect);
                        $player->sendTitle(TF::RED."WARNING!", "Your gameplay has been stopped", 50, 100, 50);
                        $p->sendMessage(TF::GREEN."[JailMate] > Gameplay player ".$player->getName()." has been stopped, teleport to the player to be interrogated");
                        $player->sendMessage(TF::RED."[JailMate] > Your gameplay has been stopped, follow the instructions of the checking administrator.");
                    }else{
                        $p->sendMessage(TF::RED."[JailMate] > Player is offline or nickname incorrect");
                    }
             }else{
                 $p->sendMessage(TF::RED."[JailMate] > You are not authorized to use this command!");
             }
            
             return true;
    }
      


}
