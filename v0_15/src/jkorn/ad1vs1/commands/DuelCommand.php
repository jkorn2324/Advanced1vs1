<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class DuelCommand extends Command
{

    public function __construct()
    {
        parent::__construct("duel", "Queues for a duel based on the kit.", "Usage: /duel {kit}", ["1vs1"]);
        $this->setPermission("permission.ad1vs1.duel");
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
        if($sender instanceof Player
            && ($player = AD1vs1Main::getPlayerManager()->getPlayer($sender)) !== null
            && $player instanceof AD1vs1Player)
        {
            if($this->testPermission($sender)) {

                if($player->isInDuel()) {
                    $sender->sendMessage(AD1vs1Util::getPrefix() . " " . TextFormat::RED . "Can't run this command while playing a duel.");
                } else {
                    $kitName = "";
                    if(isset($args[0])) {
                        $kitName = (string)$args[0];
                    }
                    AD1vs1Main::get1vs1Manager()->getQueuesManager()->putInQueue($player, $kitName);
                }
            }

        } else {

            $sender->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::RED . "Only players can use this command."
            );
        }
    }
}