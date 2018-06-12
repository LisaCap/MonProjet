<?php
namespace App\Entity;
/* Objet User => représentation de la table User dans la BDD */
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**  
    * @ORM\Column(type="string", length=25, unique=true) 
    */
    private $username;	
    /**
    * @ORM\Column(type="string", length=60, unique=true) 
    */
    private $email;
    /**
    * @ORM\Column(type="string", length=64) 
    */
    private $password;
    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;
    /**
    * @ORM\Column(type="array")
    */
    private $roles;

    // constructeur. Définit le statut actif et ajoute le role ADMIN automatiquement
    public function __construct()
    {
    	$this->isActive = true;
    	$this->roles[] = 'ROLE_ADMIN';
    }
    //récupération de l'adresse mail
    public function getEmail()
    {
    	return $this->email;
    }
    //récupération du nom d'utilisateur
    public function getUsername()
    {
    	return $this->username;
    }
    //récupération du mot de passe (hashé)
    public function getPassword()
    {
    	return $this->password;
    }
    //récupération des rôles
    public function getRoles()
    {
    	return $this->roles; //return ['ROLE_USER'];
    }
    //récupération du "salt" (null car bcript le fait automatiquement)
    public function getSalt()
    {
    	return null;
    }
    //fonction imposée par l'interface user
    public function eraseCredentials()
    {
    }
    //sérialisation de l'utilisateur (pour stocker en session)
    public function serialize()
    {
    	return serialize(array(
    		$this->id,
    		$this->username,
    		$this->password,
    		$this->isActive
    	));
    }
    //dé-sérialisation de l'objet (session)
    public function unserialize($serialized)
    {
    	list($this->id, 
    			 $this->username,
    			 $this->password,
    			 $this->isActive)
    	= unserialize($serialized);
    }
    //définition de l'email
    public function setEmail($val)
    {
    	$this->email = $val;
    }
    //définition du nom d'utilisateur
	public function setUsername($val)
    {
    	$this->username = $val;
    }
    //définition du mot de passe
    public function setPassword($val)
    {
    	$this->password = $val;
    }
    //ajout d'un role
    public function setRoles($val)
    {
    	return $this->roles[] = $val;
    }
    //compte non expiré?
    public function isAccountNonExpired()
    {
        return true;
    }
    //Compte non vérouillé
    public function isAccountNonLocked()
    {
        return true;
    }
    //  identifiants non expirés
    public function isCredentialsNonExpired()
    {
        return true;
    }
    // est activé
    public function isEnabled()
    {
        return $this->isActive;
    }

    /*
    * Ne pas oublier les commandes 
    * - php bin/console make:entity nom_de_l_entite pour créer une entité (classe vide + repository pret à l'emploi)
    * - php bin/console doctrine:migrations:diff pour générer une classe contenant le code d'ajout des tables manquantes
    * - php bin/console doctrine:migrations:migrate pour exécuter le code de migration
    * - php bin/console doctrine:schema:update --force pour mettre à jour la BDD après ajout/modification de champs
    */
}
