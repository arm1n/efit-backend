<?php

namespace AppBundle\Controller;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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
use AppBundle\Entity\Admin;

/**
 * REST controller for admin entity.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class AdminController extends ApiController
{
    /** @var $repository */
    protected $repository = Admin::class;

    private $userPasswordEncoder;
    public function __construct(
        ValidatorInterface $validator, 
        UserPasswordEncoderInterface $userPasswordEncoder)
    {
        parent::__construct($validator);

        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * Find admin by id.
     *
     * @View()
     * @Get("/api/admin/{id}")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Admin $admin)
    {
       return $admin;
    }

    /**
     * Finds all admins.
     *
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Get("/api/admin")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $builder = $this->getRepository()->createQueryBuilder('a');
        
        $builder
            ->where('a.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_ADMIN"%');

        return $builder->getQuery()->getResult();
    }

    /**
     * Creates an admin in database.
     *
     * @Post("/api/admin")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @ParamConverter("admin", converter="fos_rest.request_body")
     *
     * @param AppBundle\Entity\Admin $admin
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @throws AppBundle\Exception\ApiException
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Admin $admin, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);

        $this->_encodePassword($admin);
        $admin->setRoles(['ROLE_ADMIN']);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        return $admin;
    }

    /**
     * Patches an admin in database.
     * 
     * @Patch("/api/admin/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @ParamConverter(
     *     "admin", 
     *     converter="fos_rest.request_body", 
     *     options={
     *         "deserializationContext"={
     *             "serializeNull"=false,
     *             "groups"={"deserialize"}
     *         }
     *     }
     * )
     *
     * @param AppBundle\Entity\Admin $admin
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Admin $admin, ConstraintViolationListInterface $validation)
    {
        $this->validateData($validation);
        $this->_encodePassword($admin);

        $entityManager = $this->getEntityManager();
        $entityManager->flush();

        return $admin;
    }

    /**
     * Deletes an admin from database.
     * 
     * @Delete("/api/admin/{id}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     * @ParamConverter("admin", class="AppBundle:Admin")
     *
     * @param AppBundle\Entity\Admin $admin
     * @param Symfony\Component\Validator\ConstraintViolationListInterface $validation
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Admin $admin)
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($admin);
        $entityManager->flush();
    }

    /**
     * Get admin by `username` property.
     *
     * @Get("/api/admin/username/{username}")
     * @View(
     *     serializerGroups={"backend"},
     *     serializerEnableMaxDepthChecks=true
     * )
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     *
     * @param AppBundle\Entity\Admin $admin
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function getByUsernameAction(Admin $admin)
    {
        return $admin;
    }

    /**
     * Swaps `password` property with encoded one and 
     * sets plain text password as `plainPassword`.
     * 
     * @param AppBundle\Entity\Admin $admin
     * @return void
     */
    private function _encodePassword(Admin $admin)
    {
        $plainPassword = $admin->getPassword();
        $encodedPassword = $this->userPasswordEncoder
            ->encodePassword($admin, $plainPassword);

        $admin->setPlainPassword($plainPassword);
        $admin->setPassword($encodedPassword);
    }
}