<?php

declare(strict_types=1);

namespace jkorn\ad1vs1\level\tasks;


use pocketmine\scheduler\AsyncTask;

class AsyncDeleteLevel extends AsyncTask
{
    /** @var string */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    /**
     * Actions to execute when run
     *
     * @return void
     */
    public function onRun()
    {
        $this->remove($this->directory);
    }

    /**
     * @param string $directory
     *
     * Removes the folder or directory recursively.
     */
    private function remove(string $directory)
    {
        if(!is_dir($directory)) {
            return;
        }

        if(substr($directory, strlen($directory) - 1, 1) != "/")
        {
            $directory .= "/";
        }

        $files = glob($directory . "*", GLOB_MARK);
        foreach($files as $file) {
            if(is_dir($file)) {
                $this->remove($file);
            } else {
                unlink($file);
            }
        }

        rmdir($directory);
    }
}