<?php

namespace AppBundle\Validator;


use Symfony\Component\Validator\Constraint;

/**
 * Class Example
 * @package AppBundle\Validator
 * @Annotation
 */
class Example extends Constraint{

    public function validatedBy(){
        return 'example_valid';
    }

    /**
     * Get class constraints and properties
     * @return array
     */
    public function getTargets(){
        return array(self::CLASS_CONSTRAINT, self::PROPERTY_CONSTRAINT);
    }
}