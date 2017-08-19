<?php

namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use FOS\RestBundle\Controller\FOSRestController;
use AppBundle\Exception\ApiException;

/**
 * Base controller for FOS Rest controllers.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ApiController extends FOSRestController
{
    /** @var $respository Name of repository for this entity. */
    protected $respository = null;

    private $validator;
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /** 
     * Convenience method for accessing entity manager.
     *
     * @return Doctrine\Common\Persistence\ObjectManager
     */
    public function getEntityManager()
    {
        return $this->getDoctrine()->getManager();
    }
	
    /** 
     * Convenience method for accessing given repository.
     *
     * @param string [$respository=null]
     * @return Doctrine\ORM\EntityRepository
     */
    public function getRepository($respository = null)
    {
        if ($respository === null) {
            $respository = $this->repository;
        }

        $entityManager = $this->getDoctrine()->getManager();
        
        return $entityManager->getRepository($respository);
    }

    /**
     * Invokes symfony's validator manually and passes constraints violations to `validateData`.
     * 
     * @param Entity $entity
     * @throws AppBundle\Exception\ApiException Contains validation message and status code 400.
     * @return boolean
     */
    public function validateEntity($entity)
    {
        $constraintVioloationList = $this->validator->validate($entity);

        $this->validateData($constraintVioloationList);
    }

    /**
     * Checks if entity constraints are vialoted and throws first error.
     * 
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     * @throws AppBundle\Exception\ApiException Contains validation message and status code 400.
     * @return boolean
     */
    public function validateData(ConstraintViolationListInterface $constraintVioloationList)
    {
        if ($constraintVioloationList->count()===0) {
            return;
        }

        $message = $constraintVioloationList->get(0)->getMessage();
        $this->apiException($message);
    }

    /**
     * Checks if entity constraints are vialoted and throws first error.
     * 
     * @param string $message
     * @param integer [$status=400]
     * @throws AppBundle\Exception\ApiException Contains validation message and status code.
     * @return boolean
     */
    public function apiException($message, $status = Response::HTTP_BAD_REQUEST)
    {
        throw new ApiException($status, $message);
    }
}