<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use AppBundle\Repository\AdminRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Entity\Admin;
use AppBundle\Entity\User;

/**
 * Custom user provider for JWT refresh bundle (gesdinet_jwt_refresh_token).
 * It compromises the fact that we use both `User` and `Admin` entitites for
 * authentication and need to provide correct payload when refreshing token.
 * Please see bundle docs: https://github.com/gesdinet/JWTRefreshTokenBundle
 *
 * @subpackage Security
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class JWTRefreshUserProvider implements UserProviderInterface
{
	private $userRepository;
	private $adminRepository;
	public function __construct(AdminRepository $adminRepository, UserRepository $userRepository)
	{
		$this->adminRepository = $adminRepository;
		$this->userRepository = $userRepository;
	}

	/**
	 * Tries to load a `User` OR `Admin` entity from database.
	 *
	 * @param  string $username
	 * @throws Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 * @return Symfony\Component\Security\Core\User\UserInterface
	 */
    public function loadUserByUsername($username)
    {
    	$user = $this->userRepository->findOneByUsername($username);
    	if ($user !== null) {
    		return $user;
    	}

    	$admin = $this->adminRepository->findOneByUsername($username);
		if ($admin !== null) {
			return $admin;
		}

		$message = sprintf('Username "%s" does not exist.', $username);
		throw new UsernameNotFoundException($message);
    }

    /**
	 * Tries to refresh `User` OR `Admin` entity from database.
	 *
	 * @param  string $username
	 * @throws Symfony\Component\Security\Core\Exception\UnsupportedUserException
	 * @return Symfony\Component\Security\Core\User\UserInterface
	 */
    public function refreshUser(UserInterface $user)
    {
    	switch (true) {
    		case $user instanceof User:
    		case $user instanceof Admin:
    			return $this->loadUserByUsername($user->getUsername());
    		default:
    			$message = sprintf('Instances of "%s" are not supported.', get_class($user));
            	throw new UnsupportedUserException($message);
    	}
    }

    /**
	 * Checks if class is a `User` OR `Admin` entity.
	 *
	 * @param  string $username
	 * @return bool
	 */
    public function supportsClass($class)
    {
    	switch ($class) {
    		case User::class:
    		case Admin::class:
    			return true;
    		default:
    			return false;
    	}
    }
}