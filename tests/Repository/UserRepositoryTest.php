<?php

namespace App\Tests\Repository;

use App\Entity\Media;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);
        $this->userRepository = $this->em->getRepository(User::class);
    }

    private function createUser(string $name, string $email, string $password, array $roles, bool $blocked = false): User
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setRoles($roles);
        $user->setBlocked($blocked);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @throws Exception
     */
    public function testFindAdminUser(): void
    {
        $this->createUser('test', 'test@test.com', '123456', ['ROLE_ADMIN']);
        $result = $this->userRepository->findAdmin();

        $this->assertNotNull($result);
        $this->assertContains('ROLE_ADMIN', $result->getRoles());
    }

    public static function mediaCountProvider(): array
    {
        return [
            'aucun média' => [0],
            'un média'    => [1],
            'trois médias' => [3],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('mediaCountProvider')]
    public function testFindGuestsMediaCount(int $expectedCount): void
    {
        $guest = $this->createUser('test', 'test@test.com', '123456', ['ROLE_USER']);

        // Create as many media as expectedCount for this guest
        for ($i = 0; $i < $expectedCount; $i++) {
            $media = new Media();
            $media->setTitle('Photo' . $i);
            $media->setPath('uploads/tests_' . $i . '.jpg');
            $media->setUser($guest);
            $this->em->persist($media);
        }
        $this->em->flush();

        // Fetch all guests with their media count from the repository
        $results = $this->userRepository->findGuestsWithMediaCount();

        // Isolate our guest's row from the results using his id
        $guestResult = array_values(array_filter($results, fn($r) => $r['id'] === $guest->getId()))[0];

        // Assert that the media count matches the expected count
        $this->assertSame((string) $expectedCount, (string) $guestResult['media_count']);
    }
}
