<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class DuelCommand extends Abstract1vs1Command
{

    public function __construct()
    {
        parent::__construct("duel", "Queues for a duel based on the kit.", "Usage: /duel {kit}", ["1vs1"]);
        $this->setPermission("permission.ad1vs1.duel");
        $this->canUseInDuel = false;
        $this->consoleUseCommand = false;
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
            $kitName = "";
            if(isset($args[0])) {
                $kitName = (string)$args[0];
            }
            AD1vs1Main::get1vs1Manager()->getQueuesManager()->putInQueue(AD1vs1Main::getPlayerManager()->getPlayer($sender), $kitName);
        }

        return true;
    }
}