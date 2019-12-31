<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator as UserAssert;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @UserAssert\Example()
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @Assert\Email(
     *     message = "Adresse email invalide"
     * )
     */
    protected $email;


    public function __construct()
    {
        parent::__construct();
        $this->email="";
        $this->setSalt(hash ('sha256', $this->random_str(1024)));
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function random_str($nbr) {
        $str = "";
        $chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSUTVWXYZ0123456789";
        $nb_chars = strlen($chaine);

        for($i=0; $i<$nbr; $i++)
        {
            $str .= $chaine[ rand(0, ($nb_chars-1)) ];
        }

        return $str;
    }
}