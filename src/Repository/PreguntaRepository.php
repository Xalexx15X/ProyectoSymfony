<?php

namespace App\Repository;

use App\Entity\Pregunta;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PreguntaRepository extends ServiceEntityRepository // Clase que se encarga de manejar las preguntas
{
    public function __construct(ManagerRegistry $registry) // constructor con el registro de entidades
    {
        parent::__construct($registry, Pregunta::class); // llamo al constructor padre con el objeto pregunta
    }
 
    public function findActiveQuestions(): array // funcion que se encarga de encontrar las preguntas activas
    {
        $now = new \DateTime(); // Fecha y hora actual

        return $this->createQueryBuilder('p') // crear el builder de preguntas
            ->where('p.fecha_inicio <= :now') // filtrar las preguntas que tengan una fecha de inicio menor o igual a la fecha actual
            ->andWhere('p.fecha_fin >= :now OR p.fecha_fin IS NULL') // filtrar las preguntas que tengan una fecha 
                                                                     //de fin mayor o igual a la fecha actual o que no tengan fecha de fin
            ->setParameter('now', $now) // asigno la fecha actual a la variable $now
            ->getQuery() // obtengo el query
            ->getResult(); // obtengo los resultados
    }
    //    /**
    //     * @return Pregunta[] Returns an array of Pregunta objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Pregunta
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
