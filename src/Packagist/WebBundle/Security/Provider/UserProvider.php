<?php

/*
 * This file is part of Packagist.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *     Nils Adermann <naderman@naderman.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Packagist\WebBundle\Security\Provider;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Packagist\WebBundle\Entity\UserConnectedAccount;
use Packagist\WebBundle\Entity\UserRepository;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param UserManagerInterface $userManager
     * @param UserRepository $userRepository
     */
    public function __construct(UserManagerInterface $userManager, UserRepository $userRepository)
    {
        $this->userManager = $userManager;
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function connect($user, UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();
        $username = $response->getUsername();
        $previousUser = $this->userRepository->findByOauthProviderAndUsername($resourceOwnerName, $username);


        // The account is already connected. Do nothing
        if ($previousUser === $user) {
            return;
        }

        // 'disconnect' a previous account
        if (null !== $previousUser) {
            $previousUser->setGithubId(null);
            $previousUser->setGithubToken(null);
            $this->userManager->updateUser($previousUser);
        }

        $this->userManager->updateUser($user);

        $account = new UserConnectedAccount();
        $account->setProvider($resourceOwnerName);
        $account->setToken($response->getAccessToken());
        $account->setUsername($username);
        $account->setUser($user);

        $user->addAccounts($account);

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $resourceOwnerName = $response->getResourceOwner()->getName();
        $username = $response->getUsername();
        $user = $this->userRepository->findByOauthProviderAndUsername($resourceOwnerName, $username);

        if (!$user) {
            throw new AccountNotLinkedException(sprintf('No user with github username "%s" was found.', $username));
        }

        if (!$user->getAccounts()->first()->getToken()) {
            $user->setGithubToken($response->getAccessToken());
            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByUsername($usernameOrEmail)
    {
        $user = $this->userManager->findUserByUsernameOrEmail($usernameOrEmail);

        if (!$user) {
            throw new UsernameNotFoundException(sprintf('No user with name or email "%s" was found.', $usernameOrEmail));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        return $this->userManager->refreshUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $this->userManager->supportsClass($class);
    }
}
