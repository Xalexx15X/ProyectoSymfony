<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController // Clase que se encarga de manejar la página de login y logout
{
    // ruta para la página de login
    #[Route(path: '/login', name: 'app_login')]
    // funcion que se encarga de manejar la página de login y que recibe el manager de entidades
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError(); // obtengo el error de autenticación
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername(); // obtengo el último nombre de usuario

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]); // renderizo la vista
    }

    // ruta para la página de registro
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void // funcion que se encarga de manejar la página de registro
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.'); // si no se puede hacer nada, se intercepta por la llave de logout en su firewall.  
    }
}
