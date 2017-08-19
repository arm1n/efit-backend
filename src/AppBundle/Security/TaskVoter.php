<?php

namespace AppBundle\Security;

use AppBundle\Entity\Task;
use AppBundle\Entity\Admin;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class TaskVoter extends Voter
{
    /** @const EDIT */
    const EDIT = 'edit';

    /** @const DELETE */
    const DELETE = 'delete';

    private $decisionManager;
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * Decides if this voter is supported.
     * 
     * @param  {string} $attribute
     * @param  {mixed} $subject
     * @return {boolean}
     */
    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::DELETE, self::EDIT))) {
            return false;
        }

        if (!$subject instanceof Task) {
            return false;
        }

        return true;
    }

    /**
     * Decides if this voter is supported.
     * 
     * @param  {string} $attribute
     * @param  {mixed} $subject
     * @param  {Symfony\Component\Security\Core\Authentication\Token\TokenInterface} $token
     * @return {boolean}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $admin = $token->getUser();
        if (!$admin instanceof Admin) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->_canEdit($subject, $admin, $token);
            case self::DELETE:
                return $this->_canDelete($subject, $admin, $token);
            default:
                throw new \LogicException('This code should not be reached!');
        }
    }

    /**
     * Checks if current user is super admin or owner of task.
     * 
     * @param  {AppBundle\Entity\Task} $task
     * @param  {AppBundle\Entity\Admin} $admin
     * @param  {Symfony\Component\Security\Core\Authentication\Token\TokenInterface} $token
     * @return {boolean}
     */
    private function _canEdit(Task $task, Admin $admin, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        return $admin === $task->getWorkshop()->getOwner();
    }

    /**
     * Checks if current user is super admin or owner of task.
     * 
     * @param  {AppBundle\Entity\Task} $task
     * @param  {AppBundle\Entity\Admin} $admin
     * @param  {Symfony\Component\Security\Core\Authentication\Token\TokenInterface} $token
     * @return {boolean}
     */
    private function _canDelete(Task $task, Admin $admin, TokenInterface $token)
    {
        return $this->_canEdit($task, $admin, $token);
        return $this->_canEdit($task, $admin, $token);
    }
}