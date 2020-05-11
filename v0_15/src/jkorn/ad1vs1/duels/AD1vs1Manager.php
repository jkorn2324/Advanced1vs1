<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\duels\queues\AD1vs1QueuesManager;
use jkorn\ad1vs1\duels\types\PostGenerated1vs1;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\level\generator\Generator;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AD1vs1Manager
{

    /** @var int */
    private static $duelsCount = 0;

    /** @var AD1vs1Main */
    private $main;
    /** @var Server */
    private $server;

    /** @var Abstract1vs1[] */
    private $duels;
    /** @var AD1vs1QueuesManager */
    private $queuesManager;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();

        $this->queuesManager = new AD1vs1QueuesManager();

        $this->duels = [];
    }

    /**
     * @return AD1vs1QueuesManager
     *
     * Gets the queues manager.
     */
    public function getQueuesManager()
    {
        return $this->queuesManager;
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
     * @param AD1vs1Player $player1
     * @param AD1vs1Player $player2
     * @param string $kit
     *
     * Puts the two players in a duel.
     */
    public function putInDuel(AD1vs1Player $player1, AD1vs1Player $player2, string $kit)
    {
        $duelKit = AD1vs1Main::getKitManager()->getKit($kit);
        if($duelKit !== null || !$duelKit->isValid())
        {
            return;
        }

        $matchID = ++self::$duelsCount;

        // TODO: Check if an arena exists.
        if(AD1vs1Util::isBuildingKit($duelKit->getLocalizedName()))
        {
            $randomGenerator = AD1vs1Main::getGeneratorManager()->randomGenerator();
            $levelName = "1vs1.{$matchID}";

            $generator = Generator::getGenerator($randomGenerator->getLocalizedName());

            $this->server->generateLevel($levelName, null, $generator);
            $this->server->loadLevel($levelName);

            $duel = new PostGenerated1vs1($matchID, $player1, $player2, $duelKit);
        }


        if(isset($duel))
        {
            $player1->getPlayer()->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::GREEN . "Found duel against " . TextFormat::WHITE . $player2->getDisplayName() . TextFormat::GREEN . "!"
            );
            $player2->getPlayer()->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::GREEN . "Found duel against " . TextFormat::WHITE . $player1->getDisplayName() . TextFormat::GREEN . "!"
            );

            $this->duels[$duel->getID()] = $duel;
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