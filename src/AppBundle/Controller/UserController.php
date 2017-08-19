<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\User;

/**
 * REST controller for user entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class UserController extends ApiController
{
    /** @var $repository */
    protected $repository = User::class;

    /**
     * Gets current user session.
     *
     * @Get("/api/user/current")
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_USER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function currentAction()
    {
        return $this->getUser();
    }

    /**
     * Find user by id.
     *
     * @View()
     * @Get("/api/user/{id}")
     * @Security("has_role('ROLE_USER')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getAction()
    {
       $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Finds all users.
     *
     * @View()
     * @Get("/api/user")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Creates a user in database.
     *
     * @View()
     * @Post("/api/user")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Patches a user in database.
     *
     * @View()
     * @Patch("/api/user/{id}")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction()
    {
        $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Deletes a user from database.
     *
     * @View()
     * @Delete("/api/user/{id}")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction()
    {
        $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }
}