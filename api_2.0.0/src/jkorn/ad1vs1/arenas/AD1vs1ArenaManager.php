<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\arenas;


use jkorn\ad1vs1\AD1vs1Main;
use pocketmine\level\Level;
use pocketmine\utils\TextFormat;

class AD1vs1ArenaManager
{
    /** @var Level */
    private $server;
    /** @var AD1vs1Main */
    private $main;

    /** @var AD1vs1DuelArena[] */
    private $arenas;

    /** @var array */
    private $inactiveArenas;
    /** @var string */
    private $fileName;

    public function __construct(AD1vs1Main $main)
    {
        $this->main = $main;
        $this->server = $main->getServer();

        $this->arenas = [];
        $this->inactiveArenas = [];

        $this->loadArenas();
    }

    /**
     * Loads the arenas.
     */
    private function loadArenas()
    {
        $dataFolder = $this->main->getDataFolder();
        $this->fileName = $dataFolder . "Arenas.json";

        if(!file_exists($this->fileName)) {
            $file = fopen($this->fileName, "w");
            fclose($file);
            return;
        }

        $contents = json_decode(file_get_contents($this->fileName),true);
        foreach($contents as $localizedName => $data)
        {
            $arena = AD1vs1DuelArena::decode($localizedName, $data);
            if($arena !== null && $arena instanceof AD1vs1DuelArena)
            {
                $this->arenas[$arena->getLocalizedName()] = $arena;
                $this->inactiveArenas[$arena->getLocalizedName()] = true;
            }
        }
    }

    /**
     * @param AD1vs1DuelArena $arena
     * @param bool $create
     * @return bool
     *
     * Adds the duel arena to the list.
     */
    public function addArena(AD1vs1DuelArena $arena, bool $create = true)
    {
        $localizedName = $arena->getLocalizedName();
        if($create)
        {
            if(isset($this->arenas[$localizedName]))
            {
                return false;
            }
        }
        $this->arenas[$localizedName] = $arena;
        if(!isset($this->inactiveArenas[$localizedName])) {
            $this->inactiveArenas[$localizedName] = true;
        }

        return true;
    }

    /**
     * @param string $arena
     * @return bool
     *
     * Removes the arena.
     */
    public function removeArena(string $arena)
    {
        if(!isset($this->arenas[$localized = strtolower($arena)])) {
            return false;
        }

        unset($this->arenas[$localized]);
        if(isset($this->inactiveArenas[$localized]))
        {
            unset($this->inactiveArenas[$localized]);
        }

        return true;
    }

    /**
     * Saves the arenas to the file.
     */
    public function saveArenas()
    {
        if(!file_exists($this->fileName))
        {
            $file = fopen($this->fileName, "w");
            fclose($file);
        }

        $inputContents = [];
        foreach($this->arenas as $arenaLocalized => $arena)
        {
            $inputContents[$arena->getLocalizedName()] = $arena->encode();
        }

        file_put_contents($this->fileName, json_encode($inputContents));
    }

    /**
     * @param string $arena
     * @return AD1vs1DuelArena|null
     *
     * Gets the arena based on its name/localized name.
     */
    public function getArena(string $arena)
    {
        if(!isset($this->arenas[$localized = strtolower($arena)]))
        {
            return null;
        }

        return $this->arenas[$localized];
    }

    /**
     * @param string $name
     * @return bool
     *
     * Determines if the arena exists.
     */
    public function isArena(string $name)
    {
        return isset($this->arenas[strtolower($name)]);
    }

    /**
     * @param string $localized
     * @return bool
     *
     * Sets the arena as active.
     */
    public function setActive(string $localized)
    {
        if(!isset($this->inactiveArenas[$localized]))
        {
            return false;
        }

        unset($this->inactiveArenas[$localized]);
        return true;
    }

    /**
     * @param string $localized
     * @return bool
     *
     * Sets the arena as inactive.
     */
    public function setInActive(string $localized)
    {
        if(isset($this->inactiveArenas[$localized]))
        {
            return false;
        }

        $arena = $this->getArena($localized);
        if($arena !== null)
        {
            $this->inactiveArenas[$localized] = true;
            return true;
        }

        return false;
    }

    /**
     * @param AD1vs1DuelArena $arena
     * @return bool
     *
     * Sets the arena as active or not.
     */
    public function isActive(AD1vs1DuelArena $arena)
    {
        return !isset($this->inactiveArenas[$arena->getLocalizedName()]);
    }

    /**
     * @return AD1vs1DuelArena|null
     *
     * Gets a randomly pre generated arena.
     */
    public function randomPreGenArena()
    {
        if(count($this->inactiveArenas) <= 0)
        {
            return null;
        }

        $localized = array_keys($this->inactiveArenas)[mt_rand(0, count($this->inactiveArenas) - 1)];
        return $this->getArena($localized);
    }

    /**
     * @return array
     *
     * Lists the arenas.
     */
    public function listArenas()
    {
        if(count($this->arenas) <= 0)
        {
            return [TextFormat::RED . "None"];
        }

        $output = [];

        foreach($this->arenas as $arenaLocalized => $arena)
        {
            $string = TextFormat::GOLD . $arena->getName() . TextFormat::GRAY . " [{input}" . TextFormat::GRAY . "]";

            if(isset($this->inactiveArenas[$arenaLocalized])) {
                $input = TextFormat::GREEN . "Inactive";
            } else {
                $input = TextFormat::RED . "Active";
            }

            $output[] = str_replace("{input}", $input, $string);
        }

        return $output;
    }
}