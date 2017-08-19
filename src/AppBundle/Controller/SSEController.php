<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\Workshop;
use AppBundle\Entity\Result;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;
use AppBundle\Response\SSE;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\DefaultJWSProvider;

/**
 * REST controller for server sent events.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class SSEController extends ApiController
{
    /*
    private $jwsProvider;
    public function __construct(DefaultJWSProvider $jwsProvider) {
        $this->jwsProvider = $jwsProvider;
    }
    */
   
    /**
     * Provides current tasks `isActive` for workshop's task entities.
     *
     * @View()
     * @Get("/api/sse/workshop/{id}/tasks")
     * @Security("has_role('ROLE_USER')")
     * @QueryParam(name="event", nullable=true, description="Name of SSE event")
     * @QueryParam(name="retry", requirements="\d+", strict=true, default="3", description="Retry time of SSE")
     * @QueryParam(name="sleep", requirements="\d+", strict=true, default="3", description="Sleep time of SSE")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\StreamResponse
     */
    public function getWorkshopTasksAction(Workshop $workshop, ParamFetcher $paramFetcher)
    {
        $repository = $this->getRepository(Task::class);

        $eventName = $paramFetcher->get('event');
        $sleepTime = $paramFetcher->get('sleep');

        $sse = $this->get('AppBundle\Response\SSE');
        $sse->fetchData = function() use ($repository, $workshop){
            $results = $repository->getTaskStatesByWorkshop($workshop);
            return json_encode($results, JSON_NUMERIC_CHECK);
        };
        $sse->sleepTime = intval($sleepTime);
        $sse->eventName = $eventName;

        return $sse->getResponse();
    }
   
    /**
     * Provides current user counts for workshop.
     *
     * @View()
     * @Get("/api/sse/workshop/{id}/users")
     * @Security("has_role('ROLE_ADMIN')")
     * @QueryParam(name="event", nullable=true, description="Name of SSE event")
     * @QueryParam(name="retry", requirements="\d+", strict=true, default="3", description="Retry time of SSE")
     * @QueryParam(name="sleep", requirements="\d+", strict=true, default="3", description="Sleep time of SSE")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\StreamResponse
     */
    public function countWorkshopUsersAction(Workshop $workshop, ParamFetcher $paramFetcher)
    {
        $repository = $this->getRepository(User::class);

        $eventName = $paramFetcher->get('event');
        $sleepTime = $paramFetcher->get('sleep');

        $sse = $this->get('AppBundle\Response\SSE');
        $sse->fetchData = function() use ($repository, $workshop){
            return $repository->getUserCountByWorkshop($workshop);
        };
        $sse->sleepTime = intval($sleepTime);
        $sse->eventName = $eventName;

        return $sse->getResponse();
    }

    /**
     * Provides current result counts for workshop by tasks.
     *
     * @View()
     * @Get("/api/sse/workshop/{id}/results")
     * @Security("has_role('ROLE_ADMIN')")
     * @QueryParam(name="event", nullable=true, description="Name of SSE event")
     * @QueryParam(name="retry", requirements="\d+", strict=true, default="3", description="Retry time of SSE")
     * @QueryParam(name="sleep", requirements="\d+", strict=true, default="3", description="Sleep time of SSE")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\StreamResponse
     */
    public function countWorkshopResultsAction(Workshop $workshop, ParamFetcher $paramFetcher)
    {   
        $repository = $this->getRepository(Result::class);

        $eventName = $paramFetcher->get('event');
        $sleepTime = $paramFetcher->get('sleep');

        $sse = $this->get('AppBundle\Response\SSE');
        $sse->fetchData = function() use ($repository, $workshop) {
            $results = $repository->getResultsByWorkshop($workshop);
            return json_encode($results, JSON_NUMERIC_CHECK);
        };
        $sse->sleepTime = intval($sleepTime);
        $sse->eventName = $eventName;

        return $sse->getResponse();
    }
}