<?php

namespace AppBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Gesdinet\JWTRefreshTokenBundle\Doctrine\RefreshTokenManager;

/**
 * Custom logout handler to revoke refresh tokens.
 * @Security("has_role('IS_AUTHENTICATED_FULLY')")
 *
 * @subpackage Handler
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $refreshTokenManager;
    public function __construct(RefreshTokenManager $refreshTokenManager){
        $this->refreshTokenManager = $refreshTokenManager;
    }
	
    /** 
     * Cleans invalid refresh tokens from database and returns JSON.
     *  
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function onLogoutSuccess(Request $request)
    {
        $this->refreshTokenManager->revokeAllInvalid(new \DateTime());

        return new JsonResponse(['success' => true]);
    }
}