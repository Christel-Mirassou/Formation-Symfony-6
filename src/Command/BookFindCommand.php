<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:book:find',
    description: 'FInd a movie by title or by Imdb Id',
)]
class BookFindCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::REQUIRED, 'Argument description')  //1er argument Requis
            ->addArgument('arg2', InputArgument::OPTIONAL, 'Argument description')  //2eme argument Optionnel
            ->addArgument('arg3', InputArgument::OPTIONAL|InputArgument::IS_ARRAY, 'Argument description') //3eme argument Optionnel et tableau
            ->addOption('option1', null, InputOption::VALUE_NEGATABLE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
       
        $arg1 = $input->getArgument('arg1');
        if ($arg1) {
            $io->note(sprintf('You passed a first argument: %s', $arg1));
        }
        
        $arg2 = $input->getArgument('arg2');
        if ($arg2) {
            $io->note(sprintf('You passed a second argument: %s', $arg2));
        }

        $arg3 = $input->getArgument('arg3');
        if ($arg3) {
            $io->note(sprintf('You passed a third argument: %s', implode(', ', $arg3)));
        }


        if (null !== $value = $input->getOption('option1')) {
            $value = $value === true ? 'True' : 'False';
            $io->note('You passed an option: '. $value);
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
