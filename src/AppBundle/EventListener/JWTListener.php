<?php

namespace AppBundle\EventListener;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTFailureEventInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

/**
 * Event listener for JWT tokens.
 *
 * @subpackage EventListener
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class JWTListener
{
	/*
	private $requestStack;
	private $entityManager;
	public function __construct(RequestStack $requestStack, ObjectManager $entityManager) {
		$this->requestStack = $requestStack;
	}
	*/

	/**
	 * Sets custom payload data for JWT success response.
	 * 
	 * @param AuthenticationSuccessEvent $event
	 * @return void
	 */
	public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
	{
		// noop
	}

	/**
	 * Sets custom payload data for JWT failure response.
	 * 
	 * @param AuthenticationFailureEvent $event
	 * @return void
	 */
	public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
	{
		$exception = $event->getException();

		$data = [
			'code' => $exception->getCode(),
			'message' => $exception->getMessage(),
	    ];

	    $event->setResponse(new JsonResponse($data, JsonResponse::HTTP_UNAUTHORIZED));
	}

	/**
	 * Sets custom payload data for JWT created response.
	 * 
	 * @param JWTCreatedEvent $event
	 * @return void
	 */
	public function onJWTCreated(JWTCreatedEvent $event)
	{
		// noop
	}

	/**
	 * Sets custom payload data for JWT invalid response.
	 * 
	 * @param JWTFailureEventInterface $event
	 * @return void
	 */
	public function onJWTInvalid(JWTFailureEventInterface $event)
	{
		$data = [
			'code' => 403,
			'message' => 'The token is invalid!',
	    ];
		
		$event->setResponse(new JsonResponse($data, JsonResponse::HTTP_FORBIDDEN));
	}

	/**
	 * Sets custom payload data for JWT not found response.
	 * 
	 * @param JWTFailureEventInterface $event
	 * @return void
	 */
	public function onJWTNotFound(JWTFailureEventInterface $event)
	{
		$data = [
			'code' => 403,
			'message' => 'The token was not found!',
	    ];
		
		$event->setResponse(new JsonResponse($data, JsonResponse::HTTP_FORBIDDEN));
	}

	/**
	 * Sets custom payload data for JWT expired response.
	 * 
	 * @param JWTFailureEventInterface $event
	 * @return void
	 */
	public function onJWTExpired(JWTFailureEventInterface $event)
	{
		$data = [
			'code' => 403,
			'message' => 'The token has expired!',
	    ];
		
		$event->setResponse(new JsonResponse($data, JsonResponse::HTTP_FORBIDDEN));
	}
}