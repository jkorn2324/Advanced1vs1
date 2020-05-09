<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\player;


use jkorn\ad1vs1\AD1vs1Main;
use pocketmine\Player;
use pocketmine\utils\UUID;

/**
 * Class AD1vs1Player
 * @package jkorn\ad1vs1\player
 *
 * Wrapper class used for a player object.
 */
class AD1vs1Player
{

    /** @var Player */
    private $player;
    /** @var string */
    private $uniqueID;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->uniqueID = $player->getUniqueId()->toString();
    }

    /**
     * @return Player|null
     *
     * Gets the player corresponding.
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * @return string
     *
     * Gets the unique id of the player.
     */
    public function getUniqueId() {
        return $this->uniqueID;
    }


    /**
     * Called when the player joins.
     */
    public function onJoin() {

    }


    /**
     * Called when the player is disconnected.
     */
    public function onDisconnect() {



        AD1vs1Main::getPlayerManager()->removePlayer($this);
    }

    /**
     * @return bool
     *
     * Determines if the player is in a queue or not.
     */
    public function isInQueue() {
        // TODO: Do this.
        return false;
    }

    /**
     * @return bool
     *
     * Determines if the player is in a duel or not.
     */
    public function isInDuel() {
        // TODO: Do this.
        return false;
    }
}