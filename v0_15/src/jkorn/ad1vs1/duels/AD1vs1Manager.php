<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\duels;


use jkorn\ad1vs1\AD1vs1Main;
use pocketmine\Server;

class AD1vs1Manager
{


    /** @var AD1vs1Main */
    private $main;
    /** @var Server */
    private $server;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();
    }


}