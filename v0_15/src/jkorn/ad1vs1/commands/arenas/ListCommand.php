<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands\arenas;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\commands\Abstract1vs1Command;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ListCommand extends Abstract1vs1Command
{

    public function __construct()
    {
        parent::__construct("lsDArenas", "Lists the duel arenas.", "Usage: /lsDArenas", ["lsdarenas", "listDuelArenas"]);
        parent::setPermission("permission.ad1vs1.listarenas");
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
            $arenas = AD1vs1Main::getArenaManager()->listArenas();
            $sender->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::WHITE . "Arenas: " . implode(", ", $arenas)
            );
        }
        return true;
    }
}