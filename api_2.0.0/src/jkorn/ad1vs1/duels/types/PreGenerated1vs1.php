<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\types;

use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\arenas\AD1vs1DuelArena;
use jkorn\ad1vs1\duels\Abstract1vs1;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\event\block\BlockUpdateEvent;
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

    /** @var AD1vs1DuelArena */
    private $arena;
    /** @var int */
    private $id;

    public function __construct(int $id, AD1vs1Player $p1, AD1vs1Player $p2, IDuelKit $kit, AD1vs1DuelArena $arena)
    {
        parent::__construct($p1, $p2, $kit, ($level = $arena->getLevel())->getName());
        $this->level = $level;
        $this->arena = $arena;
        $this->id = $id;

        AD1vs1Main::getArenaManager()->setActive($arena->getLocalizedName());
    }

    /**
     * Kills the duel.
     */
    protected function kill()
    {
        AD1vs1Main::getArenaManager()->setInActive($this->arena->getLocalizedName());
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
        return $this->arena->getP1Spawn();
    }

    /**
     * @return Vector3
     *
     * Gets the player2 start position.
     */
    protected function getPlayer2Start()
    {
        return $this->arena->getP2Spawn();
    }

    /**
     * @return Position
     *
     * Gets the center position of the duel, unused here.
     */
    protected function getCenterPosition()
    {
        $pos1 = $this->arena->getEdge1();
        $pos2 = $this->arena->getEdge2();

        $averageX = ($pos1->x + $pos2->x) / 2;
        $averageY = ($pos1->y + $pos2->y) / 2;
        $averageZ = ($pos1->z + $pos2->z) / 2;

        return new Position($averageX, $averageY, $averageZ, $this->level);
    }

    /**
     * @param Event $event
     *
     * Called when the arena is edited.
     */
    public function onEditArena(Event &$event)
    {
        if(!$event instanceof BlockUpdateEvent) {
            $event->setCancelled();
        }
    }

    /**
     * @param Position $position
     * @return bool
     *
     * Determines if the duel contains the position.
     */
    public function containsPosition(Position $position)
    {
        return $this->arena->isWithinArena($position);
    }
}