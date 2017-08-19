<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Request\ParamFetcher;

use AppBundle\Security\FrontendAuthenticator;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\Workshop;
use AppBundle\Entity\Stats;
use AppBundle\Entity\User;

/**
 * REST controller for registering as `User` to a workshop.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class RegistrationController extends ApiController
{
    /** @var $repository */
    protected $repository = User::class;

    private $frontendAuthenticator;
    private $guardAuthenticationHandler;
    public function __construct(
        ValidatorInterface $validator, 
        FrontendAuthenticator $frontendAuthenticator,
        GuardAuthenticatorHandler $guardAuthenticationHandler)
    {
        parent::__construct($validator);

        $this->frontendAuthenticator = $frontendAuthenticator;
        $this->guardAuthenticationHandler = $guardAuthenticationHandler;
    }

    /**
     * Registers a new `User` to given instance of `Workshop` by code.
     *
     * @Post("/api/auth/frontend/signup")
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @RequestParam(name="_username", description="Username for registration.")
     * @RequestParam(name="_password", description="Workshop code for registration.")
     *
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function registerAction(Request $request, ParamFetcher $paramFetcher)
    {
        $username = $paramFetcher->get('_username'); // = unique user `username`
        $password = $paramFetcher->get('_password'); // = unique workshop `code`

        // check if workshop already exists
        $workshopRepository = $this->getRepository(Workshop::Class);
        $workshop = $workshopRepository->findOneByCode($password);
        if ($workshop === null) {
            $this->apiException(
                'Workshop doesn\'t exist!',
                Response::HTTP_PRECONDITION_FAILED
            );
        }

        // check if the workshop has `isActive`
        if (!$workshop->getIsActive()) {
            $this->apiException(
                'Workshop is currently locked!',
                Response::HTTP_PRECONDITION_FAILED
            );
        }

        // check if the user already exists, otherwise create one
        $userRepository = $this->getRepository();
        $user = $userRepository
            ->findOneByUsername($username);

        if ($user === null) {
            $user = new User();

            $stats = new Stats();
            $stats->setUser($user);

            $user->setStats($stats);
            $user->setUsername($username);
            $user->setWorkshop($workshop);
            $this->validateEntity($user);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($user);
            $entityManager->flush();
        } else {
            $workshopId = $user->getWorkshop()->getId();
            if ($workshopId !== $workshop->getId()) {
                $this->apiException(
                    'Username already registered to another workshop!',
                    Response::HTTP_PRECONDITION_FAILED
                );
            }
        }

        // authenticate user with our guard for `frontend` firewall
        return $this->guardAuthenticationHandler
            ->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $this->frontendAuthenticator,
                'frontend'
            );
    }

    /**
     * Get workshop by `code` property.
     * 
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Get("/api/auth/validate/workshop/{code}")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function validateWorkshopAction(Workshop $workshop)
    {
        return $workshop;
    }
}