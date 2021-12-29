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
use pocketmine\plugin\{PluginOwned, PluginOwnedTrait};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\math\Vector3;
use pocketmine\Server;
use J0k3rrWild\JailMate\Commands\Jail;
use J0k3rrWild\JailMate\Commands\Setjail;
use J0k3rrWild\JailMate\Commands\Unjail;

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


    public function onEnable():void {
        $server = $this->getServer();
        $server->getCommandMap()->register("jail", new Commands\Jail($this));
        $server->getCommandMap()->register("setjail", new Commands\Setjail($this));
        $server->getCommandMap()->register("unjail", new Commands\Unjail($this));
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml"); 
        $this->saveResource("players.json"); 
        $this->cfg = $this->getDataFolder() . 'players.json';
        $json = file_get_contents($this->cfg);
        $this->deco = json_decode($json, true);
       
        
        
    }


    public function onMoveJailed(PlayerMoveEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendActionBarMessage(TF::RED."You cannot move during the interrogation");
            $e->cancel();

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
            
            if($this->acronym === "/"){
                $e->cancel();
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

            $player->getServer()->getNameBans()->addBan($player->getName(), "Rage quit while player checking", null, "JailMate");
           
            
        }
    }

    public function onPlayerJailedItemDrop(PlayerDropItemEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot throw away items during the interrogation");
            $e->cancel();
           
            
        }
    }

    public function onJailedBlockPlace(BlockPlaceEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot put blocks during an interrogation");
            $e->cancel();
           
            
        }
    }

    public function onJailedBlockBreak(BlockBreakEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > You cannot destroy blocks during an interrogation");
            $e->cancel();
           
            
        }
    }

    public function onTryingPlayerBypassEvent(PlayerJoinEvent $e){
        if(in_array($e->getPlayer()->getName(), $this->deco)){
            $e->getPlayer()->sendMessage(TF::RED."[JailMate] > Your interview is not complete, please wait for the reviewing administrator");
            $conf = $this->getConfig();
            $getx = $conf->get("jail-X");
            $gety = $conf->get("jail-Y");
            $getz = $conf->get("jail-Z");
            $world = $conf->get("world");
            $level = Server::getInstance()->getWorldManager()->getWorldByName($world);
            
            $vect = new Vector3($getx, $gety, $getz, $level);
          
           

            $e->getPlayer()->teleport($vect);
           
           
            
        }
    }


    }

