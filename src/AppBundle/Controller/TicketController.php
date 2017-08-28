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
use AppBundle\Entity\Workshop;
use AppBundle\Entity\Ticket;

/**
 * REST controller for ticket entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class TicketController extends ApiController
{
    /** @var $repository */
    protected $repository = Ticket::class;

    /**
     * Find ticket by id.
     *
     * @View()
     * @Get("/api/ticket/{id}")
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
     * Finds all tickets.
     *
     * @View()
     * @Get("/api/ticket")
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
     * Creates a ticket in database.
     *
     * @View()
     * @Post("/api/ticket")
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
     * Patches a ticket in database.
     *
     * @View()
     * @Patch("/api/ticket/{id}")
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
     * Deletes a ticket from database.
     *
     * @View()
     * @Delete("/api/ticket/{id}")
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

    /**
     * Fetches all tickets from given workshop.
     *
     * @View()
     * @Get("/api/ticket/workshop/{id}")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getTicketsByWorkshopAction(Workshop $workshop)
    {
        return $this->getRepository()->getTicketsByWorkshop($workshop);
    }
}