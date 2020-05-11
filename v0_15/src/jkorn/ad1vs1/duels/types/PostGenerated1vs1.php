<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\types;

use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\duels\Abstract1vs1;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\event\Event;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

/**
 * Class PostGenerated1vs1
 * @package jkorn\ad1vs1\duels\types
 *
 * Duel that is for when arenas don't exist & an arena needs to be generated.
 */
class PostGenerated1vs1 extends Abstract1vs1
{

    /** @var int */
    private $id;

    public function __construct(int $id, AD1vs1Player $p1, AD1vs1Player $p2, IDuelKit $kit)
    {
        parent::__construct($p1, $p2, $kit, "1vs1.{$id}");
        $this->id = $id;
    }

    /**
     * Kills the duel.
     */
    protected function kill()
    {
        AD1vs1Util::deleteLevel($this->level);
        AD1vs1Main::get1vs1Manager()->removeDuel($this);
    }

    /**
     * @return int
     *
     * Gets the localized name of the 1vs1.
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * @return Vector3
     *
     * Gets the player1 start position.
     */
    protected function getPlayer1Start()
    {
        $x = 24; $z = 40;
        if(AD1vs1Util::isSumoKit($this->kit->getLocalizedName()))
        {
            $x = 9; $z = 5;
        }
        return new Vector3($x, 100, $z);
    }

    /**
     * @return Vector3
     *
     * Gets the player2 start position.
     */
    protected function getPlayer2Start()
    {
        $p1Position = clone $this->getPlayer1Start();
        if(AD1vs1Util::isSumoKit($this->kit->getLocalizedName()))
        {
            $p1Position->z = 10;
        } else {
            $p1Position->x = 1;
        }
        return $p1Position;
    }

    /**
     * @return Position
     *
     * Gets the center position of the duel.
     */
    protected function getCenterPosition()
    {
        $pos1 = $this->getPlayer1Start();
        $pos2 = $this->getPlayer2Start();

        $averageX = ($pos1->x + $pos2->x) / 2;
        $averageY = ($pos1->y + $pos2->y) / 2;
        $averageZ = ($pos1->z + $pos2->z) / 2;

        return new Position($averageX, $averageY, $averageZ, $this->level);
    }

    /**
     * @param Event $event
     *
     * Determines whether or not the player can edit the arena.
     *
     */
    public function canEditArena(Event &$event)
    {
        // TODO
    }
}