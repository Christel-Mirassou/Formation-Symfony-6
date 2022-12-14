<?php

namespace App\Command;

use App\Consumer\OmdbApiConsumer;
use App\Provider\MovieProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:movie:find',
    description: 'Find a movie by title or Omdb Id',
)]
class MovieFindCommand extends Command
{
    //pour faire la recherche il nous faut le provider
    public function __construct(
        private MovieProvider $provider
    ) {
        parent::__construct();
    }

    
    protected function configure(): void
    {
        $this
            //on fait la recherche en utilisant 2 arguments optionnels pour que l'on pose la question:
            // - le premier est la valeur de recherche (le titre ou l'ID)
            ->addArgument('value', InputArgument::OPTIONAL, 'The title or id you wish to search for')
            // - le second est le type de recherche (t pour titre ou i pour ID)
            ->addArgument('type', InputArgument::OPTIONAL, 'The type of search (t or i')

            //On ne va pas utiliser d'option pour cette commande
            // ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //on injecte le SymfonyStyle dans le MovieProvider pour pouvoir utiliser les méthodes de SymfonyStyle
        $this->provider->setSymfonyStyle($io);

        //Puisque les arguments sont optionnels on vérifie s'ils sont présents
        if (!$value = $input->getArgument('value')) {
            //Si non on pose la question
            $value = $io->ask('What is the title or Imdb ID of the movie?');
        }

        //On récupère le type de recherche
        $type = $input->getArgument('type');
        //Une foisla question posée on vérifie quel est le type de recherche
        if (!\in_array($type, [OmdbApiConsumer::MODE_ID, OmdbApiConsumer::MODE_TITLE])) {
            //l'utilisateur est obligé de choisir grâce au choice, le type est passé sous la forme d'un tableau associatif
            $type = $io->choice('What is the type of search?', ['t' => 'Title', 'i' => 'Imdb ID']);
        }

        //affichage intermédiaire 
        $io->title(sprintf("You are searching for a movie with %s \" %s\"", $type, $value));

        //o, utilise un try/catch pour gérer les films non trouvés
        try {
            //on récupère le film recherché
            $movie = $this->provider->getMovie($type, $value);
        } catch (\Exception $e) {
            //si le film n'est pas trouvé on affiche un message d'erreur
            $io->error('Movie not found!');
            return Command::FAILURE;
        }

        //affichage final du film trouvé sous forme d'un tableau dans le terminal
        $io->table(
            //header du tableau
            ['Id', 'Imdb Id', 'Title', 'Rated'],
            //un tableau de tableau contenant les données (body)
            [
                [$movie->getId(), $movie->getImdbId(), $movie->getTitle(), $movie->getRated()]
            ]
        );

        //Message final de succès dans le terminal
        $io->success('Movie found!');

        return Command::SUCCESS;
    }
}
