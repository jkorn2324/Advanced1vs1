<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\queues;


use jkorn\ad1vs1\player\AD1vs1Player;

class AD1vs1Queue
{

    /** @var AD1vs1Player */
    private $player;

    /** @var string */
    private $kit;
    /** @var string */
    private $uuid;

    public function __construct(AD1vs1Player $player, string $kit)
    {
        $this->kit = $kit;
        $this->player = $player;
        $this->uuid = $player->getUniqueId();
    }

    /**
     * @return AD1vs1Player
     *
     * Gets the corresponding player.
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @return string
     *
     * Gets the kit.
     */
    public function getKit()
    {
        return $this->kit;
    }

    /**
     * @return string
     *
     * Gets the unique id of the queue.
     */
    public function getUniqueID()
    {
        return $this->uuid;
    }
}