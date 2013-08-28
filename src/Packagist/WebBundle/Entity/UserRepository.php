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

namespace Packagist\WebBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class UserRepository extends EntityRepository
{
    public function findUsersMissingApiToken()
    {
        $qb = $this->createQueryBuilder('u')
            ->where('u.apiToken IS NULL');
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $provider
     * @param $username
     * @return User
     */
    public function findByOauthProviderAndUsername($provider, $username)
    {
        $qb = $this->createQueryBuilder('u');
        $qb = $qb
            ->addSelect('a')
            ->join('u.accounts', 'a')
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->eq('a.provider', ':provider'),
                    $qb->expr()->eq('a.username', ':username')
                )
            )
            ->setParameter(':provider', $provider)
            ->setParameter(':username', $username);


        return $qb->getQuery()->getOneOrNullResult();
    }
}
