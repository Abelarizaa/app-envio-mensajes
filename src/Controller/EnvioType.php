<?php
// src/Form/Type/MedicoType.php
namespace App\Controller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SelectType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Entity\Mensaje;
use App\Entity\Usuario;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

class EnvioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		$usuarios = $options['data']['usuarios'];
		$usuActual = $options['data']['usuActual'];
		$nombreUsus = array();
		foreach($usuarios as $usuario) {
			$user = $usuario->getUsuario();
			
			if ((strcmp($user, $usuActual) !== 0) && $usuario->getActivado() == 1) {
							//array_push($nombreUsus,  $usuario->getUsuario());
			array_push($nombreUsus,$user);
			}


		}
		$misUsus = array_combine($nombreUsus, $nombreUsus);
		// Creo los campos
		$builder->add('destinatarios', ChoiceType::class, array("expanded" => false,												
																	  "multiple" => true,
															          "choices" => $misUsus,

															   ));

        $builder->add('mensaje', TextareaType::class);

		
		// Submit
		$builder->add('Enviar', SubmitType::class, array('label' => 'Enviar'));
    }
}