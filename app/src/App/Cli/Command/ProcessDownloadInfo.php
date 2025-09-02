<?php

namespace SLONline\App\Cli\Command;

use SilverStripe\Core\Environment;
use SilverStripe\ORM\DataList;
use SLONline\App\Model\DownloadInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Process Download Info Sake Command
 *
 * @author    Lubos Odraska <odraska@slonline.sk>
 * @copyright Copyright (c) 2025, SLONline, s.r.o.
 */
#[AsCommand(name: 'process:downloadinfo', description: '<fg=blue>Process Download Info items in the queue</>', hidden: true)]
class ProcessDownloadInfo extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('id', InputArgument::REQUIRED, 'ID of the DownloadInfo item to process');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        Environment::increaseTimeLimitTo();
        Environment::setMemoryLimitMax(-1);
        Environment::increaseMemoryLimitTo(-1);

        /** @var DownloadInfo $downloadInfo */
        $downloadInfo = DataList::create(DownloadInfo::class)
            ->byID((int)$input->getArgument('id'));

        if ($downloadInfo) {
            $downloadInfo->process();

            return Command::SUCCESS;
        }

        return Command::INVALID;
    }
}
