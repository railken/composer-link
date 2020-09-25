<?php

namespace Railken\ComposerLink;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Eloquent\Composer\Configuration\ConfigurationReader;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use SplFileInfo;

class Command extends BaseCommand
{
    /**
     * @var \Eloquent\Composer\Configuration\ConfigurationReader
     */
    protected $composerReader;

    /**
     * @var \Symfony\Component\Cache\Simple\FilesystemCache;
     */
    protected $cache;
    
    /**
     * Create a new instance of the command.
     */
    public function __construct()
    {
        $this->composerReader = new ConfigurationReader();
        $this->cache = new FilesystemAdapter();

        parent::__construct();
    }

    public function parseKeyCache(string $key): string
    {
        return str_replace("/", '-', $key);
    }

    public function info(OutputInterface $output, $message)
    {
        $infoStyle = new OutputFormatterStyle('white', 'blue');
        $output->getFormatter()->setStyle('info', $infoStyle);

        $output->writeln($this->getHelper('formatter')->formatBlock(['INFO: ', $message], 'info', true));
    }

    public function error(OutputInterface $output, $message)
    {
        $infoStyle = new OutputFormatterStyle('white', 'red');
        $output->getFormatter()->setStyle('info', $infoStyle);

        $output->writeln($this->getHelper('formatter')->formatBlock(['ERROR: ', $message], 'error', true));
    }

    /**
     * Recursively delete a directory and all of it's contents - e.g.the equivalent of `rm -r` on the command-line.
     * Consistent with `rmdir()` and `unlink()`, an E_WARNING level error will be generated on failure.
     *
     * @param string $source absolute path to directory or file to delete.
     * @param bool   $removeOnlyChildren set to true will only remove content inside directory.
     *
     * @return bool true on success; false on failure
     */
    function rmdir($source, $removeOnlyChildren = false)
    {
        if(empty($source) || file_exists($source) === false)
        {
            return false;
        }

        if(is_file($source) || is_link($source))
        {
            return unlink($source);
        }

        $files = new RecursiveIteratorIterator
        (
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach($files as $fileinfo)
        {
            if($fileinfo->isDir())
            {
                if($this->rmdir($fileinfo->getRealPath()) === false)
                {
                    return false;
                }
            }
            else
            {
                if(unlink($fileinfo->getRealPath()) === false)
                {
                    return false;
                }
            }
        }

        if($removeOnlyChildren === false)
        {
            return rmdir($source);
        }

        return true;
    }

    public function setCacheItem($key, $value)
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($value);
        $this->cache->save($cacheItem);
    }

    public function getCacheItem($key)
    {
        $cacheItem = $this->cache->getItem($key);
        return $cacheItem->get();
    }

    public function hasCacheItem($key)
    {
        $cacheItem = $this->cache->getItem($key);
        return $cacheItem->isHit();
    }

    public function unsetCacheItem($key)
    {
        $this->cache->deleteItem($key);
    }
}
