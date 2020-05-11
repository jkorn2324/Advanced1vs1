<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use jkorn\ad1vs1\duels\Abstract1vs1;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\level\generator\Generator;
use pocketmine\network\protocol\AdventureSettingsPacket;
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

    /**
     * @param PlayerMoveEvent $event
     * Called when the player moves or not.
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);

        if($a1vs1Player !== null) {
            $event->setCancelled($a1vs1Player->onMove(
                $event->getFrom(),
                $event->getTo()
            ));
        }
    }

    /**
     * @param PlayerDeathEvent $event
     * Called when the player dies.
     */
    public function onDeath(PlayerDeathEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        if($a1vs1Player !== null) {
            $message = $event->getDeathMessage();
            $a1vs1Player->onDeath($message);
            $event->setDeathMessage($message);
        }
    }

    /**
     * @param DataPacketSendEvent $event
     *
     * Called when the server sends a packet to the player.
     */
    public function onDataPacketSend(DataPacketSendEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        if ($a1vs1Player !== null) {

            $packet = $event->getPacket();
            if($packet instanceof AdventureSettingsPacket)
            {
                $flags = $packet->flags;
                if($a1vs1Player->isFlying())
                {
                    // Initial test variable, if it doesn't work, test 0x400.
                    $flags |= 0x200;
                }
                $packet->flags = $flags;
            }
        }
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * Called when the player interacts with an item.
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        if($a1vs1Player !== null
            && ($duel = AD1vs1Main::get1vs1Manager()->getDuelFromPlayer($a1vs1Player)) !== null
            && $duel instanceof Abstract1vs1)
        {
            $duel->canEditArena($event);
        }
    }

    /**
     * @param BlockPlaceEvent $event
     *
     * Called when a block is placed.
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        if($a1vs1Player !== null
            && ($duel = AD1vs1Main::get1vs1Manager()->getDuelFromPlayer($a1vs1Player)) !== null
            && $duel instanceof Abstract1vs1)
        {
            $duel->canEditArena($event);
        }
    }

    /**
     * @param BlockBreakEvent $event
     *
     * Called when a block is broken.
     */
    public function onBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();
        $a1vs1Player = AD1vs1Main::getPlayerManager()->getPlayer($player);
        if($a1vs1Player !== null
            && ($duel = AD1vs1Main::get1vs1Manager()->getDuelFromPlayer($a1vs1Player)) !== null
            && $duel instanceof Abstract1vs1)
        {
            $duel->canEditArena($event);
        }
    }
}