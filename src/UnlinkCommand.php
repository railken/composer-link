<?php

namespace Railken\ComposerLink;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class UnlinkCommand extends Command
{

    protected function configure()
    {
        $this
            ->setName('unlink')
            ->setDescription('Link composer package')
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Target directory', getcwd())
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $composerPath = $input->getOption('dir').'/composer.json';

        if (!file_exists($composerPath)) {
            return $this->error($output, 'No composer.json found');
        }

        $name = $input->getArgument('name');

        if (!empty($name)) {

            $packageDir = $this->cache->get($this->parseKeyCache($name));

            if (!$packageDir) {
                return $this->error($output, sprintf('Cannot find package %s', $name));
            }

            $link = $input->getOption('dir')."/vendor/".$name;

            $this->info($output, sprintf('Unlinked %s', $link));

            if (file_exists($link)) {
                $this->rmdir($link);
            }



        } else {
            $packageName = $this->composerReader->read($composerPath)->name();


            if (!$this->cache->has($this->parseKeyCache($packageName))) { 
                return $this->error($output, sprintf('Package %s is already unlinked', $packageName));
            }

            $this->cache->delete($this->parseKeyCache($packageName));

            return $this->info($output, sprintf('Removed %s: %s', $packageName, $input->getOption('dir')));
        }
    }
}
