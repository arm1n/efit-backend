<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\Result;
use AppBundle\Entity\Task;

/**
 * REST controller for task entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class TaskController extends ApiController
{
    /** @var $repository */
    protected $repository = Task::class;

    /**
     * Find task by id.
     *
     * @View()
     * @Get("/api/task/{id}")
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
     * Finds all tasks.
     *
     * @View()
     * @Get("/api/task")
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
     * Creates a task in database.
     *
     * @View()
     * @Post("/api/task")
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
     * Patches a task in database.
     * 
     * @Patch("/api/task/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("is_granted('edit', task)")
     * @ParamConverter(
     *     "task", 
     *     converter="fos_rest.request_body", 
     *     options={
     *         "deserializationContext"={
     *             "serializeNull"=false,
     *             "groups"={"deserialize"}
     *         }
     *     }
     * )
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Task $task, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);

        $entityManager = $this->getEntityManager();
        $entityManager->flush();

        return $task;
    }

    /**
     * Deletes a task from database.
     *
     * @View()
     * @Delete("/api/task/{id}")
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