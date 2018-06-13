<?php
namespace App\Controller;
//src/Controller/SecurityController

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
//sécurité
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
// table utilisateurs
use App\Entity\User;
//formulaire
use App\Form\UserType;


use Symfony\Component\Routing\Annotation\Route; 

class SecurityController extends Controller
{
    /*
    Inscription d'un utilisateur
    affiche le formulaire et ajoute l'utilisateur dans la table user
    */
    
    /**
    * @Route(
    *   "/inscription",
    *   name = "inscription")
    */
    public function inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder, \Swift_Mailer $mailer)
    {
        //liaison avec la table des utilisateur
        $user = new User();
        //creation du formulaire UserType
        $form = $this->createForm(UserType::class, $user);
        
        //recuperation des données du formulaire
        //Traitemnt du formulaire
            //recuperation des données (on "attrape" ->handle)
            $form->handleRequest($request);
        
            //si formulaire soumis et validé, je le lie à ma BDD pour une insertion
            if($form->isSubmitted() && $form->isValid())
            {
                //encodage du mot de passe
                $hash = $passwordEncoder->encodePassword($user, $user->getPassword());//equivalent passwordhash, soit mon mdp crypté
                $user->setPassword($hash);
                
                //connexion BDD + enregistrement
                $em = $this->getDoctrine()->getManager();
                $em->persist($user); //prepare()
                $em->flush(); //execute()
                
                //envoi d'un mail
                $message = (new \Swift_Message('Bienvenue'))
                        ->setFrom('wf3nimes@gmail.com')
                        ->setTo($user->getEmail())
                        ->setBody
                    ($this->renderView('mail/confirmation.html.twig', array('name' => $user->getUsername() ) ), 'text/html');
                $mailer->send($message);
                
                //retour de l'utilisateur sur la page Index (comme un location en PHP)
                return $this->redirectToRoute('index');
            }
        //affichage du formulaire
        return $this->render('security/inscription.html.twig', array('form' => $form->createView(), 'title' =>'inscription'));
    }
    
    //connexion d'un utilisateur
    //la route est dans le fichier routes.yml
    public function login(Request $request, AuthenticationUtils $authUtils)
    {
        $error = $authUtils->getLastAuthenticationError();
        
        //dernier user name saisi
        $lastUsername = $authUtils->getLastUsername();
        
        //affichage du formulaire
        return $this->render('security/login.html.twig', array('last_username' => $lastUsername, 'error' =>$error, 'title' =>'login')); //toute la security se fait par security.yaml, avec form_login: login_path: login check_path: login
    }
    
     /**
    * @Route(
    *   "/admin",
    *   name = "admin")
    */
    
    public function admin()
    {
        /*return new Response('Page admin');*/
        return $this->render('security/admin.html.twig', array('title' => 'Admin'));
    }
    
    /**
    * @Route(
    *   "/user",
    *   name = "user")
    */
    
    public function user()
    {
        //restriction si ROLE_USER (ou ROLE_ADMIN)
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'Merci de vous connectez pour avoir accès à cette partie du site.');
        return new Response('Page user');
    }
    
    /**
    * @Route(
    *   "/user2",
    *   name = "user2")
    */
    
    public function user2(AuthorizationCheckerInterface $authChecker)
    {
        if($authChecker->isGranted('ROLE_ADMIN'))
        {
            $profil = 'admin';
        } elseif ($authChecker->isGranted('ROLE_USER')){
            $profil = 'user';
        } else {
            $profil = 'anonymous';
        }
        
        return new Response('Vous êtes ' . $profil);
    }
}