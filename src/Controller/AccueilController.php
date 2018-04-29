<?php
namespace App\Controller;

use App\Kernel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route; //To define the route to access it
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AccueilController extends Controller
{
    /**
     * @Route("/", name="home") //The route to access the next function
     */
    public function default(AuthorizationCheckerInterface $authChecker)
    {
        if($authChecker->isGranted('ROLE_IUT') || $authChecker->isGranted('ROLE_CFA') || $authChecker->isGranted('ROLE_MAITRE_APP')) {
            return $this->redirectToRoute('liste');
        }
        else if ($authChecker->isGranted('ROLE_APPRENTI')) {
            return $this->redirectToRoute('suivi_perso');
        }
        else {
            if($_SERVER['APP_ENV'] == 'dev') {
                return $this->render('accueil/logauto.html.twig');
            }
            else {
                return $this->redirectToRoute('fos_user_security_login');
            }
        }
    }
}
?>