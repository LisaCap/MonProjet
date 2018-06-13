<?php
//src/Vontroller/TestController.php
namespace App\Controller; //on est dans le dossier src, mais on ecrit App. C'est son nom pour l'appeler (c'est par rapport à l'autolaod)

use Symfony\Component\HttpFoundation\Response; //retourner une reponse au format HTML

use Symfony\Component\Routing\Annotation\Route; //pour utiliser les annotation installé via le sension/extra-bundle (voir config symphony)
//il y a eu une creation de fichier dans config/routes/annotations.yaml

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//creer le lien avec Twig

use App\Entity\Test;
//creer le lien avec ma table test , dans ma bdd symf4

use App\Form\TestType;
//creer le lien avec mon objet TestType, mon formulaire sur mesure

//FORMULAIRE/////////////////////////////////////////////////

//pour le formulaire (permet de recuperer $_GET et $_POST)
use Symfony\Component\HttpFoundation\Request;
//pour les types de données de formulaire
use Symfony\Component\Form\Extension\Core\Type\TextType; //input et textarea
use Symfony\Component\Form\Extension\Core\Type\IntegerType; //nombre
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType; //btn submit

//pour les fonctions Services, et on precise le fichier qui nous interesse
use App\Services\Proverbes;

////////////////////////////////////////////////////////////

class TestController extends Controller
{
    //page d'accueil du site
    public function accueil()
    {
        //génération d'un nombre aléatoire entre 0 et 100
        $nombre = mt_rand(0, 100); //fonction mt (math)_rand(random)
        //renvoi d'une réponse HTML
        return new Response('<html><body><h1>Hello</h1><p>Mon nombre aléatoire : ' . $nombre . '</p></body></html>');//Response est un objet prédéfini qui genere des pages HTML
    }
    
    //page bonjour != de la page d'accueil
    public function bonjour($nom, $prenom)
    {
        return new Response('<html><body><p><b>Bonjour </b>' . $prenom . ' ' . $nom . '</p></body></html>');
    }
    
    //la fonctionnalité suivante est possible grace à l'extension du pack qu'on a installer sensio/framework-extra-bundle
    //elle va nous permettre via des annotations de passer des parametres, plutot qu'aller configurer notre fichier routes.yaml
    //tout le code pour bonjour2() se situe ici
    //ATTENTION au cote et guillements. mettre des guillements partout, pas des cotes. sinon le code casse
    
    /**
    * @Route(
    *   "/bonjour2/{nom}/{prenom}",
    *   defaults={"nom": "Test", "prenom": "Toto"},
    *   name = "bonjourAnnotation")
    */
    
    //page bonjour != de la page d'accueil
    public function bonjour2($nom, $prenom)
    {
        return new Response('<html><body><p><b>Bonjour </b>' . $prenom . ' ' . $nom . '</p></body></html>');
    }
    
    /**
    * @Route(
    *   "/bonjour3/{nom}/{prenom}",
    *   defaults={"nom": "Test3", "prenom": "Toto3"},
    *   name = "bonjour3")
    */
    //ici on utilise @Route pck on a installé sensio extra bundle
    
    public function bonjour3($nom, $prenom)
    {
        //un tableau simple ou l'on effectuera une boucle dessus dans bonjour3.html.twig
        $a1 = [1,2,3,4,5,6,7,8,9,10];
        
        //un tableau associatif ou l'on effectuera une boucle dessus dans bonjour3.html.twig
        $a2 = array(
                array('nom' => 'Tshirt', 'prix' => 4),
                array('nom' => 'Robe', 'prix' => 30),
                array('nom' => 'Chemise', 'prix' => 10)
                
        );
        
        //appel du template test/bonjour3.html.twig
        return $this->render('test/bonjour3.html.twig',
                             array('title'=> 'Hello Twig',
                                   'nom' => $nom,
                                   'prenom' => $prenom,
                                   'a1' => $a1,
                                   'a2' => $a2));
    }
    
    /**
    * @Route(
    *   "/exercice1/{nombre_repetition}",
    *   defaults={"nombre_repetition": "1"},
    *   name = "nombrealeatoire",
    *   requirements={"nombre_repetition"="\d+"})
    */
    
    //le parametre \d+ indique que je veux obligatoirement un nombre.
    //si j'avais ecris \d, ce serait forcement un chiffre de 0 à 9 qui serait attendu
    
    //ici on fait un exercice où on affiche x fois un nombre aléatoire, dont x est un parametre que je rentre dans le GET
    public function exercice1($nombre_repetition)
    {
        
        $tmp = array();
            
        for($i = 0; $i <= $nombre_repetition; $i++)
        {
            $tmp[] = " * " . mt_rand(0, 100) . " * ";
        }
            
        
        
        
        return $this->render('test/exercice1.html.twig',
                             array('title'=> 'Exercice 1',
                                   'tmp' => $tmp
                                   ));
    }
    
    public function footer(Proverbes $proverbe)
    {
        $message = $proverbe->getMsg();
        // faire apparaitre la date et l'heure
        $dte = date('d/m/Y H:i:s');
        //appel du template footer.html.twig
        return $this->render('test/footer.html.twig', array('dte' => $dte, 'message' => $message));
    }
    
    /**
    * @Route(
    *   "/ajoutTest",
    *   name = "ajoutTest")
    */
    
    public function ajoutTest()
    {
        //on recupere un gestionnaire d'entité
        $em = $this->getDoctrine()->getManager();
        //$this = ma page
        //getDoctrine = mon gestionnaire de bdd
        // getManager = gestionnaire d'entité (entité = relation table/classe)
        
        $test = new Test(); //issu du fichier Test.php, pour avoir acces aux getter et setter des champs de la table
        $test->setNom('DUPOND');
        $test->setPrenom('Georgette');
        $test->setAge(mt_rand(18,70));
        
        //generer la requete SQL correspondant
        $em->persist($test);//equivalence d'un prepare
        //executer la requete
        $em->flush();//equivalence d'un execute
        
        //reponse (normalement on devrait faire un template)
        return new Response('Enregistré avec le numéro' . $test->getId());
        //Pour verifier : taper dans le navigateur : http://localhost/MonProjet/public/ajoutTest
        //et il affiche : Enregistré avec le numéro1 (ca marche ! )
    }
    
    /**
    * @Route(
    *   "/lireTest/{id}",
    *   name = "lireTest",
    *   requirements={"id":"\d+"},
    *   defaults={"id":1})
    */
    
    public function lireTest($id)
    {
        //trouver l'enregistrement d'id avec $id via le repository
        $test = $this->getDoctrine()//methode predefini de Doctrine //chercher mon mapping vers ma BDD (rapport classe/table)
                     ->getRepository(Test::class) //le repository permet de lire dans une table
                     ->find($id);// find permet de trouver la ligne dont l'id est $id
                  // ->findAll(); retourne toute la table
                  // ->findBy(['prenom' => 'Michel']); retourne tous les enregistrements dont le nom est Michel
                  // ->findOneBy(['prenom' => 'Michel', 'nom' => 'MARTIN']); retourne l'enregistrment dont le nom est MARTIN et le prenom est Michel
        
        return new Response(print_r($test, true)); //true permet de renvoyer une chaine de caractere (evite les erreurs, car Response ne peut renvoyer qu'une chaine de caractere)
        
        //on a decrit la fonction debug info dans mon TestController, et là on lui a dit de n'afficher que l'id et le nom, donc on a modifier l'affichage du print_r
    }
    
    /**
    * @Route(
    *   "/modifTest/{id}",
    *   name = "modifTest",
    *   requirements={"id":"\d+"},
    *   defaults={"id":1})
    */
    
    public function modifTest($id)
    {
        //il faut d'abord lire la cellule avant de la modifier
        $test = $this->getDoctrine()
                     ->getRepository(Test::class) 
                     ->find($id);
        //on modifie l'enregistrement
        $test->setNom('Martine');
        $em = $this->getDoctrine()->getManager();
        $em->persist($test);//prepare()
        $em->flush();//execute()
        
        return new Response('Modifier le numéro' . $test->getId());
        
    }
    
    /**
    * @Route(
    *   "/supprTest/{id}",
    *   name = "supprTest",
    *   requirements={"id":"\d+"},
    *   defaults={"id":1})
    */
    
    public function supprTest($id)
    {
        //il faut d'abord lire la cellule avant de la supprimer
        $test = $this->getDoctrine()
                     ->getRepository(Test::class) 
                     ->find($id);
        //on supprime l'enregistrement
        
        if($test) //si l'id existe
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($test);//prepare() du delete
            $em->flush();//execute()

            return new Response("Ok c'est supprimé!");
            
        } else{ //si l'id n'existe plus/pas
            return new Response('L\'id n\'existe plus');
        }
    }
    
    /**
    * @Route(
    *   "/lireMaRequeteAMoi/{age}",
    *   name = "lireMaRequeteAMoi",
    *   requirements={"age":"\d+"},
    *   defaults={"age":1})
    */
    
    public function lireMaRequeteAMoi($age)
    {
        //trouver l'enregistrement d'id avec $id via le repository
        $test = $this->getDoctrine()//methode predefini de Doctrine //chercher mon mapping vers ma BDD (rapport classe/table)
                     ->getRepository(Test::class) //le repository permet de lire dans une table
                     ->findAllGreaterThanAge($age);// ici on recupere via le TestRepository.php la requete speciale que j'ai faite
        
        return new Response(print_r($test, true)); 
    }
    
    /**
    * @Route(
    *   "/formTest",
    *   name = "formTest")
    */
    
    public function formTest(Request $request)
    {
        //(Request $request) : injection de dependance
        //request contient la validation de mon formulaire. equivalent d'un $_POST
        //donc si $request contient qqchose, c'est que le formulaire a été validé
        
        //Création d'un objet Test (lien avec ma table Test dans ma BDD)
        $test = new Test();
        //création du formulaire dans le controleur
        $form = $this->createFormBuilder($test) // methode predefini de symfony, et je precise avec quelle table je travaille
                    ->add('nom', TextType::class)
                    ->add('prenom', TextType::class)
                    ->add('age', IntegerType::class)
                    ->add('Save', SubmitType::class, array('label' =>'Enregistrer', 'attr' => ['class' => 'btn btn-success']))
                    ->getForm();
                    //si je veux rajouter des contraintes, c'est dans le add qu'il faut que je le mette
        //Traitemnt du formulaire
            //recuperation des données (on "attrape" ->handle)
            $form->handleRequest($request);
        
            //si formulaire soumis et validé, je le lie à ma BDD pour une insertion
            if($form->isSubmitted() && $form->isValid())
            {
                //recuperation des infos saisies
                $test = $form->getData();
                //connexion BDD + enregistrement
                $em = $this->getDoctrine()->getManager();
                $em->persist($test); //prepare()
                $em->flush(); //execute()
                
                //retour de l'utilisation sur la page Index (comme un location en PHP)
                return $this->redirectToRoute('index');
            }
            
        
        //Relier à ma vue dans mon template twig
        return $this->render('test/formTest.html.twig', array('form' => $form->createView()));
    }
    
    
    /**
    * @Route(
    *   "/formTest2",
    *   name = "formTest2")
    */
    
    public function formTest2(Request $request)
    {
        //message flash(liste d'attente message)
        $this->addFlash('message', 'Hello');
        $this->addFlash('message', 'World');
        $this->addFlash('message', 'une autre message');
        $this->addFlash('message', 'ééééééééééh');
        
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        //relié à la classe TestType présent dans src/Form
        
        //Traitemnt du formulaire
            //recuperation des données (on "attrape" ->handle)
            $form->handleRequest($request);
        
            //si formulaire soumis et validé, je le lie à ma BDD pour une insertion
            if($form->isSubmitted() && $form->isValid())
            {
                //recuperation des infos saisies
                $test = $form->getData();
                //connexion BDD + enregistrement
                $em = $this->getDoctrine()->getManager();
                $em->persist($test); //prepare()
                $em->flush(); //execute()
                
                //retour de l'utilisation sur la page Index (comme un location en PHP)
                return $this->redirectToRoute('index');
            }
            
        
        //Relier à ma vue dans mon template twig
        return $this->render('test/formTest2.html.twig', array('form' => $form->createView()));
    }
    
     /**
    * @Route(
    *   "/proverbes",
    *   name = "proverbes")
    */
    
    public function affichageProverbes(Proverbes $proverbe)
    {
        //on injecte la classe Proverbe, et la variable $proverbe est issu de cette classe
        
        
        
        $nombre = mt_rand(0, 6);
        //recuperation du proverbe dans le service
        $message = $proverbe->getMsg();
        
        //creation d'un message flash
        $this->addFlash('proverbe', $message);
        
        //Relier à ma vue dans mon template twig
        return $this->render('test/proverbes.html.twig', array('nombre' => $nombre));
    }
    
}