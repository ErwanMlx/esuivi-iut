<?php

namespace App\Command;

use App\Entity\Apprenti;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/*
 * Exécuté à partir d'une tâche cron, pour l'exécuter à la racine du projet : php bin/console app:rappel_email
 */
class RappelCommand extends ContainerAwareCommand {

    protected function configure () {
        // On set le nom de la commande
        $this->setName('app:rappel_email');

        // On set la description
        $this->setDescription("Permet d'envoyer des mails de rappel de validation d'une étape");

        // On set l'aide
        $this->setHelp("Voir src/Command/RappelCommand");
    }

    public function execute (InputInterface $input, OutputInterface $output) {

        $liste_apprentis = $this->getContainer()->get('doctrine')->getRepository(Apprenti::class)->searchApprentiRetard(15);

//        $output->writeln((String) var_dump($liste_apprentis));
        foreach ($liste_apprentis as &$apprenti) {
            $this->getContainer()->get('app.emailservice')->relance_apprenti($apprenti->getCompte());
            $output->writeln("Relance envoyée à " . $apprenti->getCompte()->getEmail());
        }
    }
}