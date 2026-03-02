<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetRolesAlwaysContainsRoleUser(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    /**
     * @return void
     */
    public function testSetRoles(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_ADMIN']);
        $this->assertContains('ROLE_ADMIN', $user->getRoles());
        $this->assertContains('ROLE_USER', $user->getRoles());
    }

    /**
     * @return void
     */
    public function testGetUserIdentifierReturnsEmail(): void
    {
        $user = new User();
        $user->setEmail('test@test.com');
        $this->assertEquals('test@test.com', $user->getUserIdentifier());
    }

    /**
     * @return void
     */
    public function testIsBlockedDefaultFalse(): void
    {
        $user = new User();
        $this->assertFalse($user->isBlocked());
    }

    /**
     * @return void
     */
    public function testSetBlocked(): void
    {
        $user = new User();
        $user->setBlocked(true);
        $this->assertTrue($user->isBlocked());
    }

    /**
     * @return void
     */
    public function testGetRolesNoDuplicates(): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $roles = $user->getRoles();
        $this->assertCount(1, $roles);
        $this->assertSame($roles, array_unique($roles));
    }
}
