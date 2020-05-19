<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\player;


use jkorn\ad1vs1\AD1vs1Main;
use pocketmine\Player;
use pocketmine\Server;

class AD1vs1PlayerManager
{

    /** @var AD1vs1Player[]|array */
    private $players;

    /** @var Server */
    private $server;
    /** @var AD1vs1Main */
    private $main;

    public function __construct(AD1vs1Main $main)
    {
        $this->players = [];

        $this->main = $main;
        $this->server = $main->getServer();
    }

    /**
     * @param Player $player
     *
     * Stores the player in the player list.
     */
    public function putPlayer(Player $player)
    {
        if(!isset($this->players[$uuid = $player->getUniqueId()->toString()])) {
            $this->players[$uuid] = new AD1vs1Player($player);
        }
    }

    /**
     * @param $player
     * @return AD1vs1Player|null
     *
     * Gets the player from the list, if the player doesn't exist, the list adds it.
     */
    public function getPlayer($player) {

        if(!$player instanceof Player || !$player->isOnline()) {
            return null;
        }

        if(!isset($this->players[$uuid = $player->getUniqueId()->toString()])) {
            return $this->players[$uuid] = new AD1vs1Player($player);
        }

        return $this->players[$uuid];
    }

    /**
     * @param AD1vs1Player $player
     *
     * Removes the player from the list.
     */
    public function removePlayer(AD1vs1Player $player) {

        if(isset($this->players[$uuid = $player->getUniqueId()])) {
            unset($this->players[$uuid]);
        }
    }
}