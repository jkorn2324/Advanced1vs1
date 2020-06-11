<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels\queues;


use jkorn\ad1vs1\AD1vs1Main;
use jkorn\ad1vs1\AD1vs1Util;
use jkorn\ad1vs1\kits\AD1vs1KitManager;
use jkorn\ad1vs1\player\AD1vs1Player;
use pocketmine\utils\TextFormat;

class AD1vs1QueuesManager
{

    /** @var AD1vs1Queue[] */
    private $queues;

    /** @var AD1vs1Main */
    private $main;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->queues = [];
    }

    /**
     * @param AD1vs1Player $player
     * @param string $kit
     *
     * Determines whether or not the player is able to be in a queue.
     */
    public function putInQueue(AD1vs1Player $player, string $kit = "")
    {
        if(!$player->isOnline() || $player->isInDuel())
        {
            $this->main->getLogger()->info($player->getDisplayName() . " is in a duel or isn't online.");
            return;
        }

        if(strtolower($kit) !== AD1vs1KitManager::DEFAULT_1VS1_KIT && $kit !== "" && !AD1vs1Main::getKitManager()->putKit($kit))
        {
            $player->getPlayer()->sendMessage(
                AD1vs1Util::getPrefix() . " " . TextFormat::RED . "The kit '{$kit}' doesn't exist!"
            );
            return;
        }

        if($kit === "")
        {
            $kit = AD1vs1KitManager::DEFAULT_1VS1_KIT;
        }

        if(isset($this->queues[$player->getUniqueId()])) {

            $queue = $this->queues[$player->getUniqueId()];
            if($queue->getKit() === $kit) {
                $player->getPlayer()->sendMessage(
                  AD1vs1Util::getPrefix() . " " . TextFormat::RED . " You are already queued for that kit!"
                );
                return;
            }

            unset($this->queues[$player->getUniqueId()]);
        }

        $player->getPlayer()->sendMessage(
            AD1vs1Util::getPrefix() . " " . TextFormat::GREEN . "You have successfully queued for kit '{$kit}'."
        );

        $queue = new AD1vs1Queue($player, $kit);
        $match = $this->findMatch($queue);

        if($match !== null && $match instanceof AD1vs1Queue)
        {
            $this->main->getLogger()->info("Found match!");
            AD1vs1Main::get1vs1Manager()->putInDuel($queue->getPlayer(), $match->getPlayer(), $kit);
            unset($this->queues[$match->getUniqueID()]);
            return;
        }

        $this->queues[$queue->getUniqueID()] = $queue;
    }

    /**
     * @param AD1vs1Player $player
     * @param bool $sendMessage
     *
     * Removes the player from the queue.
     */
    public function removeFromQueue(AD1vs1Player $player, bool $sendMessage = false)
    {
        if(isset($this->queues[$player->getUniqueId()])) {
            unset($this->queues[$player->getUniqueId()]);
        }
    }

    /**
     * @param AD1vs1Queue $queue
     * @return AD1vs1Queue|null
     *
     * Finds a match for the queue.
     */
    private function findMatch(AD1vs1Queue $queue)
    {
        foreach($this->queues as $pending)
        {
            if(!$pending->getPlayer()->isOnline())
            {
                continue;
            }

            if($pending->getKit() === $queue->getKit())
            {
                return $pending;
            }
        }
        return null;
    }

    /**
     * @param AD1vs1Player $player
     * @return bool
     *
     * Determines whether or not the player is in a queue.
     */
    public function isInQueue(AD1vs1Player $player)
    {
        return (isset($this->queues[$uuid = $player->getUniqueId()]));
    }
}