<?php
namespace App\Service; 

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CrearUsuarioService // Clase que se encarga de crear un usuario en la base de datos
{
    private EntityManagerInterface $entityManager; // Entidad de la base de datos
    private UserRepository $userRepository; // Repositorio de usuarios
    private UserPasswordHasherInterface $passwordHasher; // Hasher de contraseñas

    public function __construct( // Constructor con los servicios que se necesitan
        EntityManagerInterface $entityManager, // Entidad de la base de datos
        UserRepository $userRepository, // Repositorio de usuarios
        UserPasswordHasherInterface $passwordHasher // Hasher de contraseñas
    ) {
        $this->entityManager = $entityManager; // Asigno el servicio a la variable privada
        $this->userRepository = $userRepository; // Asigno el servicio a la variable privada
        $this->passwordHasher = $passwordHasher; // Asigno el servicio a la variable privada
    }

    public function createUser(string $email, string $password, string $role): void // Método que crea un usuario
    {
        // primero verifico si el email ya existe
        $existingUser = $this->userRepository->findOneBy(['email' => $email]);
        if ($existingUser) { // si existe lo manda un mensaje de error
            throw new \InvalidArgumentException('El correo electrónico ya está registrado.');
        }

        // si no existe crear el usuario
        $user = new User(); // Crear el nuevo usuario con el constructor de la clase User
        $user->setEmail($email); // le asigno el email al usuario
        $user->setRoles([$role]); // le asigno el rol al usuario por defecto ROLE_USER
        // ahora la contraseña la codifico y se la paso al usuario
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        // por ultimo hacemos el flush y persist del usuario
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
