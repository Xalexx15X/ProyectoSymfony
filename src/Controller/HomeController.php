<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController // Clase que se encarga de manejar la página de inicio
{
    #[Route('/', name: 'home')] // ruta que se encarga de manejar la página de inicio
    public function index(): Response
    {
        $user = $this->getUser(); // obtengo el usuario

        // si el usuario es admin o usuario me redirigo a la página de preguntas de admin o usuario
        if ($user && in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('admin_preguntas');
        } elseif ($user && in_array('ROLE_USER', $user->getRoles())) {
            return $this->redirectToRoute('pregunta_usuario');
        }

        // si no es admin ni usuario me redirigo a la página de login
        return $this->redirectToRoute('app_login');
    }
}
