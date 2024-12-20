<?php
namespace App\Command;

use App\Service\CrearUsuarioService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

// declaro el comando su nombre para luego usarlo en la consola y una descripcion para saver lo que hace
#[AsCommand(name: 'app:create-user', description: 'Crea un nuevo usuario en el sistema')] 
class CrearUsuarioCommand extends Command // heredo de la clase Command
{
    private CrearUsuarioService $crearUsuarioService; // variable privada para almacenar el servicio

    public function __construct(CrearUsuarioService $crearUsuarioService) // constructor con el servicio para que pueda ser inyectado
    {
        parent::__construct(); // llamo al constructor padre
        $this->crearUsuarioService = $crearUsuarioService; // asigno el servicio a la variable privada
    }

    protected function execute(InputInterface $input, OutputInterface $output): int // metodo que se ejecuta cuando se ejecuta el comando
    {
        $helper = $this->getHelper('question'); // obtengo el helper de preguntas

        // Preguntar por el email
        $emailQuestion = new Question('Introduce el email del nuevo usuario: ');
        $email = $helper->ask($input, $output, $emailQuestion); // pregunto y lo guardo en la variable $email

        // Preguntar por la contraseña
        $passwordQuestion = new Question('Introduce la contraseña del nuevo usuario: ');
        $passwordQuestion->setHidden(true); // oculto la contraseña
        $password = $helper->ask($input, $output, $passwordQuestion); // pregunto y lo guardo en la variable $password

        // Preguntar por el rol
        $roleQuestion = new Question('Introduce el rol del nuevo usuario (por ejemplo, ROLE_USER): ', 'ROLE_USER'); 
        $role = $helper->ask($input, $output, $roleQuestion); // pregunto y lo guardo en la variable $role

        try { // ahora hago un try/catch para verificar si el email ya existe si existe lo manda un mensaje de error si no lo crea
            $this->crearUsuarioService->createUser($email, $password, $role); // llamo al servicio y lo paso los datos
            $output->writeln('<info>Usuario creado exitosamente con el rol: ' . $role . '</info>'); // mostro el mensaje de exito
        } catch (\InvalidArgumentException $e) { // si el email ya existe lo manda un mensaje de error
            $output->writeln('<error>' . $e->getMessage() . '</error>'); // mostro el mensaje de error
            return Command::FAILURE; // retorno un codigo de error
        }

        return Command::SUCCESS; // retorno un codigo de exito
    }
}
