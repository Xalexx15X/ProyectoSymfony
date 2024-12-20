<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    // ruta para la página de registro
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,// Objeto Request
        UserPasswordHasherInterface $userPasswordHasher, // Objeto UserPasswordHasherInterface
        EntityManagerInterface $entityManager, // Objeto EntityManagerInterface
        MailerService $mailerService // Objeto MailerService
    ): Response { // Tipo de retorno
        $user = new User(); // nuevo objeto User
        $form = $this->createForm(RegistrationFormType::class, $user); // creo el formulario
        $form->handleRequest($request); // manejo la petición
 
        if ($form->isSubmitted() && $form->isValid()) { // si el formulario es enviado y es válido
            // Hashear la contraseña del usuario
            $plainPassword = $form->get('plainPassword')->getData(); // obtengo la contraseña del usuario
            $hashedPassword = $userPasswordHasher->hashPassword($user, $plainPassword); // hasho la contraseña
            $user->setPassword($hashedPassword); // establezco la contraseña hasheada

            // Guardar el usuario en la base de datos
            $entityManager->persist($user); // guardo el usuario en la base de datos
            $entityManager->flush(); // actualizo la base de datos

            $mailerService->sendWelcomeEmail($user); // envio de correo

            // Agregar mensaje de confirmación
            $this->addFlash('success', 'Te hemos mandado un mensaje de confirmación y bienvenida. Revisa tu correo.');

            // Redireccionar al login
            return $this->redirectToRoute('app_login');
        }

        // Si el formulario no es válido, mostrar el formulario de registro
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(), // mostrar el formulario de registro
        ]);
    }
    //http://localhost:8025
}
