<?php
namespace mohagames\handboeien;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;


class main extends PluginBase implements Listener{

    public $arrested = array();

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch($command->getName()){
            case "handboeien":
                $item = ItemFactory::get(ItemIds::LEAD);
                $item->setCustomName("Handboeien");
                $sender->getInventory()->addItem($item);
                return true;
            default:
                return false;
        }
    }

    public function arrestatie(EntityDamageByEntityEvent $e){
        $target = $e->getEntity();
        if($target instanceof Player && $e->getDamager() instanceof Player){
            $item = $e->getDamager()->getInventory()->getItemInHand();
            if($item->getId() == ItemIds::LEAD && $item->getCustomName() == "Handboeien"){
                if(isset($this->arrested[$e->getEntity()->getName()])){
                    $e->setCancelled();
                    $target->removeEffect(EFFECT::BLINDNESS);
                    $target->removeEffect(EFFECT::SLOWNESS);
                    unset($this->arrested[$target->getName()]);
                    $target->sendMessage("§aU bent vrij");
                    $e->getDamager()->sendMessage("§aU hebt iemand vrijgelaten");
                }
                else{
                    $e->setCancelled();
                    $effect = new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 2147483647, 1, false);
                    $effect1 = new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 2147483647, 1, false);
                    $target->addEffect($effect);
                    $target->addEffect($effect1);
                    $this->arrested[$target->getName()] = true;
                    $target->sendMessage("§4U bent gearresteerd!");
                    $e->getDamager()->sendMessage("§aU hebt iemand succesvol gearresteerd.");
                }

            }
        }
    }
}
