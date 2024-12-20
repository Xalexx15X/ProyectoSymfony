<?php
namespace App\Controller;

use App\Entity\Pregunta;
use App\Entity\Respuesta;
use App\Form\PreguntaType;
use App\Repository\PreguntaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController // Clase que se encarga de manejar las preguntas de administrador
{
    // ruta para mostrar el index que sera la tabla de preguntas que el administrador creara, editara y eliminara
    #[Route('/admin/preguntas', name: 'admin_preguntas')]
    // funcion que se encarga de mostrar el index y que recibe el repositorio de preguntas
    public function index(PreguntaRepository $preguntaRepository): Response 
    {
        // primero le muestro todas las preguntas para eso le digo que me de el findAll 
        $preguntas = $preguntaRepository->findAll();

        //una vez que tengo las preguntas me muestro en la vista
        return $this->render('admin/preguntas/index.html.twig', [
            'preguntas' => $preguntas, // le paso las preguntas a la vista
        ]);
    }

    // ruta para crear una nueva pregunta 
    #[Route('/admin/preguntas/new', name: 'admin_pregunta_new')]
    // funcion que se encarga de crear una nueva pregunta y que recibe el formulario y el manager de entidades
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pregunta = new Pregunta(); // creo  el objeto pregunta
        // ahora creo una variable la cual va a crear el formulario y le va a pasar el objeto pregunta
        $form = $this->createForm(PreguntaType::class, $pregunta); 

        $form->handleRequest($request); // le paso el request al formulario

        if ($form->isSubmitted() && $form->isValid()) { // si el formulario es válido
            // ahora guardo la pregunta
            $pregunta = $form->getData(); // le paso el formulario al objeto pregunta
            $entityManager->persist($pregunta); // le paso el objeto pregunta al manager de entidades
            $entityManager->flush(); // ahora guardo la pregunta

            // ahora creo las respuestas como enteros y las guardo en un array
            $respuestasTexto = [
                $pregunta->getRespuesta1(), 
                $pregunta->getRespuesta2(),
                $pregunta->getRespuesta3(),
                $pregunta->getRespuesta4(),
            ];

            // ahora hago un foreach para crear las respuestas y asociarlas con la pregunta
            foreach ($respuestasTexto as $indice => $respuestaTexto) { 
                if ($respuestaTexto) { // si el texto no está vacío
                    $respuesta = new Respuesta(); // creo la respuesta
                    $respuesta->setRespuesta($indice + 1); // le asigno el número de la respuesta (1, 2, 3, 4)
                    $respuesta->setPregunta($pregunta);   // le asigno la pregunta
                    $entityManager->persist($respuesta); // le paso la respuesta al manager de entidades
                }
            }

            $entityManager->flush(); // ahora guardo todas las respuestas

            $this->addFlash('success', 'Pregunta creada exitosamente.'); // me muestro un mensaje de exito
            return $this->redirectToRoute('admin_preguntas'); // me redirigo a la tabla de preguntas
        }

        //por ultimo renderizo la vista
        return $this->render('admin/preguntas/new.html.twig', [
            'form' => $form->createView(), // le paso el formulario a la vista
        ]);
    }

    // ruta para editar una pregunta
    #[Route('/admin/preguntas/edit/{id}', name: 'admin_pregunta_edit', methods: ['GET', 'POST'])] // ruta que recibe el id de la pregunta y el manager de entidades
    // funcion que se encarga de editar una pregunta y que recibe el formulario y el manager de entidades
    public function edit(Request $request, Pregunta $pregunta, EntityManagerInterface $entityManager): Response
    {
        // primero obtengo las respuestas individuales
        $respuestas = [
            $pregunta->getRespuesta1(),
            $pregunta->getRespuesta2(),
            $pregunta->getRespuesta3(),
            $pregunta->getRespuesta4(),
        ];

        // ahora creo el formulario
        $form = $this->createForm(PreguntaType::class, $pregunta); // guardo en la variavle form el crear formulario con el objeto pregunta
        $form->handleRequest($request); // le paso el request al formulario

        if ($form->isSubmitted() && $form->isValid()) { // si el formulario es válido
            // Guardar la pregunta
            $entityManager->flush(); // ahora guardo la pregunta

            $this->addFlash('success', 'Pregunta editada exitosamente.'); // me muestro un mensaje de exito
            return $this->redirectToRoute('admin_preguntas'); // me redirigo a la tabla de preguntas
        }

        // por ultimo renderizo la vista
        return $this->render('admin/preguntas/edit.html.twig', [ 
            'form' => $form->createView(), // le paso el formulario a la vista
            'pregunta' => $pregunta, // le paso la pregunta a la vista del twig
            'respuestas' => $respuestas,  // le paso las respuestas a la vista del twig
        ]);
    }

    // ruta para eliminar una pregunta
    #[Route('/admin/preguntas/delete/{id}', name: 'admin_pregunta_delete', methods: ['POST'])]
    // funcion que se encarga de eliminar una pregunta y que recibe el manager de entidades
    public function delete(Pregunta $pregunta, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($pregunta); // elimino la pregunta
        $entityManager->flush(); // ahora guardo la pregunta

        $this->addFlash('success', 'Pregunta eliminada exitosamente.'); // me muestro un mensaje de exito
        return $this->redirectToRoute('admin_preguntas'); // me redirigo a la tabla de preguntas que esto lo que hace es que recarge la pagina para que se vaya
    }

    // ruta para mostrar una pregunta
    #[Route('/admin/preguntas/{id}', name: 'admin_pregunta_show', methods: ['GET'])]
    // funcion que se encarga de mostrar una pregunta y que recibe el manager de entidades
    public function show(Pregunta $pregunta, EntityManagerInterface $entityManager): Response 
    {
        // primero contar la cantidad de respuestas para esta pregunta
        $respuestas = $entityManager->getRepository(Respuesta::class)->findBy(['pregunta' => $pregunta]);
        $conteoRespuestas = [1 => 0, 2 => 0, 3 => 0, 4 => 0];  // inicializo el conteo para las respuestas 1, 2, 3 y 4

        // ahora hago un foreach para contar las respuestas
        foreach ($respuestas as $respuesta) {
            if ($respuesta->getUser() !== null) { // solo contar si la respuesta tiene un usuario asociado
                $numeroRespuesta = $respuesta->getRespuesta(); // obtengo el número de la respuesta (1, 2, 3 o 4)
                if (isset($conteoRespuestas[$numeroRespuesta])) { // si el numero de la respuesta existe en el array
                    $conteoRespuestas[$numeroRespuesta]++; // lo incremento
                }
            }
        }

        // por ultimo renderizo la vista
        return $this->render('admin/preguntas/show.html.twig', [
            'pregunta' => $pregunta, // le paso la pregunta a la vista del twig
            'conteoRespuestas' => $conteoRespuestas,  // le paso los conteos a la vista del twig
        ]);
    }
}
