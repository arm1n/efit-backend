<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\Workshop;
use AppBundle\Entity\Result;
use AppBundle\Entity\Ticket;
use AppBundle\Entity\User;
use AppBundle\Entity\Task;

/**
 * REST controller for result entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ResultController extends ApiController
{
    /** @var $repository */
    protected $repository = Result::class;

    /**
     * Find result by id.
     *
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=false
     * )
     * @Get("/api/result/{id}")
     * @Security("has_role('ROLE_USER')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Result $result)
    {
        $this->apiException(
            'This method is not allowed!',
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    /**
     * Finds all results.
     *
     * @View()
     * @Get("/api/result")
     * @Security("has_role('ROLE_USER')")
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
     * Creates a result in database.
     *
     * @Post("/api/result")
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter(
     *     "result", 
     *     converter="fos_rest.request_body",
     *     options={
     *         "deserializationContext"={
     *             "serializeNull"=false,
     *             "groups"={"deserialize"}
     *         }
     *     }
     * )
     *
     * @param AppBundle\Entity\Result $result
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Result $result, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);
        $this->_assertWrite($result);

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $connection->beginTransaction();
        try {
            // link result relationships 
            $user = $this->getUser();
            $user->addResult($result);
            $result->setUser($user);

            // immediate flush for `Stats`!
            $entityManager->flush();

            // try to update `Stats` table
            $this->_updateStats($result);

            // write changes to database
            $connection->commit();
        } catch (Exception $exception) {
            $connection->rollBack();
            
            $this->apiException(
                'Could not save result!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $result; 
    }

    /**
     * Patches a result in database.
     *
     * @View(
     *     serializerGroups={"frontend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Patch("/api/result/{id}")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter(
     *     "result", 
     *     converter="fos_rest.request_body", 
     *     options={
     *         "deserializationContext"={
     *             "serializeNull"=false,
     *             "groups"={"deserialize"}
     *         }
     *     }
     * )
     *
     * @param AppBundle\Entity\Result $result
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Result $result, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);
        $this->_assertWrite($result);

        $entityManager = $this->getEntityManager();
        $connection = $entityManager->getConnection();

        $connection->beginTransaction();
        try {
            // immediate flush for `Stats`!
            $entityManager->flush();

            // try to update `Stats` table
            $this->_updateStats($result);

            // write changes to database
            $connection->commit();
        } catch (Exception $exception) {
            $connection->rollBack();
            
            $this->apiException(
                'Could not save result!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return $result;
    }

    /**
     * Deletes a result from database.
     *
     * @View()
     * @Delete("/api/result/{id}")
     * @Security("has_role('ROLE_USER')")
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
     * Fetches all results from provided task.
     *
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=false
     * )
     * @Get("/api/result/task/{id}")
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getResultsByTaskAction(Task $task)
    {
        return $task->getResults();
    }

    /**
     * Asserts that user can perform write operation on results.
     * 
     * @param AppBundle\Entity\Result $result
     *
     * @throws AppBundle\Exception\ApiException
     */
    private function _assertWrite(Result $result)
    {
        // only real users from entity `User` are allowed
        // this prevents `Admin` entities to post results
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->apiException(
                'Only users can post results!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // assert that task is not locked currenlty (SSE):
        // state of transmitted task from JSON and current
        // database state can be different due to timing,
        // so we have to fetch task again to make a check
        $repository = $this->getRepository(Task::class);
        $taskId = $result->getTask()->getId();
        $task = $repository->find($taskId);

        if ($task === null) {
            $this->apiException(
                'No task found for this result!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (!$task->getIsActive()) {
            $this->apiException(
                'Task for result is currently locked!',
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        // assert this is first result DURING workshop
        // this prevents possibility that `User` posts
        // a second result during workshop, cause they
        // should post further task results from remote
        if ($task->getWorkshop()->getIsActive()) {
            $repository = $this->getRepository();
            if ($repository->hasResultByTaskAndUser($task,$user)) {
                $this->apiException(
                    'Only one result permitted during workshop!',
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        }
    }

    /**
     * Updates `Stats` Entity for current user when
     * positing a new `Result` from played `Task`.
     * 
     * @param \AppBundle\Entity\Result
     * @return void
     */
    private function _updateStats(Result $result)
    {        
        $entityManager = $this->getEntityManager();
        $repository = $this->getRepository();

        // no operation for `isPending`
        if ($result->getIsPending()) {
            return;
        }
        
        $user = $this->getUser();
        $stats = $user->getStats();
        $task = $result->getTask();
        $workshop = $task->getWorkshop();
        
        
        //
        // TASKS
        //

        // update `tasks` property of `Stats` entity
        
        $tasks = $stats->getTasks();
        $type = $task->getType();

        // first time = add a new ticket for `User`
        if (!array_key_exists($type, $tasks)) {
            $count = $result->getTicketCount();
            for ($i = 0; $i<$count; $i++) {
                $this->_addTicket();
            }

            $tasks[$type] = 0;
        }

        // increment the `counts` property afterwards
        $tasks[$type] = $tasks[$type] + 1;
        $stats->setTasks($tasks);

        //
        // BLOCKS
        //

        // update `blocks` property of `Stats` entity
        $blocks = $stats->getBlocks();
        $block = $task->getBlock();

        // create a new entry for unknown `block`
        if (!array_key_exists($block, $blocks)) {
            $blocks[$block] = 0;
        }

        // get open types for given `block` and with current flag
        // `inBlock` set to false -> if `type` of result tasks is
        // the same as specified in `Task` entitity the round has
        // been finished and these tasks need to be updated with
        // the `inBlock` flag set to true for ignoring next time
        $filterTypes = function($type) use ($workshop) {
            // drop tasks which are per se not countable
            // (f.e. if a task has no result to submit!)
            if (!Task::getIsInteractiveByType($type)) {
                return false;
            }

            // if workshop is currently running, accept
            // all exercises which should not be skipped
            if ($workshop->getIsActive()) {
                return true;
            }

            // ignore workshop tasks when playing remote
            return !Task::getIsWorkshopOnlyByType($type);
        };

        // collect `openTypes` array while filtering results,
        // which we need as well to update `inBlock` property
        $openTypes = []; $openResults = array_filter(
            $repository->getOpenResultsByTaskAndUser($task, $user),
            function($result) use ($filterTypes, &$openTypes) {
                $type = $result->getTask()->getType();
                $openType = $filterTypes($type);
                if ($openType === false) {
                    return false;
                }

                $openTypes[] = $type;
                return true;
            }
        );
        
        $allTypes = Task::getTasksByBlock($block);
        $allTypes = array_filter($allTypes, $filterTypes);

        // this is needed for strict comparison check
        sort($allTypes);
        sort($openTypes);
        if ($openTypes === $allTypes) {
            foreach($openResults as $countedResult) {
                $countedResult->setInBlock(true);
            }

            // increment state of `User` if `block`
            // has been finished for the first time
            $blocks[$block] = $blocks[$block] + 1;
            if ($blocks[$block] === 1) {
                $this->_addState();
            }
        }

        $stats->setBlocks($blocks);

        //
        // ROUNDS
        //
        
        // update `rounds` property of `Stats` entity
        $allBlocks = Task::getBlocks();
        $rounds = $stats->getRounds();

        // current `blocks` from `Stats` need the same
        // size as all blocks defined in `Task` entity
        // if so, the minimum of `blocks` reflects the
        // the current amount of played rounds by user
        $sizeAll = count($allBlocks);
        $sizeBlocks = count($blocks);

        if ($sizeAll === $sizeBlocks) {
            // no update if still same round
            $newRounds = min($blocks);
            if ($rounds !== $newRounds) {
                $stats->setRounds($newRounds);
            }
        }

        // write all changes
        $entityManager->flush();
    }

    /**
     * Adds new `Ticket` to current `User`.
     * 
     * @return void
     */
    private function _addTicket()
    {
        $user = $this->getUser();

        $ticket = new Ticket();
        $ticket->setUser($user);
        $user->addTicket($ticket);
    }

    /**
     * Tries to increase `state` of `User` to
     * next level if that's still possible.
     * 
     * @return void
     */
    private function _addState()
    {
        $user = $this->getUser();
        $state = $user->getState();
        $states = User::getStates();

        reset($states);
        while ($value = current($states)) {
            if ($value === $state) {
                break;
            }

            next($states);
        }

        $next = next($states);
        if ($next === false) {
            return;
        }

        $user->setState($next);
    }


}