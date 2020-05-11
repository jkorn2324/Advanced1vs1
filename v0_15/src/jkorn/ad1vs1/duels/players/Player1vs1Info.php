<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\players;


use jkorn\ad1vs1\player\AD1vs1Player;

class Player1vs1Info
{

    /** @var AD1vs1Player */
    private $player;

    /** @var bool */
    private $online;
    /** @var bool */
    private $teleportedToSpawn;
    /** @var string */
    private $displayName;

    public function __construct(AD1vs1Player $player)
    {
        $this->player = $player;
        $this->online = true;
        $this->teleportedToSpawn = false;
        $this->displayName = $player->getDisplayName();
    }

    /**
     * @return bool
     *
     * Determines if the player has already teleported to spawn.
     */
    public function didTeleportToSpawn()
    {
        return $this->teleportedToSpawn;
    }

    /**
     * @return bool
     *
     * Determines if the player is online or not.
     */
    public function isOnline()
    {
        if($this->player === null)
        {
            return false;
        }

        return $this->online && $this->player->isOnline();
    }

    /**
     * Sets the player as offline.
     */
    public function setOffline()
    {
        $this->online = false;
    }

    /**
     * Sets the player as teleported to spawn.
     */
    public function setTeleportedToSpawn()
    {
        $this->teleportedToSpawn = true;
    }

    /**
     * @return string
     *
     * Gets the display name of the player.
     */
    public function getDisplayName()
    {
        if($this->player !== null)
        {
            return $this->player->getDisplayName();
        }

        return $this->displayName;
    }

    /**
     * @return AD1vs1Player|null
     *
     * Gets the corresponding player.
     */
    public function getAD1vs1Player()
    {
        return $this->player;
    }

    /**
     * @param $player
     * @return bool
     *
     * Determines if the player equals another player.
     */
    public function equals($player)
    {
        return $this->player->equals($player);
    }
}