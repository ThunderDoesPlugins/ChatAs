<?php
declare(strict_types=1);
/** Created By Thunder33345 **/
namespace Thunder33345\ChatAs;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class ChatAs extends PluginBase implements Listener
{
  public function onLoad()
  {

  }

  public function onEnable()
  {

  }

  public function onDisable()
  {

  }


  public function onCommand(CommandSender $sender, Command $command, string $label, array $args):bool
  {
    $cmds = ['chatas', 'chatasexact', 'hiddenchatas', 'hiddenchatasexact'];
    $invalid = true;
    foreach($cmds as $cmd){
      if($command->getName() == $cmd){
        $invalid = false;
        break;
      }
    }
    if($invalid) return true;
    if(!$sender->hasPermission('chatas.use')){
      $sender->sendMessage(TextFormat::RED."Insufficient Permission");
      return true;
    }
    if(count($args) < 2){
      $sender->sendMessage(TextFormat::RED."/$label <player> <message>");
      return true;
    }
    $stringAs = $args[0];
    $chatAs = null;
    switch($command->getName()){
      case "chatas":
        $chatAs = $this->getServer()->getPlayer($stringAs);
        break;
      case "chatasexact":
        $chatAs = $this->getServer()->getPlayerExact($stringAs);
        break;
    }
    if($chatAs === null){
      $sender->sendMessage(TextFormat::RED.'Player "'.$stringAs.'" not found!');
      return true;
    }
    $hidden = false;
    switch($command->getName()){
      case "hiddenchatas":
      case "hiddenchatasexact":
        $hidden = true;
        break;
    }
    array_shift($args);
    $message = implode(' ', $args);
    $this->getServer()->getPluginManager()->callEvent($ev = new PlayerChatEvent($chatAs, $message));
    //cancelled check removed intentionally acting as it was said by a OP
    if($hidden){
      $recipients = $ev->getRecipients();
      foreach($recipients as $key => $player){
        if(!$player instanceof Player) continue;
        if($player->getLowerCaseName() === $chatAs->getLowerCaseName()) unset($recipients[$key]);//unset to prevent the sendas from seeing it
      }
      $ev->setRecipients($recipients);
    }
    $this->getServer()->broadcastMessage($this->getServer()->getLanguage()->translateString($ev->getFormat(), [
     $ev->getPlayer()->getDisplayName(),
     $ev->getMessage(),
    ]), $ev->getRecipients());
    $sender->sendMessage('Successfully said as '.$chatAs->getName());
    return true;
  }
}