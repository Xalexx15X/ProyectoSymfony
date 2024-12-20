<?php
// src/Controller/UsuarioController.php
namespace App\Controller;

use App\Entity\Pregunta;
use App\Entity\Respuesta;
use App\Repository\PreguntaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UsuarioController extends AbstractController // Clase que se encarga de manejar las preguntas de usuario
{
    private $preguntaRepository; // repositorio de preguntas

    public function __construct(PreguntaRepository $preguntaRepository) // constructor con el repositorio de preguntas
    {
        $this->preguntaRepository = $preguntaRepository; // asigno el repositorio de preguntas
    }

    // ruta para mostrar todas las preguntas de usuario
    #[Route('/preguntas', name: 'pregunta_usuario')]
    public function index(): Response // funcion que se encarga de mostrar todas las preguntas de usuario
    {
        // primero obtengo todas las preguntas activas
        $preguntas = $this->preguntaRepository->findActiveQuestions();

        // ahora recupero las preguntas que ya han sido respondidas por el usuario
        $usuario = $this->getUser();
        $preguntasRespondidas = []; // inicializo el array de preguntas respondidas
        $conteoRespuestas = []; // inicializo el array de conteo de respuestas

        // ahora contar respuestas para cada pregunta
        foreach ($preguntas as $pregunta) {
            // filtro respuestas del usuario
            $respuestasUsuario = $pregunta->getRespuesta()->filter(function($respuesta) use ($usuario) {
                return $respuesta->getUser() === $usuario; // si el usuario es el mismo que el usuario asociado a la respuesta
            });

            // si no es vacío, lo agrego a la lista de preguntas respondidas
            if (!$respuestasUsuario->isEmpty()) {
                $preguntasRespondidas[] = $pregunta->getId(); // agrego el id de la pregunta a la lista de preguntas respondidas
            }
            // ahora cuento las respuestas para cada pregunta 
            $conteoRespuestas[$pregunta->getId()] = $pregunta->getRespuesta()->count();
        }

        // por ultimo renderizo la vista
        return $this->render('usuario/preguntas/index.html.twig', [
            'preguntas' => $preguntas, // le paso las preguntas a la vista del twig
            'preguntasRespondidas' => $preguntasRespondidas, // le paso las preguntas respondidas a la vista del twig
            'conteoRespuestas' => $conteoRespuestas,  // le paso el conteo de respuestas a la vista del twig
        ]);
    }

    // ruta para contestar una pregunta
    #[Route('/pregunta/contestar/{id}', name: 'pregunta_contestar', methods: ["GET", "POST"])]
    // funcion que se encarga de contestar una pregunta y que recibe el manager de entidades
    public function contestar(Pregunta $pregunta, Request $request, EntityManagerInterface $em): Response 
    {
        $usuario = $this->getUser();
        $respuestasUsuario = $pregunta->getRespuesta()->filter(function($respuesta) use ($usuario) {
            return $respuesta->getUser() === $usuario;
        });

        $preguntaRespondida = !$respuestasUsuario->isEmpty();

        // Calcular estadísticas de respuestas
        $conteoRespuestas = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0
        ];
        foreach ($pregunta->getRespuesta() as $resp) {
            if ($resp->getUser() !== null) {
                $conteoRespuestas[$resp->getRespuesta()]++;
            }
        }

        // Crear formulario solo si no ha respondido
        $form = null;
        if (!$preguntaRespondida) {
            $form = $this->createFormBuilder()
                ->add('respuesta', ChoiceType::class, [
                    'choices' => [
                        $pregunta->getRespuesta1() => 1,
                        $pregunta->getRespuesta2() => 2,
                        $pregunta->getRespuesta3() => 3,
                        $pregunta->getRespuesta4() => 4,
                    ],
                    'expanded' => true,
                    'multiple' => false,
                ])
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $respuestaEntidad = new Respuesta();
                $respuestaEntidad->setPregunta($pregunta);
                $respuestaEntidad->setUser($usuario);
                $respuestaEntidad->setRespuesta($form->get('respuesta')->getData());
                $respuestaEntidad->setFechaRespuesta(new \DateTime());

                $em->persist($respuestaEntidad);
                $em->flush();

                $this->addFlash('success', 'Respuesta enviada con éxito.');
                return $this->redirectToRoute('pregunta_usuario');
            }
        }

        return $this->render('usuario/preguntas/contestar.html.twig', [
            'pregunta' => $pregunta,
            'form' => $form ? $form->createView() : null,
            'preguntaRespondida' => $preguntaRespondida,
            'conteoRespuestas' => $conteoRespuestas,
        ]);
    }
    
}
