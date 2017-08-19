<?php

namespace AppBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;

/**
 * Authenticator for `frontend` firewall and `User` entity.
 * Checks custom credentials and generates the JWT tokens.
 *
 * @subpackage Security
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class FrontendAuthenticator extends AbstractGuardAuthenticator
{
    private $entityManager;
    private $jwtSuccessHandler;
    private $jwtFailureHandler;
    public function __construct(
        ObjectManager $entityManager, 
        AuthenticationSuccessHandler $jwtSuccessHandler,
        AuthenticationFailureHandler $jwtFailureHandler)
    {
        $this->entityManager = $entityManager;
        $this->jwtSuccessHandler = $jwtSuccessHandler;
        $this->jwtFailureHandler = $jwtFailureHandler;
    }

    /**
     * Invoked on every request. Return value will be passed to getUser().
     * If returning null, this authenticator will be entirely skipped.
     * 
     * @param Request $request
     * @return array|null
     */
    public function getCredentials(Request $request)
    {
        return [
            'username' => $request->request->get('_username')
        ];
    }

    /**
     * Tries to load `User` entity by given `username` property from credentials.
     * If user's not found it will fail, otherwise checkCredentials() gets called.
     * 
     * @param array $credentials
     * @param UserProviderInterface $userProvider
     * @throws AuthenticationException
     * @return User|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials['username'];
        if ($username === null) {
            throw new AuthenticationException('No code has been submitted!');
        }

        try {
            return $userProvider->loadUserByUsername($username);
        } catch(\Exception $e) {
            throw new AuthenticationException('This code is invalid!');
        }
    }

    /**
     * Checks if associated `Workshop` has falsy `isActive` flag.
     * If not, the authentication will fail with a 401 response!
     * 
     * @param array $credentials
     * @param UserInterface $user
     * @throws AuthenticationException
     * @return boolean
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user->getWorkshop()->getIsActive()) {
            return true;
        }
        
        throw new AuthenticationException('Workshop is still active!');
    }

    /**
     * Creates JWT token from user object and returns JSON response.
     * 
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JSONResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {   
        return $this->jwtSuccessHandler->onAuthenticationSuccess($request, $token);
    }

    /**
     * Returns error message from AuthenticationException as JSON response.
     * 
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JSONResponse
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->jwtFailureHandler->onAuthenticationFailure($request, $exception);
    }

    /**
     * Returns error message if authorization's required, but acctually not sent.
     * 
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return JSONResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            'code' => 401,
            'message' => 'You need to sign in for gaining access!',
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Authentication doesn't support remember me.
     * 
     * @return boolean
     */
    public function supportsRememberMe()
    {
        return false;
    }
}