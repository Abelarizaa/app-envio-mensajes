<?php
// src/Form/Type/MedicoType.php
namespace App\Controller;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {		
		// Creo los campos
        $builder->add('nombre', TextType::class);
	    $builder->add('password', PasswordType::class);
	    $builder->add('email', EmailType::class);
		$builder->add('edad', NumberType::class, array("required" => false));	// La edad es opcional
        $builder->add('aficiones', TextType::class, array("required" => false));
        $builder->add('ciudad', TextType::class, array("required" => false));
        $builder->add('avatar', FileType::class, array("required" => false,
													  "attr" => ['accept' => 'image/jpeg', 'image/png']
													  ));
		
		// Submit
		$builder->add('Enviar', SubmitType::class, array('label' => 'Enviar'));
    }
}