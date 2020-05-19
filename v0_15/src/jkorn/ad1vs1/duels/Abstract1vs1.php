<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels;


use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\duels\players\Player1vs1Info;
use jkorn\ad1vs1\kits\IDuelKit;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\FlintSteel;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

abstract class Abstract1vs1
{

    const STATUS_STARTING = 0;
    const STATUS_IN_PROGRESS = 1;
    const STATUS_ENDING = 2;
    const STATUS_ENDED = 3;

    const REASON_LOST = 0;
    const REASON_UNFAIR_RESULT = 1;
    const REASON_LEFT_SERVER = 2;

    // 30 Minutes
    const MAX_DURATION_SECONDS = 60 * 30;

    /** @var Player1vs1Info */
    protected $player1, $player2;
    /** @var IDuelKit */
    protected $kit;

    /** @var int */
    protected $currentTick;
    /** @var int */
    protected $countdownSeconds, $durationSeconds, $endingSeconds;

    /** @var int */
    protected $status;
    /** @var Level */
    protected $level;
    /** @var Server */
    protected $server;

    /** @var array */
    protected $results;

    public function __construct(AD1vs1Player $p1, AD1vs1Player $p2, IDuelKit $kit, string $level)
    {
        $this->server = Server::getInstance();
        $this->currentTick = 0;
        $this->countdownSeconds = 5;
        $this->durationSeconds = 0;
        $this->endingSeconds = 2;
        $this->player1 = new Player1vs1Info($p1);
        $this->player2 = new Player1vs1Info($p2);
        $this->kit = $kit;
        $this->status = self::STATUS_STARTING;
        $this->level = $this->server->getLevelByName($level);

        $this->results = [
            "winner" => NULL,
            "loser" => NULL
        ];
    }

    /**
     * Puts the players in the duel.
     */
    protected function setPlayersInMatch()
    {
        $ad1vs1P1 = $this->player1->getAD1vs1Player();
        $ad1vs1P2 = $this->player2->getAD1vs1Player();

        $ad1vs1P1->setGamemode(0);
        $ad1vs1P2->setGamemode(0);

        $ad1vs1P1->disableFlight();
        $ad1vs1P2->disableFlight();

        $ad1vs1P1->setImmobile();
        $ad1vs1P2->setImmobile();

        $ad1vs1P1->clearInventory();
        $ad1vs1P2->clearInventory();

        $player1 = $ad1vs1P1->getPlayer();
        $player2 = $ad1vs1P2->getPlayer();

        $p1Pos = $this->getPlayer1Start();
        $position = new Position($p1Pos->x, $p1Pos->y, $p1Pos->z, $this->level);
        AD1vs1Util::onChunkGenerated($this->level, $position->x >> 4, $position->z >> 4, function() use ($player1, $position)
        {
            $player1->teleport($position);
        });

        if(!AD1vs1Util::areLevelsEqual($player1->getLevel(), $this->level))
        {
            $player1->teleport($position);
        }

        $p2Pos = $this->getPlayer2Start();
        $position = new Position($p2Pos->x, $p2Pos->y, $p2Pos->z, $this->level);
        AD1vs1Util::onChunkGenerated($this->level, $position->x >> 4, $position->z >> 4, function() use ($player2, $position)
        {
            $player2->teleport($position);
        });

        if(!AD1vs1Util::areLevelsEqual($player2->getLevel(), $this->level))
        {
            $player2->teleport($position);
        }

        $this->kit->sendTo($ad1vs1P1);
        $this->kit->sendTo($ad1vs1P2);
    }

    /**
     * Updates the duel.
     * @return bool
     */
    public function update()
    {
        $checkSeconds = ($this->currentTick % 20 === 0) && $this->currentTick !== 0;
        if(!$this->player1->isOnline() || !$this->player2->isOnline())
        {
            return true;
        }

        $ad1vs1Player1 = $this->player1->getAD1vs1Player();
        $ad1vs1Player2 = $this->player2->getAD1vs1Player();

        $player1 = $ad1vs1Player1->getPlayer();
        $player2 = $ad1vs1Player2->getPlayer();

        if($this->status === self::STATUS_STARTING)
        {
            if($this->currentTick === 5)
            {
                $this->setPlayersInMatch();
            }

            if($checkSeconds)
            {
                switch($this->countdownSeconds) {
                    case 5:
                        $player1->sendPopup($message = (AD1vs1Util::getPrefix() . TextFormat::WHITE . " Duel starting in 5..."));
                        $player2->sendPopup($message);
                        break;
                    case 0:
                        $player1->sendPopup($message = (AD1vs1Util::getPrefix() . TextFormat::WHITE . " Duel is starting NOW!"));
                        $player2->sendPopup($message);

                        $this->status = self::STATUS_IN_PROGRESS;

                        $ad1vs1Player1->setImmobile(false);
                        $ad1vs1Player2->setImmobile(false);

                        $this->currentTick++;
                        return true;
                    default:
                        $player1->sendPopup($message = (AD1vs1Util::getPrefix() . TextFormat::WHITE . " {$this->countdownSeconds}..."));
                        $player2->sendPopup($message);
                }

                $this->countdownSeconds--;
            }
        }
        elseif ($this->status === self::STATUS_IN_PROGRESS)
        {
            if(AD1vs1Util::isSumoKit($this->kit->getLocalizedName()))
            {
                $minimumPos = $this->getCenterPosition()->y - 5;
                if($player1->y < $minimumPos)
                {
                    $this->setEnded($this->player2, self::STATUS_ENDED);
                    return false;
                }

                if($player2->y < $minimumPos)
                {
                    $this->setEnded($this->player1, self::STATUS_ENDED);
                    return false;
                }
            }

            if($checkSeconds) {

                if($this->durationSeconds >= self::MAX_DURATION_SECONDS) {
                    $this->setEnded(null, self::STATUS_ENDED);
                    return false;
                }

                $this->durationSeconds++;
            }
        }
        elseif ($this->status === self::STATUS_ENDING)
        {
            if($checkSeconds && $this->endingSeconds > 0)
            {
                $this->endingSeconds--;
                if($this->endingSeconds === 0) {
                    $this->status = self::STATUS_ENDED;
                    $this->currentTick++;
                    return true;
                }
            }
        }
        elseif ($this->status === self::STATUS_ENDED)
        {
            $this->onEndDuel();
            $this->status = self::STATUS_ENDED;
            $this->kill();
            return false;
        }

        $this->currentTick++;
        return true;
    }

    /**
     * @param null $winner
     * @param mixed ...$extraData
     *
     * Sets the duel as ended or not.
     */
    public function setEnded($winner = null, ...$extraData)
    {
        $status = self::STATUS_ENDING;
        if(count($extraData) > 0) {
            if(is_int($extraData[0])) {
                $status = (int)$extraData[0];
            }
        }

        if($winner !== null && $this->isPlayer($winner))
        {
            if($this->player1->equals($winner))
            {
                $this->setResults($this->player1, $this->player2);
            }
            elseif ($this->player2->equals($winner))
            {
                $this->setResults($this->player2, $this->player1);
            }
        }

        $this->status = $status;
    }

    /**
     * @param AD1vs1Player $player
     * @param int $reason
     *
     * Removes the player from the duel.
     */
    public function removePlayerFromDuel(AD1vs1Player $player, int $reason)
    {
        if(!$this->isPlayer($player))
        {
            return;
        }

        $status = self::STATUS_ENDING;
        if($reason !== self::REASON_UNFAIR_RESULT)
        {
            if($this->player1->equals($player)) {
                $winner = $this->player2;
                $loser = $this->player1;
            } elseif ($this->player2->equals($player)) {
                $winner = $this->player1;
                $loser = $this->player2;
            }
            $status = $reason === self::REASON_LOST ? self::STATUS_ENDING : self::STATUS_ENDED;
        }

        if(isset($winner) && $status >= self::STATUS_IN_PROGRESS) {
            $this->setEnded($winner, $status);
        } else {
            $this->setEnded(null, $status);
        }

        if($reason === self::REASON_LEFT_SERVER) {

            if(isset($loser)) {
                $loser->setOffline();
            }

            $this->onEndDuel();
            $this->kill();
            return;
        }

        if(isset($loser)) {
            $loser->setTeleportedToSpawn();
        }
    }

    /**
     * @param Player1vs1Info $winner
     * @param Player1vs1Info $loser
     *
     * Sets the results of the duel.
     */
    protected function setResults(Player1vs1Info $winner, Player1vs1Info $loser)
    {
        $this->results["winner"] = $winner;
        $this->results["loser"] = $loser;
    }

    /**
     * Called when the duel is ended.
     */
    public function onEndDuel()
    {
        if($this->player1->isOnline() && ($player1 = $this->player1->getAD1vs1Player()) !== null)
        {
            if(!$this->player1->didTeleportToSpawn())
            {
                $player1->putInLobby();
            }
        }

        if($this->player2->isOnline() && ($player2 = $this->player2->getAD1vs1Player()) !== null)
        {
            if(!$this->player2->didTeleportToSpawn())
            {
                $player2->putInLobby();
            }
        }

        $winner = "None"; $loser = "None";

        $winnerData = $this->results["winner"];
        if($winnerData instanceof Player1vs1Info)
        {
            $winner = $winnerData->getDisplayName();
        }

        $loserData = $this->results["loser"];
        if($loserData instanceof Player1vs1Info)
        {
            $loser = $loserData->getDisplayName();
        }

        $this->broadcastMessage(TextFormat::GREEN . "Winner" . TextFormat::GRAY . ": " . TextFormat::YELLOW . $winner . TextFormat::DARK_GRAY . ' - ' . TextFormat::RED . "Loser" . TextFormat::GRAY . ": " . TextFormat::YELLOW . $loser);
    }


    /**
     * Kills the duel.
     */
    abstract protected function kill();

    /**
     * @return int
     *
     * Gets the localized name of the 1vs1.
     */
    abstract public function getID();

    /**
     * @param Player1vs1Info|AD1vs1Player|Player $player
     * @return bool
     *
     * Determines if the player is part of the duel.
     */
    public function isPlayer($player)
    {
        return $this->player1->equals($player) || $this->player2->equals($player);
    }

    /**
     * @return Vector3
     *
     * Gets the player1 start position.
     */
    abstract protected function getPlayer1Start();

    /**
     * @return Vector3
     *
     * Gets the player2 start position.
     */
    abstract protected function getPlayer2Start();

    /**
     * @return Position
     *
     * Gets the center position of the duel.
     */
    abstract protected function getCenterPosition();

    /**
     * @param string $message
     *
     * Broadcasts the message to everyone in the duel.
     */
    protected function broadcastMessage(string $message)
    {
        if($this->player1->isOnline() && ($player1 = $this->player1->getAD1vs1Player()) !== null)
        {
            $player1->getPlayer()->sendMessage(AD1vs1Util::getPrefix() . " " . $message);
        }

        if($this->player2->isOnline() && ($player2 = $this->player2->getAD1vs1Player()) !== null)
        {
            $player2->getPlayer()->sendMessage(AD1vs1Util::getPrefix() . " " . $message);
        }
    }

    /**
     * Determine if the players can place or break blocks.
     *
     * @param Event $event - The block event.
     */
    abstract public function onEditArena(Event &$event);

    /**
     * @param Event $event - The event being called.
     * List of events that this function is called:
     * - PlayerInteractEvent
     *
     * Determines whether or not the player can use an item or not.
     */
    public function canUseItem(Event &$event)
    {
        if($this->status !== self::STATUS_IN_PROGRESS)
        {
            $event->setCancelled();
            return;
        }

        if($event instanceof PlayerInteractEvent)
        {
            $item = $event->getItem();
            if($item instanceof FlintSteel) {
                $event->setCancelled(!AD1vs1Util::isBuildingKit($this->kit->getLocalizedName()));
            }
        }
    }

    /**
     * @param Position $position
     * @return bool
     *
     * Determines if the duel contains the position.
     */
    abstract public function containsPosition(Position $position);
}