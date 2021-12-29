<?php

declare(strict_types=1);

namespace J0k3rrWild\JailMate;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\level\Position;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;

//events
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\block\BlockBreakEvent;


class Main extends PluginBase implements Listener{

public $acronym;
public $deco;
public $cfg;


    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml"); 
        $this->saveResource("players.json"); 
        $this->cfg = $this->getDataFolder() . 'players.json';
        $json = file_get_contents($this->cfg);
        $this->deco = json_decode($json, true);
        // array_push($deco, "ez");
        // $imp = implode(",", $deco);
        
        // $arr = explode(",", $imp);
        // file_put_contents($cfg, json_encode($deco));
        // var_dump($deco);
        
        
    }

    public function onCommand(CommandSender $p, Command $cmd, string $label, array $args) : bool{
        if(!isset($args[0])) return false;
 
        switch($cmd){
            case "jail":
                
             if($p->hasPermission("jail.jail") || $p->hasPermission("jail")){  
                if($player = $this->getServer()->getPlayer($args[0])){

                $conf = $this->getConfig();
                $getx = $conf->get("jail-X");

                if($p===$player){
                    $p->sendMessage(TF::RED."[JailMate] > You can't arrest yourself");
                    break;
                }

                if($player->hasPermission("jail.bypass") || $player->hasPermission("jail")){
                    $p->sendMessage(TF::RED."[JailMate] > You cannot arrest a player ".$player->getName());
                    break;
                }

                if(($getx === NULL) || ($getx === false)){
                    $p->sendMessage(TF::RED."[JailMate] > There is no set up arrest postition");
                    return true;
                }
               
                if(in_array($player->getName(), $this->deco)){
                    $p->sendMessage(TF::RED."[JailMate] > The player is currently incapacitated!");
                    break;
                }else{
                     //Save jailed player to array and config
                    array_push($this->deco, $player->getName());
                    file_put_contents($this->cfg, json_encode($this->deco));
                    
                }
                    //Get jail pos
                    
                    
                    $gety = $conf->get("jail-Y");
                    $getz = $conf->get("jail-Z");
                    $level = $conf->get("world");

                    $tp = new Position($getx, $gety, $getz, $this->getServer()->getLevelByName($level));
                    $player->teleport($tp);
                    $player->sendTitle(TF::RED."WARNING!", "Your gameplay has been stopped", 50, 100, 50);
                    $p->sendMessage(TF::GREEN."[JailMate] > Gameplay player ".$player->getName()." has been stopped, teleport to the player to be interrogated");
                    $player->sendMessage(TF::RED."[JailMate] > Your gameplay has been stopped, follow the instructions of the checking administrator.");
                    }else{
                        $p->sendMessage(TF::RED."[JailMate] > Player is offline or nickname incorrect");
                    }
             }else{
                $p->sendMessage(TF::RED."[JailMate] > You are not authorized to use this command!");
             }
                    break;
            case "setjail":
                
             if($p->hasPermission("jail.setjail") || $p->hasPermission("jail")){
              if($args[0]==="confirm"){

                if(!($p instanceof Player)){
                    $p->sendMessage(TF::RED."[JailMate] > You cannot set spawn while in the console!");
                    return false;
                }
                
             
        
                $conf = $this->getConfig();
                $getx = round($p->getX());
                $gety = round($p->getY());
                $getz = round($p->getZ());
                $level = $p->getLevel()->getName();
                    
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
             break;
            case "unjail":
             if($p->hasPermission("jail.jail") || $p->hasPermission("jail")){
                $player = $this->getServer()->getPlayer($args[0]);
                if($player && in_array($player->getName(), $this->deco)){
                    $new = array_diff($this->deco, array($player->getName()));
                    file_put_contents($this->cfg, json_encode($new));
                    $json = file_get_contents($this->cfg);
                    $this->deco = json_decode($json, true);
                    $player->teleport($player->getLevel()->getSafeSpawn());
                    $p->sendMessage(TF::GREEN."[JailMate] > The player has been released from arrest");
                    $player->sendMessage(TF::GREEN."[JailMate] > The administrator released you from the arrest!");
            }else{
                $p->sendMessage(TF::RED."[JailMate] > The player is not under arrest!");
            
            }
         }
         break;
    }
        return true;


     }


    public function onMoveJailed(PlayerMoveEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendActionBarMessage(TF::RED."You cannot move during the interrogation");
            $e->setCancelled(true);

        }

    }

    public function onCommandJailed(PlayerCommandPreprocessEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot use commands during questioning");
            $command = explode(" ", strtolower($e->getMessage()));
            $this->acronym = "";
            foreach ($command as $w) {
                $this->acronym .= $w[0];
                
              }
            // var_dump($command);
            if($this->acronym === "/"){
             $e->setCancelled(true);
            }else{
             $e->setCancelled(false);
            }
        }
    } 
    
    public function RageQuitBan(PlayerQuitEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
           $player = $e->getPlayer();
           $this->getServer()->broadcastmessage(TF::GREEN."[JailMate] > Left ".$player->getName()." left during the interrogation, which results in a ban.");
          
           $new = array_diff($this->deco, array($player->getName()));
           file_put_contents($this->cfg, json_encode($new));
           $json = file_get_contents($this->cfg);
           $this->deco = json_decode($json, true);

            $player->setBanned(true);
           
            
        }
    }

    public function onPlayerJailedItemDrop(PlayerDropItemEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot throw away items during the interrogation");
            $e->setCancelled(true);
           
            
        }
    }

    public function onJailedBlockPlace(BlockPlaceEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot put blocks during an interrogation");
            $e->setCancelled(true);
           
            
        }
    }

    public function onJailedBlockBreak(BlockBreakEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot destroy blocks during an interrogation");
            $e->setCancelled(true);
           
            
        }
    }

    public function onTryingPlayerBypassEvent(PlayerJoinEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > Your interview is not complete, please wait for the reviewing administrator");
            $conf = $this->getConfig();
            $getx = $conf->get("jail-X");
            $gety = $conf->get("jail-Y");
            $getz = $conf->get("jail-Z");
            $level = $conf->get("world");
            
            $tp = new Position($getx, $gety, $getz, $this->getServer()->getLevelByName($level));
            $e->getPlayer()->teleport($tp);
           
            
        }
    }


    }

