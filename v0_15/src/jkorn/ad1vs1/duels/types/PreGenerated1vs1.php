<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\types;

use jkorn\ad1vs1\duels\Abstract1vs1;
use pocketmine\block\Block;
use pocketmine\event\Event;
use pocketmine\level\Position;
use pocketmine\math\Vector3;

/**
 * Class PreGenerated1vs1
 * @package jkorn\ad1vs1\duels\types
 *
 * Duel that is for when arenas exist, etc...
 */
class PreGenerated1vs1 extends Abstract1vs1
{


    /**
     * Kills the duel.
     */
    protected function kill()
    {
        // TODO: Implement kill() method.
    }

    /**
     * @return int
     *
     * Gets the localized name of the 1vs1.
     */
    public function getID()
    {
        // TODO: Implement getID() method.
    }

    /**
     * @return Vector3
     *
     * Gets the player1 start position.
     */
    protected function getPlayer1Start()
    {
        // TODO: Implement getPlayer1Start() method.
    }

    /**
     * @return Vector3
     *
     * Gets the player2 start position.
     */
    protected function getPlayer2Start()
    {
        // TODO: Implement getPlayer2Start() method.
    }

    /**
     * @return Position
     *
     * Gets the center position of the duel.
     */
    protected function getCenterPosition()
    {
        // TODO: Implement getCenterPosition() method.
    }

    /**
     * @param Event $event
     *
     * Determines whether or not the arena can be edited.
     */
    public function canEditArena(Event &$event)
    {
        // TODO: Implement canEditArena() method.
    }
}