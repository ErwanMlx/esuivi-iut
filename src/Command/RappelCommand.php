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

        $this->addArgument('nbJours', InputArgument::OPTIONAL, "Au bout de combien de jours faut-il faire une relance ?");
    }

    public function execute (InputInterface $input, OutputInterface $output) {

        $nbJours = $input->getArgument('nbJours');
        if(empty($nbJours)) {
            $nbJours = 15;
        }

        $liste_apprentis_retard = $this->getContainer()->get('doctrine')->getRepository(Apprenti::class)->searchApprentiRetard($nbJours, 'ROLE_APPRENTI');

        foreach ($liste_apprentis_retard as &$apprenti) {
            $this->getContainer()->get('app.emailservice')->relance_apprenti($apprenti->getCompte());
            $output->writeln("Relance envoyée à " . $apprenti->getCompte()->getEmail());
        }

        $liste_apprentis_ma_retard = $this->getContainer()->get('doctrine')->getRepository(Apprenti::class)->searchApprentiRetard($nbJours, 'ROLE_MAITRE_APP');

        foreach ($liste_apprentis_ma_retard as &$apprenti) {
            $this->getContainer()->get('app.emailservice')->relance_ma($apprenti);
            $output->writeln("Relance envoyée à " . $apprenti->getDossier()->getMaitreApprentissage()->getCompte()->getEmail());
        }


    }
}