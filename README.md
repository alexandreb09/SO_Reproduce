Reproduce:
  - Download project
  - run `Composer update` inside root folder
  - Update parameters in `app\config\parameters.yml` file
  - Create an empty DataBase (I use MySQL v5.7.14 with PhpMyAdmin)
  - Set up database schema with `php bin/console doctrine:schema:update --force` 
  - open `tests\AppBundle\Validator\UserTest.php`
  - run `SampleTest` test

Note:
  - The captcha secret key isn't provided here (in `AppBundle\Security\LoginFormAuthentifictor.php`)

Actual error:
 > **Error : Call to a member function getUser() on null**

Trace:
> C:\wamp64\www\SOProject\src\AppBundle\Validator\ExampleValidator.php:25
  C:\wamp64\www\SOProject\var\cache\test\ContainerHqdaxfj\getUser_ExampleValidatorService.php:8
  C:\wamp64\www\SOProject\var\cache\test\ContainerHqdaxfj\appTestDebugProjectContainer.php:1094
  C:\wamp64\www\SOProject\var\cache\test\ContainerHqdaxfj\appTestDebugProjectContainer.php:2931
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\DependencyInjection\ServiceLocator.php:64
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\ContainerConstraintValidatorFactory.php:46
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\RecursiveContextualValidator.php:799
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\RecursiveContextualValidator.php:518
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\RecursiveContextualValidator.php:329
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\RecursiveContextualValidator.php:140
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\RecursiveValidator.php:100
  C:\wamp64\www\SOProject\vendor\symfony\symfony\src\Symfony\Component\Validator\Validator\TraceableValidator.php:65
  C:\wamp64\www\SOProject\tests\AppBundle\Command\UserTest.php:94


**Thank you for your help.**
