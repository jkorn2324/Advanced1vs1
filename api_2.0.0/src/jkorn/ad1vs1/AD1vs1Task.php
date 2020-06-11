<?php

declare(strict_types=1);

namespace jkorn\ad1vs1;


use pocketmine\scheduler\Task;

class AD1vs1Task extends Task
{
    /** @var int */
    private $currentTick = 0;
    /** @var AD1vs1Main */
    private $main;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $main->getServer()->getScheduler()->scheduleRepeatingTask($this, 1);
    }

    /**
     * Actions to execute when run
     *
     * @param $currentTick
     *
     * @return void
     */
    public function onRun($currentTick)
    {
        AD1vs1Main::get1vs1Manager()->update();
        $this->currentTick++;
    }

    /**
     * Cancels the task.
     */
    public function cancel()
    {
        $server = $this->main->getServer();
        $server->getScheduler()->cancelTask($this->getTaskId());
    }

}