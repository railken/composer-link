<?php

namespace Railken\ComposerLink;

use Eloquent\Composer\Configuration\ConfigurationReader;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Railken\ComposerLink\Command as BaseCommand;

class LinkCommand extends BaseCommand
{
    /**
     * @var \Eloquent\Composer\Configuration\ConfigurationReader
     */
    protected $composerReader;

    /**
     * @var \Symfony\Component\Cache\Simple\FilesystemCache;
     */
    protected $cache;

    protected function configure()
    {
        $this
            ->setName('link')
            ->setDescription('Link composer package')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Target directory', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composerPath = $input->getOption('dir').'/composer.json';

        if (!file_exists($composerPath)) {
            $this->error($output, 'No composer.json found');

            return Command::FAILURE;
        }

        $name = $input->getArgument('name');


        if (!empty($name)) {

            $packageDir = $this->getCacheItem($this->parseKeyCache($name));

            if (!$packageDir) {
                $this->error($output, sprintf('Cannot find package %s', $name));

                return Command::FAILURE;
            }

            $link = $input->getOption('dir')."/vendor/".$name;

            $this->info($output, sprintf('Linked %s', $link));

            if (file_exists($link)) {
                $this->rmdir($link);
            }


            symlink($packageDir, $link);


        } else {

            $packageName = $this->composerReader->read($composerPath)->name();

            if ($dirPackage = $this->getCacheItem($this->parseKeyCache($packageName))) { 
                $this->error($output, sprintf('Package %s is already linked at %s', $packageName, $dirPackage));

                return Command::FAILURE;
            }

            $this->setCacheItem($this->parseKeyCache($packageName), $input->getOption('dir'));

            $this->info($output, sprintf('Added %s: %s', $packageName, $input->getOption('dir')));
        }


        return Command::SUCCESS;
    }

}