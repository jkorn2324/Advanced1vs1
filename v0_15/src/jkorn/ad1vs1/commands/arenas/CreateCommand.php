<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands\arenas;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\commands\Abstract1vs1Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CreateCommand extends Abstract1vs1Command
{

    public function __construct()
    {
        parent::__construct("duel-create", "Creates a new pre generated duel arena.", "Usage: /duel-create <name>", ["duelCreate"]);
        parent::setPermission("permission.ad1vs1.manage.arenas");

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
        if($this->testPermission($sender)) {

            assert($sender instanceof Player);
            $player = AD1vs1Main::getPlayerManager()->getPlayer($sender);
            if(!isset($args[0])) {
                $sender->sendMessage(AD1vs1Util::getPrefix() . " " . TextFormat::RED . $this->getUsage());
                return true;
            }

            $arenaData = $player->getPlayerArenaData();
            $remainingVariables = $arenaData->getVariablesLeft();
            if(count($remainingVariables) > 0)
            {
                $sender->sendMessage(
                    AD1vs1Util::getPrefix() . TextFormat::RED . " These remaining variables need to be set: "
                    . "\n" . implode("\n", $remainingVariables)
                );
                return true;
            }

            $arena = $arenaData->toDuelArena($args[0], $sender->getLevel());
            $arenaManager = AD1vs1Main::getArenaManager();

            if($arena === null || $arenaManager->isArena($args[0]))
            {
                $sender->sendMessage(
                    AD1vs1Util::getPrefix() . TextFormat::RED . " The arena '{$args[0]}' already exists."
                );
                return true;
            }

            $arenaManager->addArena($arena);
            $sender->sendMessage(AD1vs1Util::getPrefix() . TextFormat::GREEN . " Successfully created a new duel arena.");
            $player->resetPlayerArenaData();
        }

        return true;
    }
}