<?php


namespace jkorn\ad1vs1\commands;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

abstract class Abstract1vs1Command extends Command
{

    /** @var bool */
    protected $consoleUseCommand = true;
    /** @var bool */
    protected $canUseInDuel = true;

    /**
     * @param CommandSender $target
     * @return bool
     *
     * Overriden to add support for duels & console checking.
     */
    public function testPermission(CommandSender $target)
    {
        if(!parent::testPermission($target))
        {
            return false;
        }

        if($target instanceof Player
            && ($player = AD1vs1Main::getPlayerManager()->getPlayer($target)) !== null
            && $player instanceof AD1vs1Player) {

            if(!$this->canUseInDuel && $player->isInDuel()) {
                $target->sendMessage(AD1vs1Util::getPrefix() . " " . TextFormat::RED . "Can't run this command while playing a duel.");
                return false;
            }

            return true;

        } else {

            if($this->consoleUseCommand) return true;

            $target->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::RED . "Only players can use this command."
            );
        }

        return false;
    }
}