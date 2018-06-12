<?php

namespace App\Form;

/* src/Form/TestType.php */

use App\Entity\Test;
//table Test dans la BDD

//recupérer les options pour construire les formulaire !
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

//gerer les affichages de données par type, selon le besoin
use Symfony\Component\Form\Extension\Core\Type\TextType; //input et textarea
use Symfony\Component\Form\Extension\Core\Type\IntegerType; //nombre
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; //btn submit
use Symfony\Component\Form\Extension\Core\Type\ChoiceType; //choix multiple

//validateurs pour les données
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;


//on ne met pas le request, on le gerera dans le controller

class TestType extends AbstractType
{
    //création d'un formulaire qui sera appelé dans un controleur
    //function hérité de la classe abstraite, et donc obligation de la remplir
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, array('constraints' => array(new NotBlank(), new Length(array('min' => 3, 'max' => 20))), 'label' => 'Nouveau Label Du Nom'))
                /*->add('prenom', TextType::class,  array('constraints' => array(new Regex(array('pattern' => "/^[a-zA-Zéèàêâùïîüùëç]+$/")))))*/
                ->add('prenom', ChoiceType::class, array('choices' =>array('Marcel'=>1, 'Paul' => 2, 'Maurice' => 3, 'Alain' =>4), 'expanded' => false, 'multiple' => false))
            //expanded = false + multiple = false =>selected
            //expanded = true + multiple = false =>radio
            //expanded = true + multiple = true =>chekbox
            
            //Marcel => 1 >>>> le 1 renvoie à value = "1", donc attention  
                
                ->add('age', IntegerType::class, array('constraints' => array(new Range(array('min' => 18, 'max'=> 60)))))
                ->add('Save', SubmitType::class, array('label' =>'Enregistrer', 'attr' => ['class' => 'btn btn-success']))
                ->getForm();
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class' => Test::class));
        //rattachement à la classe Test qui est liée à ma table Test
    }
}