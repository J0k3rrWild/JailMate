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


class Unjail extends Command implements PluginOwned{
    use PluginOwnedTrait;

    public function __construct(Main $plugin){
		parent::__construct("unjail", "Unjail player", "/unjail <player>");
		$this->setPermission("jail.jail");
		$this->plugin = $plugin;
	}

public function execute(CommandSender $p, string $label, array $args){
    if(!isset($args[0])){ 
        throw new InvalidCommandSyntaxException;
        return false;
}
 
             if($p->hasPermission("jail.jail") || $p->hasPermission("jail")){
                $player = $this->plugin->getServer()->getPlayerExact($args[0]);
                if($player && in_array($player->getName(), $this->plugin->deco)){
                    $new = array_diff($this->plugin->deco, array($player->getName()));
                    file_put_contents($this->plugin->cfg, json_encode($new));
                    $json = file_get_contents($this->plugin->cfg);
                    $this->plugin->deco = json_decode($json, true);
                    $player->teleport($player->getWorld()->getSafeSpawn());
                    $p->sendMessage(TF::GREEN."[JailMate] > The player has been released from arrest");
                    $player->sendMessage(TF::GREEN."[JailMate] > The administrator released you from the arrest!");
            }else{
                $p->sendMessage(TF::RED."[JailMate] > The player is not under arrest!");
            }
         }
         return true;
    }
        


}
