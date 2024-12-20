<?php
namespace App\Form;

use App\Entity\Pregunta;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PreguntaType extends AbstractType // Clase que se encarga de crear el formulario de pregunta
{
    public function buildForm(FormBuilderInterface $builder, array $options): void // metodo que se encarga de crear el formulario
    {
        $builder // crear el formulario
            // crear los campos de la pregunta
            ->add('titulo', TextType::class, [ 
                'label' => 'Título', // le asigno el título al campo
                'required' => true, // le asigno que el campo sea obligatorio
            ]) 
            // crear los campos de fecha de inicio y fin
            ->add('fecha_inicio', DateTimeType::class, [
                'widget' => 'single_text', // asigno el widget de fecha de inicio
                'label' => 'Fecha de Inicio', // asigno el título al campo
                'required' => true, // asigno que el campo sea obligatorio
            ])
            // crear los campos de fecha de inicio y fin
            ->add('fecha_fin', DateTimeType::class, [
                'widget' => 'single_text', // asigno el widget de fecha de fin
                'label' => 'Fecha de Fin', // asigno el título al campo
                'required' => false, // asigno que el no es necesario que el campo sea obligatorio
            ]) 
            // crear los campos de las respuestas
            ->add('respuesta1', TextType::class, [
                'label' => 'Respuesta 1', // asigno el título al campo
                'required' => true, // asigno que el campo sea obligatorio
            ])
            ->add('respuesta2', TextType::class, [
                'label' => 'Respuesta 2', // asigno el título al campo
                'required' => true, // asigno que el campo sea obligatorio
            ])
            ->add('respuesta3', TextType::class, [
                'label' => 'Respuesta 3', // asigno el título al campo
                'required' => false, // asigno que el campo no es obligatorio 
            ])
            ->add('respuesta4', TextType::class, [
                'label' => 'Respuesta 4', // asigno el título al campo
                'required' => false, // asigno que el campo no es obligatorio
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void // metodo que se encarga de configurar las opciones del formulario
    {
        $resolver->setDefaults([ // asigno las opciones por defecto
            'data_class' => Pregunta::class, // asigno la clase de la entidad
        ]);
    }
}
