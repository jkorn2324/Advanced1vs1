<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\player;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\duels\Abstract1vs1;
use jkorn\ad1vs1\player\data\DuelArenaData;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\level\Level;
use pocketmine\level\Location;
use pocketmine\level\Position;
use pocketmine\Player;

/**
 * Class AD1vs1Player
 * @package jkorn\ad1vs1\player
 *
 * Wrapper class used for a player object.
 */
class AD1vs1Player
{

    /** @var Player */
    private $player;
    /** @var string */
    private $uniqueID;

    /** @var bool */
    private $immobile;
    /** @var bool */
    private $flying;

    /** @var string */
    private $displayName;

    /** @var DuelArenaData|null */
    private $playerArenaData;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->immobile = false;
        $this->flying = true;
        $this->playerArenaData = null;
        $this->displayName = $player->getDisplayName();
        $this->uniqueID = $player->getUniqueId()->toString();
    }

    /**
     * @return Player|null
     *
     * Gets the player corresponding.
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * @return string
     *
     * Gets the unique id of the player.
     */
    public function getUniqueId() {
        return $this->uniqueID;
    }


    /**
     * Called when the player joins.
     */
    public function onJoin() {}


    /**
     * Called when the player is disconnected.
     */
    public function onDisconnect()
    {
        $duel = ($duelsManager = AD1vs1Main::get1vs1Manager())->getDuelFromPlayer($this);
        if($duel !== null) {
            $duel->removePlayerFromDuel($this, Abstract1vs1::REASON_LEFT_SERVER);
        }

        $duelsManager->getQueuesManager()->removeFromQueue($this);
        AD1vs1Main::getPlayerManager()->removePlayer($this);
    }

    /**
     * Called when the player moves or not.
     *
     * @param Location $from
     * @param Location $to
     *
     * @return bool - true if motion should be cancelled or not.
     */
    public function onMove(Location $from, Location $to)
    {
        if($this->immobile) {
            return true;
        }

        return false;
    }

    /**
     * Called when the player dies.
     *
     * @param &$message
     */
    public function onDeath(&$message)
    {
        if(!$this->isOnline())
        {
            return;
        }

        $duel = AD1vs1Main::get1vs1Manager()->getDuelFromPlayer($this);
        if($duel !== null)
        {
            $lastCause = $this->player->getLastDamageCause();
            $output = Abstract1vs1::REASON_UNFAIR_RESULT;
            if($lastCause !== null && $lastCause instanceof EntityDamageByEntityEvent)
            {
                $damager = $lastCause->getDamager();
                if($damager !== null
                    && $damager instanceof Player
                    && $duel->isPlayer($damager)
                    && $lastCause->getCause() !== EntityDamageEvent::CAUSE_SUICIDE)
                {
                    $output = Abstract1vs1::REASON_LOST;
                }
            }

            $message = "";
            $duel->removePlayerFromDuel($this, $output);

            $this->clearInventory();
            $this->player->removeAllEffects();
        }
    }

    /**
     * Called when the player respawns.
     *
     * @param Position &$position
     */
    public function onRespawn(Position &$position) {}


    /**
     * @return bool
     *
     * Determines if the player is in a queue or not.
     */
    public function isInQueue() {
        // TODO: Do this.
        return false;
    }

    /**
     * @return bool
     *
     * Determines if the player is in a duel or not.
     */
    public function isInDuel()
    {
        return ($duel = AD1vs1Main::get1vs1Manager()->getDuelFromPlayer($this)) !== null
            && $duel instanceof Abstract1vs1;
    }

    /**
     * @return bool
     *
     * Determines if the player is online or not.
     */
    public function isOnline()
    {
        return $this->player != null && $this->player->isOnline();
    }

    /**
     * @return bool
     *
     * Determines whether or not the player is immobile or not.
     */
    public function isImmobile()
    {
        return $this->immobile;
    }

    /**
     * @param bool $mobile
     *
     * Sets the player as immobile or not.
     */
    public function setImmobile(bool $mobile = true)
    {
        if(!$this->isOnline()) {
            return;
        }

        $this->immobile = $mobile;
    }

    /**
     * @param $player
     * @return bool
     *
     * Determines if the player is part of the 1vs1.
     */
    public function equals($player)
    {
        if($player instanceof Player)
        {
            $uuid = $player->getUniqueId();
            if($uuid !== null)
            {
                return $this->uniqueID === $uuid->toString();
            }
        }
        elseif ($player instanceof AD1vs1Player)
        {
            return $this->uniqueID === $player->getUniqueId();
        }

        return false;
    }

    /**
     * Determines whether or not the player can fly or not.
     */
    public function disableFlight()
    {
        if(!$this->isOnline()) {
            return;
        }

        $this->setFlying(false);
        $this->player->setAllowFlight(false);
    }

    /**
     * @param bool $flying
     *
     * Sets the player as flying or not.
     */
    public function setFlying(bool $flying)
    {
        if(!$this->isOnline())
        {
            return;
        }

        /*
         * Flags:
         * - 0x200 - Untested [ ]
         * - 0x400 - Untested [ ]
         */

        $this->flying = $flying;
        $this->player->sendSettings();
    }

    /**
     * @return bool
     *
     * Determines whether or not the player
     * is flying or not.
     */
    public function isFlying()
    {
        return $this->flying;
    }

    /**
     * @param int $gamemode
     *
     * Sets the gamemode of the player.
     */
    public function setGamemode(int $gamemode)
    {
        if(!$this->isOnline())
        {
            return;
        }

        $this->player->setGamemode($gamemode);
    }

    /**
     * @return int
     *
     * Gets the gamemode of the player.
     */
    public function getGamemode()
    {
        if(!$this->isOnline())
        {
            return 0;
        }
        return $this->player->getGamemode();
    }

    /**
     * Clears the inventory of the player.
     */
    public function clearInventory()
    {
        if(!$this->isOnline())
        {
            return;
        }

        $this->player->getInventory()->clearAll();
    }

    /**
     * @param $level
     * @return bool
     *
     * Determines if the player is in a given level.
     */
    public function isInLevel($level)
    {
        if(!$this->isOnline())
        {
            return false;
        }

        $pLevel = $this->player->getLevel();
        if($level instanceof Level)
        {
            return $pLevel->getName() === $level->getName();
        }
        elseif (is_string($level))
        {
            return $pLevel->getName() === $level;
        }

        return false;
    }

    /**
     * @return string
     *
     * Gets the displayname of the player.
     */
    public function getDisplayName()
    {
        if($this->isOnline()) {
            return $this->player->getDisplayName();
        }

        return $this->displayName;
    }

    /**
     * Sends the player to the lobby.
     *
     * @param bool $teleport
     */
    public function putInLobby(bool $teleport = true)
    {
        if(!$this->isOnline())
        {
            return;
        }

        $this->player->removeAllEffects();
        $this->clearInventory();

        if($teleport) {
            $this->player->teleport(AD1vs1Util::getSpawnPosition());
        }
    }

    /**
     * @return DuelArenaData
     *
     * Gets the player arena data.
     */
    public function getPlayerArenaData()
    {
        if($this->playerArenaData === null)
        {
            return $this->playerArenaData = new DuelArenaData();
        }

        return $this->playerArenaData;
    }

    /**
     * Resets the player arena data.
     */
    public function resetPlayerArenaData()
    {
        $this->playerArenaData = null;
    }
}