<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\types;

use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\duels\Abstract1vs1;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\level\AD1vs1GeneratorInfo;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerBucketEmptyEvent;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Bucket;
use pocketmine\item\ItemBlock;
use pocketmine\level\Level;
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

    /** @var array|Vector3[] */
    private $blocks = [];

    /** @var AD1vs1GeneratorInfo */
    private $generatorInfo;

    public function __construct(int $id, AD1vs1Player $p1, AD1vs1Player $p2, IDuelKit $kit, AD1vs1GeneratorInfo $info)
    {
        parent::__construct($p1, $p2, $kit, "1vs1.{$id}");
        $this->id = $id;
        $this->generatorInfo = $info;
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
        $center = new Vector3(0, 100, 0);
        $center->x = ($this->generatorInfo->getWidth() * 16) - 6;
        $center->z = ($this->generatorInfo->getLength() * 16) / 2;

        return $center;
    }

    /**
     * @return Vector3
     *
     * Gets the player2 start position.
     */
    protected function getPlayer2Start()
    {
        $center = new Vector3(0, 100, 0);
        $center->x = 6;
        $center->z = ($this->generatorInfo->getLength() * 16) / 2;

        return $center;
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
     * @param Event $event - The event being called.
     * The list of events that this event adheres to are:
     * - PlayerBucketFillEvent
     * - PlayerBucketEmptyEvent
     * - BlockPlaceEvent
     * - BlockBreakEvent
     * - BlockUpdateEvent // TODO: Need to add a level checker & a position checker.
     *
     * Determines whether or not the player can edit the arena.
     *
     */
    public function canEditArena(Event &$event)
    {
        if($this->status !== self::STATUS_IN_PROGRESS || !AD1vs1Util::isBuildingKit($this->kit->getLocalizedName()))
        {
            $event->setCancelled();
            return;
        }

        if($event instanceof BlockPlaceEvent)
        {
            if(!isset($this->blocks[$localized = AD1vs1Util::localizePosition($event->getBlockReplaced())]))
            {
                $this->blocks[$localized] = true;
                return;
            }
        }
        elseif ($event instanceof BlockBreakEvent)
        {
            $block = $event->getBlock();
            if(isset($this->blocks[$localized = AD1vs1Util::localizePosition($block)]))
            {
                unset($this->blocks[$localized]);
                return;
            }
        }
        elseif ($event instanceof PlayerBucketFillEvent)
        {
            // No need to do anything here.
            return;
        }
        elseif ($event instanceof PlayerBucketEmptyEvent)
        {
            // No need to do anything here.
            return;
        }

        $event->setCancelled();
    }
}