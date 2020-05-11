<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\Server;

class AD1vs1Manager
{


    /** @var AD1vs1Main */
    private $main;
    /** @var Server */
    private $server;

    /** @var Abstract1vs1[] */
    private $duels;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();

        $this->duels = [];
    }

    /**
     * Updates the duels.
     */
    public function update()
    {
        foreach($this->duels as $duel)
        {
            $duel->update();
        }
    }

    /**
     * @param Abstract1vs1 $duel
     *
     * Removes the duel from the list.
     */
    public function removeDuel(Abstract1vs1 $duel)
    {
        if(isset($this->duels[$duel->getID()])) {
            unset($this->duels[$duel->getID()]);
            unset($duel);
        }
    }

    /**
     * @param AD1vs1Player $player
     * @return Abstract1vs1|null
     *
     * Gets the duel from the player.
     */
    public function getDuelFromPlayer(AD1vs1Player $player)
    {
        foreach($this->duels as $duel)
        {
            if($duel->isPlayer($player)) {
                return $duel;
            }
        }
        return null;
    }
}