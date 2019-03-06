<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 10.04.18
 * Time: 11:31
 */

namespace AppBundle\DataFixture\ORM;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements ORMFixtureInterface, ContainerAwareInterface
{
    private $container;

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $userManager = $this->container->get('fos_user.user_manager');

        // Create our user and set details
        $user = $userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail('admin@email.com');
        $user->setPlainPassword('1234');
        $user->setPassword('1234');
        $user->setEnabled(true);
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setName('Андрей');

        // Update the user
        $userManager->updateUser($user, true);
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
