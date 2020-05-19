<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\arenas;


use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AD1vs1DuelArena
{

    /** @var Level */
    private $level;

    /** @var string */
    private $name, $localizedName;
    /** @var Vector3 */
    private $pos1Edge, $pos2Edge;
    /** @var Vector3 */
    private $player1Spawn, $player2Spawn;

    /** @var bool */
    private $open;

    public function __construct(string $name, Level $level, Vector3 $p1, Vector3 $p2, Vector3 $pos1, Vector3 $pos2, string $localizedName = "")
    {
        $this->name = $name;
        $this->level = $level;
        $this->localizedName = $localizedName === "" ? strtolower($name) : $localizedName;
        $this->player1Spawn = $p1;
        $this->player2Spawn = $p2;
        $this->open = true;
        $this->pos1Edge = $pos1;
        $this->pos2Edge = $pos2;
    }

    /**
     * @param Vector3 $position
     * @return bool
     *
     * Determines whether or not the position is
     * within the arena.
     */
    public function isWithinArena(Vector3 $position)
    {
        if($position instanceof Position)
        {
            $level = $position->getLevel();
            if($level === null || $level->getName() !== $this->level->getName())
            {
                return false;
            }
        }

        $minVec = AD1vs1Util::getMinimumVector($this->pos1Edge, $this->pos2Edge);
        $maxVec = AD1vs1Util::getMaximumVector($this->pos1Edge, $this->pos2Edge);

        return $position->x >= $minVec->x && $position->x <= $maxVec->x
            && $position->z >= $minVec->z && $position->z <= $maxVec->z;
    }

    /**
     * @return string
     *
     * Gets the localized name of the duel arena.
     */
    public function getLocalizedName()
    {
        return $this->localizedName;
    }

    /**
     * @return string
     *
     * Gets the name of the arena.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Decodes the duel arena to an array.
     *
     * @return array
     */
    public function encode()
    {
        return [
            "name" => $this->name,
            "level" => $this->level->getName(),
            "pos1Edge" => AD1vs1Util::vec3ToArr($this->pos1Edge),
            "pos2Edge" => AD1vs1Util::vec3ToArr($this->pos2Edge),
            "player1Spawn" => AD1vs1Util::vec3ToArr($this->player1Spawn),
            "player2Spawn" => AD1vs1Util::vec3ToArr($this->player2Spawn)
        ];
    }

    /**
     * @param string $localizedName
     * @param array $data
     *
     * @return AD1vs1DuelArena|null
     *
     * Decodes the duel arena from the json.
     */
    public static function decode(string $localizedName, array $data)
    {
        if(isset($data["name"], $data["level"], $data["pos1Edge"], $data["pos2Edge"], $data["player1Spawn"], $data["player2Spawn"]))
        {
            $server = Server::getInstance();
            $levelName = $data["level"];

            if(!($loaded = $server->isLevelLoaded($levelName))) {
                $loaded = $server->loadLevel($levelName);
            }

            if($loaded)
            {
                $edge1 = AD1vs1Util::arrToVec3($data["pos1Edge"]);
                $edge2 = AD1vs1Util::arrToVec3($data["pos2Edge"]);
                $p1 = AD1vs1Util::arrToVec3($data["player1Spawn"]);
                $p2 = AD1vs1Util::arrToVec3($data["player2Spawn"]);

                if($edge1 !== null && $edge2 !== null && $p1 !== null && $p2 !== null)
                {
                    return new AD1vs1DuelArena(
                        $data["name"],
                        $server->getLevelByName($levelName),
                        $p1,
                        $p2,
                        $edge1,
                        $edge2,
                        $localizedName
                    );
                }
            }
        }
        return null;
    }

    /**
     * @param AD1vs1Player $player
     * @return bool
     *
     * Determines whether the player can edit the positions.
     */
    public function canEditPositions(AD1vs1Player $player)
    {
        if(!$player->isOnline())
        {
            return false;
        }

        $normalPlayer = $player->getPlayer();
        if(!AD1vs1Util::areLevelsEqual($player->getPlayer()->getLevel(), $this->level))
        {
            $normalPlayer->sendMessage(AD1vs1Util::getPrefix() . " " . TextFormat::RED . " You need to be in the same level to edit the positions of the arena. (Level: {$this->level->getName()})");
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
    public function edit(string $name, $value)
    {
        switch($name)
        {
            case "pos1":
            case "pos1Edge":
            case "edge1":
                if($value instanceof Vector3) {
                    $this->pos1Edge = $value;
                }
                break;

            case "pos2":
            case "pos2Edge":
            case "edge2":
                if($value instanceof Vector3) {
                    $this->pos2Edge = $value;
                }
                break;

            case "p1":
            case "p1Spawn":
                if($value instanceof Vector3) {
                    $this->player1Spawn = $value;
                }
                break;

            case "p2":
            case "p2Spawn":
                if($value instanceof Vector3) {
                    $this->player2Spawn = $value;
                }
                break;
        }
    }
}