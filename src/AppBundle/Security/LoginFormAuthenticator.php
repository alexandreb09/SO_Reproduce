<?php

namespace AppBundle\Security;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator{
    use TargetPathTrait;

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;
    private $loginAttemptRepository;


    public function __construct(EntityManagerInterface $entityManager,
                                UrlGeneratorInterface $urlGenerator,
                                CsrfTokenManagerInterface $csrfTokenManager,
                                UserPasswordEncoderInterface $passwordEncoder){
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request){
        return $request->getPathInfo() == '/login_check' &&
            $request->isMethod('POST') &&
            $request->request->get('_password') !== null;
    }



    public function getCredentials(Request $request){
        $isLoginSubmit = $request->getPathInfo() == '/login_check' &&
            $request->isMethod('POST') &&
            $request->request->get('_password') !== null;
        $isCaptcha = $request->request->get('captcha_set');

        if ($isCaptcha == 1 && $request->request->get('_password') !== null) {
            // This block is used to check captcha
            $secret = "";
            if($_POST['g-recaptcha-response'] !== null){
                $response = $_POST['g-recaptcha-response'];
                $remoteip = $_SERVER['REMOTE_ADDR'];

                $api_url = "https://www.google.com/recaptcha/api/siteverify?secret="
                    . $secret
                    . "&response=" . $response
                    . "&remoteip=" . $remoteip ;

                $decode = json_decode(file_get_contents($api_url), true);

                if ($decode['success'] == true) {
                    $username = $request->request->get('_username');
                    $password = $request->request->get('_password');
                    $csrfToken = $request->request->get('_csrf_token');

                    if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
                        throw new InvalidCsrfTokenException('Invalid CSRF token.');
                    }

                    $request->getSession()->set(
                        Security::LAST_USERNAME,
                        $username
                    );

                    return [
                        'username' => $username,
                        'password' => $password,
                    ];
                }
                else{
                    throw new CustomUserMessageAuthenticationException('Captcha invalide.');
                }
            }
            else{
                throw new CustomUserMessageAuthenticationException('Captcha invalide.');
            }
        }
        else {
            if (!$isLoginSubmit) {
                // skip authentication
                return;
            }

            $username = $request->request->get('_username');
            $password = $request->request->get('_password');
            $csrfToken = $request->request->get('_csrf_token');

            if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $csrfToken))) {
                throw new InvalidCsrfTokenException('Invalid CSRF token.');
            }

            $request->getSession()->set(
                Security::LAST_USERNAME,
                $username
            );

            return [
                'username' => $username,
                'password' => $password,
            ];
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider){
        $username = $credentials["username"];
        $user = $this->entityManager->getRepository('AppBundle:User')->findOneBy(['username' => $username]);
        return $user;
    }


    public function checkCredentials($credentials, UserInterface $user){
        $password = $credentials["password"];
        $rep = false;
        if ($this->passwordEncoder->isPasswordValid($user, $password)){
            $rep = true;
        }
        return $rep;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey){
        $targetPath = null;
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('homepage'));
    }

    protected function getLoginUrl(){
        return $this->urlGenerator->generate('fos_user_security_login');
    }



}