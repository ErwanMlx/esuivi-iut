<?php
/**
 * Created by PhpStorm.
 * User: Erwan
 * Date: 24/02/2018
 * Time: 16:59
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class LogAutoController extends Controller
{



    /**
     * Page de log auto pour les tests
     *
     * @Route("/logauto/", name="page_logauto")
     */
    public function page_logauto() {
        return new Response(
            '
            <a href="' . $this->generateUrl('logauto', array('type' => 'IUT')) .'">Connexion en tant que IUT</a><br>
            <a href="' . $this->generateUrl('logauto', array('type' => 'ADMIN')) .'">Connexion en tant que IUT ADMIN</a><br>
            <a href="' . $this->generateUrl('logauto', array('type' => 'APPRENTI')) .'">Connexion en tant que APPRENTI</a><br>
            <a href="' . $this->generateUrl('logauto', array('type' => 'CFA')) .'">Connexion en tant que CFA</a><br>
            <a href="' . $this->generateUrl('fos_user_security_login') .'">Page de login normale</a>
            '
        );

    }

    /**
     * Page de log auto pour les tests
     *
     * @Route("/logauto/{type}", name="logauto")
     */
    public function logauto(Request $request, $type) {
        $role = 'ROLE_'.$type;

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneByRole($role);

//        if(!$user) {
//            return new Response(
//                'Il n\'existe pas d\'utilisateur avec le role'. $role
//            );
//        }

        //Handle getting or creating the user entity likely with a posted form
        // The third parameter "main" can change according to the name of your firewall in security.yml
        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);

        // If the firewall name is not main, then the set value would be instead:
        // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));
        $this->get('session')->set('_security_main', serialize($token));

        // Fire the login event manually
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        /*
         * Now the user is authenticated !!!!
         * Do what you need to do now, like render a view, redirect to route etc.
         */
        return $this->redirectToRoute('home');
    }

}