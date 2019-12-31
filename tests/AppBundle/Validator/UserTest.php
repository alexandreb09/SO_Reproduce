<?php

namespace Tests\AppBundle\Validator;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class UserTest extends WebTestCase{

    /** @var  Application $application */
    protected static $application;

    /** @var  Client $client */
    protected $client;

    /** @var  ContainerInterface $container */
    protected $container;

    /** @var  EntityManager $entityManager */
    protected $entityManager;

    protected $validator;

    /**
     * {@inheritDoc}
     * @throws OptimisticLockException
     */
    public function setUp(){
        self::runCommand('doctrine:database:drop --force');
        self::runCommand('doctrine:database:create');
        self::runCommand('doctrine:schema:create');

        $this->client = static::createClient();
        $this->container = $this->client->getContainer();
        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');

        // Set validator
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->validator = $kernel->getContainer()->get('validator');

        // Create one user
        $this->createOneUser();

        parent::setUp();
    }
    protected static function runCommand($command){
        $command = sprintf('%s --quiet', $command);
        try {
            return self::getApplication()->run(new StringInput($command));
        } catch (Exception $e) {
            return null;
        }
    }
    protected static function getApplication(){
        if (null === self::$application) {
            $client = static::createClient();

            self::$application = new Application($client->getKernel());
            self::$application->setAutoExit(false);
        }
        return self::$application;
    }

    /**
     * @throws OptimisticLockException
     */
    public function createOneUser(){
        // Create a random user
        $userOne = new User();
        $userOne->setUsername('user');
        $userOne->setPlainPassword('userpwd');
        $userOne->setEnabled(TRUE);
        $userOne->setEmail('user1@example.com');
        $userOne->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($userOne);
        $this->entityManager->flush();
    }

    private function logIn($firewallName = 'main'){
        // dummy call to bypass the hasPreviousSession check
        $crawler = $this->client->request('GET', '/');
        $session = $this->client->getContainer()->get('session');

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'user1@example.com']);

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new PostAuthenticationGuardToken($user, $firewallName, [new Role('ROLE_CLIENT')]);
        self::$kernel->getContainer()->get('security.token_storage')->setToken($token);

        $session->set('_security_'.$firewallName, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }


    /**
     * @test
     */
    public function SampleTest(){
        $this->logIn();

        $user = new User();
        $this->validator->validate($user);
    }
}
