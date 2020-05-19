<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\commands\arenas;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\arenas\AD1vs1DuelArena;
use jkorn\ad1vs1\commands\Abstract1vs1Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class EditPosCommand extends Abstract1vs1Command
{

    /** @var string */
    private $varName, $varDescription;

    public function __construct($varName, string $varDescription, $description)
    {
        parent::__construct($varName, $description, "Usage: /{$varName} {name}");
        parent::setPermission("permission.ad1vs1.manage.arenas");

        $this->varName = $varName;
        $this->varDescription = $varDescription;

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
            assert($sender instanceof Player);

            $player = AD1vs1Main::getPlayerManager()->getPlayer($sender);

            if(isset($args[0]))
            {
                $arenaName = (string)$args[0];
                $arena = AD1vs1Main::getArenaManager()->getArena($arenaName);
                if($arena instanceof AD1vs1DuelArena)
                {
                    $arena->edit(
                        $this->varName,
                        new Vector3($sender->x, $sender->y, $sender->z)
                    );
                    $sender->sendMessage(
                        AD1vs1Util::getPrefix() . TextFormat::GREEN . " Successfully edited the existing arena '{$args[0]}!'"
                    );
                    return true;

                } else {
                    $sender->sendMessage(
                        AD1vs1Util::getPrefix() . TextFormat::RED . " The arena {$args[0]} doesn't exist!"
                    );
                    return true;
                }
            }

            $arenaData = $player->getPlayerArenaData();
            $arenaData->set(
                $this->varName,
                new Vector3($sender->x, $sender->y, $sender->z)
            );

            $sender->sendMessage(AD1vs1Util::getPrefix() . TextFormat::GREEN . " Successfully set the {$this->varDescription}.");
        }

        return true;
    }
}