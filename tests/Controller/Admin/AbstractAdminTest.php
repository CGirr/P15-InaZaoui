<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractAdminTest extends WebTestCase
{
    protected ?KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * @return void
     */
    protected function loginAsAdmin(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $admin = $em->getRepository(User::class)->findOneBy(['email' => 'ina@zaoui.com']);
        $this->client->loginUser($admin);
    }

    /**
     * @return void
     */
    protected function loginAsUser(): void
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        $user = $em->getRepository(User::class)->findOneBy(['email' => 'isy@isy.com']);
        $this->client->loginUser($user);
    }

    /**
     * @param string $class
     * @param array $criteria
     * @return object
     */
    protected function getEntity(string $class, array $criteria): object
    {
        $em = static::getContainer()->get('doctrine.orm.entity_manager');
        return $em->getRepository($class)->findOneBy($criteria);
    }
}
