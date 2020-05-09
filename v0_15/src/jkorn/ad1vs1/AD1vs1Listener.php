<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\level\generator\Generator;
use pocketmine\Server;

class AD1vs1Listener implements Listener
{

    /** @var AD1vs1Main */
    private $main;
    /** @var Server */
    private $server;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();

        $this->server->getPluginManager()->registerEvents($this, $main);
    }

    /**
     * @param PlayerJoinEvent $event
     * Called when the player joins.
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        ($playerManager = AD1vs1Main::getPlayerManager())->putPlayer($player);
        $player = $playerManager->getPlayer($player);
        $player->onJoin();
    }

    /**
     * @param PlayerQuitEvent $event
     * Called when the player leaves the game.
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        $player->onDisconnect();
    }
}