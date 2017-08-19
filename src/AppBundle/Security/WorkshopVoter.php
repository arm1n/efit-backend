<?php

namespace AppBundle\Security;

use AppBundle\Entity\Admin;
use AppBundle\Entity\Workshop;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class WorkshopVoter extends Voter
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

        if (!$subject instanceof Workshop) {
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
     * Checks if current user is super admin or owner of workshop.
     * 
     * @param  {AppBundle\Entity\Workshop} $workshop
     * @param  {AppBundle\Entity\Admin} $admin
     * @param  {Symfony\Component\Security\Core\Authentication\Token\TokenInterface} $token
     * @return {boolean}
     */
    private function _canEdit(Workshop $workshop, Admin $admin, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        return $admin === $workshop->getOwner();
    }

    /**
     * Checks if current user is super admin or owner of workshop.
     * 
     * @param  {AppBundle\Entity\Workshop} $workshop
     * @param  {AppBundle\Entity\Admin} $admin
     * @param  {Symfony\Component\Security\Core\Authentication\Token\TokenInterface} $token
     * @return {boolean}
     */
    private function _canDelete(Workshop $workshop, Admin $admin, TokenInterface $token)
    {
        if (!$this->_canEdit($workshop, $admin, $token)) {
            return false;
        }

        // workshops with registered users cannot be
        // deleted any more, only before registration
        return $workshop->getUsers()->count() === 0;
    }
}