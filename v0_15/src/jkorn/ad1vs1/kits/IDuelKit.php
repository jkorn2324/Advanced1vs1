<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\kits;


use jkorn\ad1vs1\player\AD1vs1Player;

interface IDuelKit
{

    function getLocalizedName();

    function sendTo(AD1vs1Player $player);
}