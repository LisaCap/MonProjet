<?php

namespace App\Form;

/* src/Form/TestType.php */

use App\Entity\User;
//table User dans la BDD

//recupérer les options pour construire les formulaire !
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

//gerer les affichages de données par type, selon le besoin
use Symfony\Component\Form\Extension\Core\Type\TextType; //input et textarea
use Symfony\Component\Form\Extension\Core\Type\IntegerType; //nombre
use Symfony\Component\Form\Extension\Core\Type\EmailType; //email
use Symfony\Component\Form\Extension\Core\Type\RepeatedType; //saisir deux fois le mot de passe, et gestion des valeurs identiques
use Symfony\Component\Form\Extension\Core\Type\PasswordType; // password
use Symfony\Component\Form\Extension\Core\Type\SubmitType; //btn submit

//validateurs pour les données
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;


//on ne met pas le request, on le gerera dans le controller

class UserType extends AbstractType
{
    //création d'un formulaire qui sera appelé dans un controleur
    //function hérité de la classe abstraite, et donc obligation de la remplir
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', EmailType::class)
                ->add('username', TextType::class)
                ->add('password', repeatedType::class, array('type' => PasswordType::class, 'first_options' => array('label' =>'Password'), 'second_options' => array('label' =>'Repeat Password')))
                //creer deux champs input qui fait tout les controle et le cryptage
                ->add('Save', SubmitType::class, array('label' =>'Enregistrer', 'attr' => ['class' => 'btn btn-primary']));
                //Tous les champs sont require par default. Il faut le preciser pas "require" : false si on ne le veux pas
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => User::class));
        //rattachement à la classe Test qui est liée à ma table Test
    }
    
    
}