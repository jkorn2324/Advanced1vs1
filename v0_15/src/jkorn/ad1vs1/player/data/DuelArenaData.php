<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\player\data;


use jkorn\ad1vs1\arenas\AD1vs1DuelArena;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

class DuelArenaData
{

    /** @var Vector3|null */
    private $edge1, $edge2;
    /** @var Vector3|null */
    private $pos1, $pos2;

    /**
     * DuelArenaData constructor.
     */
    public function __construct()
    {
        $this->edge1 = null;
        $this->edge2 = null;
        $this->pos1 = null;
        $this->pos2 = null;
    }

    /**
     * @return bool
     *
     * Determines if the arena data is valid or not.
     */
    private function isValid()
    {
        if($this->edge1 === null
        || $this->edge2 === null
        || $this->pos1 === null
        || $this->pos2 === null)
        {
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @param $value
     *
     * Sets the value.
     */
    public function set(string $name, $value)
    {
        switch($name)
        {
            case "pos1":
            case "pos1Edge":
            case "edge1":
                if($value instanceof Vector3) {
                    $this->edge1 = $value;
                }
                break;

            case "pos2":
            case "pos2Edge":
            case "edge2":
                if($value instanceof Vector3) {
                    $this->edge2 = $value;
                }
                break;

            case "p1":
            case "p1Spawn":
                if($value instanceof Vector3) {
                    $this->pos1 = $value;
                }
                break;

            case "p2":
            case "p2Spawn":
                if($value instanceof Vector3) {
                    $this->pos2 = $value;
                }
                break;
        }
    }

    /**
     * @param string $name
     * @param Level $level
     * @return AD1vs1DuelArena|null
     *
     * Converts the arena data to a duel arena.
     */
    public function toDuelArena(string $name, Level $level)
    {
        if(!$this->isValid())
        {
            return null;
        }

        return new AD1vs1DuelArena($name, $level, $this->pos1, $this->pos2, $this->edge1, $this->edge2);
    }

    /**
     * @return array
     *
     * Gets the variables left, used to notify the user.
     */
    public function getVariablesLeft()
    {
        $output = [];

        if($this->pos1 === null)
        {
            $output[] = "Player1 spawn position. [Vector3] - Command: /p1Spawn";
        }

        if($this->pos2 === null)
        {
            $output[] = "Player2 spawn position. [Vector3] - Command: /p2Spawn";
        }

        if($this->edge1 === null)
        {
            $output[] = "First arena outside edge. [Vector3] - Command: /edge1";
        }

        if($this->edge2 === null)
        {
            $output[] = "Second arena outside edge. [Vector3] - Command: /edge2";
        }

        return $output;
    }
}