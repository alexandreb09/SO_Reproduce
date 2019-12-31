<?php

namespace AppBundle\Validator;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ExampleValidator extends ConstraintValidator{
    protected $requestStack;
    protected $em;
    protected $user_id;

    public function __construct(RequestStack $request,
                                EntityManager $em,
                                TokenStorage $tokenStorage){
        $this->requestStack = $request;
        $this->em = $em;

        /** @var User $user */
        $user = $tokenStorage->getToken()->getUser();
        $this->user_id = $user != "anon." ? $user->getId() : null;
    }

    /**
     * @param $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        // some validation rules ...
    }
}