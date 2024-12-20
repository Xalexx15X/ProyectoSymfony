<?php
namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ServicioCambioContrasena
{
    private $gestoEntidad;
    private $repositorioUsuario;
    private $codificadorContrasena;

    public function __construct(
        EntityManagerInterface $gestoEntidad, 
        UserRepository $repositorioUsuario,
        UserPasswordHasherInterface $codificadorContrasena
    ) {
        $this->gestoEntidad = $gestoEntidad;
        $this->repositorioUsuario = $repositorioUsuario;
        $this->codificadorContrasena = $codificadorContrasena;
    }

    public function cambiarContrasenaUsuario(User $usuario, string $nuevaContrasena): bool
    {
        try {
            // Codificar la nueva contraseña
            $contrasenacodificada = $this->codificadorContrasena->hashPassword(
                $usuario, 
                $nuevaContrasena
            );

            // Establecer la nueva contraseña codificada
            $usuario->setPassword($contrasenacodificada);

            // Persistir y guardar cambios
            $this->gestoEntidad->persist($usuario);
            $this->gestoEntidad->flush();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function buscarUsuarioPorCorreo(string $correo): ?User
    {
        return $this->repositorioUsuario->findOneBy(['email' => $correo]);
    }
}