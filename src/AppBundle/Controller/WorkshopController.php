<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\ConstraintViolationListInterface;
//use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Get;
//use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\View;
//use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Controller\ApiController;
use AppBundle\Entity\Workshop;

/**
 * REST controller for workshop entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class WorkshopController extends ApiController
{
    /** @var $repository */
    protected $repository = Workshop::class;

    /**
     * Find workshop by id.
     *
     * @Get("/api/workshop/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_USER')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Workshop $workshop)
    {
       return $workshop;
    }

	/**
     * Finds all workshops.
     * 
     * @Get("/api/workshop")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $builder = $this->getRepository()->createQueryBuilder('ws');
        
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $builder
                ->where('ws.owner = :owner')
                ->setParameter('owner', $this->getUser());
        }

        $builder->orderBy('ws.createdAt', 'DESC');

        return $builder->getQuery()->getResult();
    }

    /**
     * Creates a workshop in database.
     * 
     * @Post("/api/workshop")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_ADMIN')")
     * @ParamConverter("workshop", converter="fos_rest.request_body")
     *
     * @param AppBundle\Entity\Workshop $workshop
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Workshop $workshop, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);
        
        $workshop->setOwner($this->getUser());

        $entityManager = $this->getEntityManager();
        $entityManager->persist($workshop);
        $entityManager->flush();

        return $workshop;
    }

    /**
     * Patches a workshop in database.
     * 
     * @Patch("/api/workshop/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("is_granted('edit', workshop)")
     * @ParamConverter(
     *     "workshop", 
     *     converter="fos_rest.request_body", 
     *     options={
     *         "deserializationContext"={
     *             "serializeNull"=false,
     *             "groups"={"deserialize"}
     *         }
     *     }
     * )
     *
     * @param AppBundle\Entity\Workshop $workshop
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Workshop $workshop, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);

        $entityManager = $this->getEntityManager();
        $entityManager->flush();

        return $workshop;
    }

    /**
     * Deletes a workshop from database.
     * 
     * @Delete("/api/workshop/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("is_granted('delete', workshop)")
     * @ParamConverter("workshop", class="AppBundle:Workshop")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Workshop $workshop)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($workshop);
        $entityManager->flush();
    }

    /**
     * Get workshop by `code` property.
     *
     * @Get("/api/workshop/code/{code}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getByCodeAction(Workshop $workshop)
    {
        return $workshop;
    }
}