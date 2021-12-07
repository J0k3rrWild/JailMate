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
        $this->getLogger()->info(TF::GREEN."[MateJail] > Plugin oraz konfiguracja zostały załadowane pomyślnie");
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
                
                
                if($p===$player){
                    $p->sendMessage(TF::RED."[MeetMate] > Nie możesz aresztować samego siebie");
                    break;
                }

                if($player->hasPermission("jail.bypass") || $player->hasPermission("jail")){
                    $p->sendMessage(TF::RED."[MeetMate] > Nie możesz aresztować gracza ".$player->getName()." ponieważ posiada veto");
                    break;
                }
               
                if(in_array($player->getName(), $this->deco)){
                    $p->sendMessage(TF::RED."[MeetMate] > Gracz jest już aktualnie ubezwłasnowlniony!");
                    break;
                }else{
                     //Save jailed player to array and config
                    array_push($this->deco, $player->getName());
                    file_put_contents($this->cfg, json_encode($this->deco));
                    
                }
                    //Get jail pos
                    $conf = $this->getConfig();
                    $getx = $conf->get("jail-X");
                    $gety = $conf->get("jail-Y");
                    $getz = $conf->get("jail-Z");
                    $level = $player->getLevel();

                    $tp = new Position($getx, $gety, $getz, $level);
                    $player->teleport($tp);
                    $player->addTitle(TF::RED."UWAGA!", "Twoja rozgrywka została zatrzymana", 50, 100, 50);
                    $p->sendMessage(TF::GREEN."[MeetMate] > Rozgrywka gracza ".$player->getName()." została zatrzymana, teleportuj sie do gracza by poddać go przesłuchaniu");
                    $player->sendMessage(TF::RED."[MeetMate] > Twoja rozgrywka została zatrzymana, wykonuj polecenia administratora sprawdzającego.");
                    }else{
                        $p->sendMessage(TF::RED."[MeetMate] > Gracz jest offline lub niepoprawny nick");
                    }
             }else{
                 $p->sendMessage(TF::RED."[MeetMate] > Nie masz uprawnień by używać tej komendy");
             }
                    break;
            case "setjail":
                
             if($p->hasPermission("jail.setjail") || $p->hasPermission("jail")){
              if($args[0]==="confirm"){
                
                // $p->sendMessage(TF::RED."[JailMate] > Nie możesz ustawić spawnu będąc w konsoli!");
        
                $conf = $this->getConfig();
                $getx = round($p->getX());
                $gety = round($p->getY());
                $getz = round($p->getZ());
                    
                $conf->set("jail-X", $getx);
                $conf->set("jail-Y", $gety);
                $conf->set("jail-Z", $getz);
                $conf->save();
                $p->sendMessage(TF::GREEN."[MeetMate] > Ustawiono areszt na pozycji X:{$getx}/Y:{$gety}/Z:{$getz}");
              }else{
                $p->sendMessage(TF::RED."[JailMate] > Musisz potwierdzić ustanowienie nowego spawnu, wpisz /setjail confirm");
              }
             }else{
                $p->sendMessage(TF::RED."[JailMate] > Nie masz uprawnień by używać tej komendy");
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
                    $p->sendMessage(TF::GREEN."[MeetMate] > Gracz został wypuszczony z aresztu");
                    $player->sendMessage(TF::GREEN."[MeetMate] > Administrator wypuścił cię ze przesłuchania");
            }else{
                $p->sendMessage(TF::RED."[MeetMate] > Gracz nie jest aresztowany!");
            }
         }
         break;
    }
        return true;


     }


    public function onMoveJailed(PlayerMoveEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->addActionBarMessage(TF::RED."Nie możesz się ruszać podczas przesłuchania");
            $e->setCancelled(true);

        }

    }

    public function onCommandJailed(PlayerCommandPreprocessEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[MeetMate] > Nie możesz używać komend podczas przesłuchania");
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
           $this->getServer()->broadcastmessage(TF::GREEN."[MeetMate] > Gracz ".$player->getName()." wyszedł podczas przesłuchania co skutkuje banem.");
          
           $new = array_diff($this->deco, array($player->getName()));
           file_put_contents($this->cfg, json_encode($new));
           $json = file_get_contents($this->cfg);
           $this->deco = json_decode($json, true);

            $player->setBanned(true);
           
            
        }
    }

    public function onPlayerJailedItemDrop(PlayerDropItemEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[MeetMate] > Nie możesz wyrzucać przedmiotów podczas przesłuchania");
            $e->setCancelled(true);
           
            
        }
    }

    public function onJailedBlockPlace(BlockPlaceEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[MeetMate] > Nie możesz kłaść bloków podczas przesłuchania");
            $e->setCancelled(true);
           
            
        }
    }

    public function onJailedBlockBreak(BlockBreakEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[MeetMate] > Nie możesz niszczyć bloków podczas przesłuchania");
            $e->setCancelled(true);
           
            
        }
    }

    public function onTryingPlayerBypassEvent(PlayerJoinEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[MeetMate] > Twoje przesłuchanie nie dobiegło końca, prosze zaczekaj na administratora sprawdzającego");
            $conf = $this->getConfig();
            $getx = $conf->get("jail-X");
            $gety = $conf->get("jail-Y");
            $getz = $conf->get("jail-Z");
            $level = $e->getPlayer()->getLevel();

            $tp = new Position($getx, $gety, $getz, $level);
            $e->getPlayer()->teleport($tp);
           
            
        }
    }


    }

