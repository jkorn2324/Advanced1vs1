<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands\arenas;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\commands\Abstract1vs1Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DeleteCommand extends Abstract1vs1Command
{

    public function __construct()
    {
        parent::__construct("arenadelete", "Deletes a duel arena.", "Usage: /arenadelete <name>", ["arena-delete", "deletearena"]);
        parent::setPermission("permission.ad1vs1.manage.arenas");
        $this->canUseInDuel = false;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param string[] $args
     *
     * @return mixed
     */
    public function execute(CommandSender $sender, $commandLabel, array $args)
    {
        if($this->testPermission($sender))
        {
            if(!isset($args[0])) {
                $sender->sendMessage(
                    AD1vs1Util::getPrefix() . TextFormat::RED . " " . $this->getUsage()
                );
                return true;
            }

            if(!AD1vs1Main::getArenaManager()->isArena($args[0])) {
                $sender->sendMessage(
                    AD1vs1Util::getPrefix() . TextFormat::RED . " This arena doesn't exist."
                );
                return true;
            }

            AD1vs1Main::getArenaManager()->removeArena($args[0]);
        }

        return true;
    }
}