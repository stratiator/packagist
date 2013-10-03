<?php
/**
 * @author strati <strati@strati.hu>
 */
 

namespace Packagist\WebBundle\Model;


use Doctrine\ORM\EntityManager;
use Packagist\WebBundle\Entity\User;
use Packagist\WebBundle\Entity\UserConnectedAccount;

class UserConnectedAccountManager
{
	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @param User   $user
	 * @param string $provider
	 */
	public function deleteConnectedAccountFromUser(User $user, $provider)
	{
		$account = $this->getUserAccountOfProvider($user, $provider);

		if ($account->getProvider()) {
			$this->em->remove($account);
			$this->em->flush();
		}
	}

	/**
	 * @param User   $user
	 * @param string $provider
	 *
	 * @return UserConnectedAccount
	 */
	public function getUserAccountOfProvider(User $user, $provider)
	{
		$account = $user->getAccounts()->filter(
			function(UserConnectedAccount $account) use($provider) {
				return $account->getProvider() == $provider;
			}
		)->first();

		if (empty($account)) {
			$account = new UserConnectedAccount();
		}

		return $account;
	}

	/**
	 * @param User   $user
	 * @param string $provider
	 *
	 * @return bool
	 */
	public function isUserConnectedToProvider(User $user, $provider)
	{
		return (bool)$this->getUserAccountOfProvider($user, $provider)->getProvider();
	}

	/**
	 * @param UserConnectedAccount $account
	 */
	public function saveAccount(UserConnectedAccount $account)
	{
		$this->em->persist($account);
		$this->em->flush();
	}
}