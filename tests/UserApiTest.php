<?php
// api/tests/AbstractTest.php
namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;


class UserApiTest extends ApiTestCase
{
    private KernelBrowser $client;

    function cleanUsers()
    {
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        // Remove any existing users from the test database
        foreach ($userRepository->findAll() as $user) {
            $em->remove($user);
        }

        $em->flush();
    }

    function generateAndReturnUser($user): User
    {
        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository(User::class);

        $em->persist($user);
        $em->flush();

        $newUserRepository = $em->getRepository(User::class);
        return $userRepository->find($user->getId());
    }

    public function setUp(): void
    {
        self::bootKernel();
    }

    protected function createClientWithCredentials($user): Client
    {
        $token = $this->getToken($user);

        return static::createClient([], ['headers' => ['authorization' => 'Bearer '.$token]]);
    }

    protected function getToken($user): string
    {
        return $user->getEmail();
    }

    public function testGetItself()
    {
        $this->cleanUsers();
        $user = $this->generateAndReturnUser((new User)->setEmail('toGet@example.com'));

        $response = $this->createClientWithCredentials($user)->request('GET', 'api/users/'.$user->getId());
        $this->assertResponseIsSuccessful();
    }

    public function testGetAllForUserRole()
    {
        $this->cleanUsers();
        $user = $this->generateAndReturnUser((new User)->setEmail('toGet@example.com'));

        $response = $this->createClientWithCredentials($user)->request('GET', 'api/users');
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testPatchItself()
    {
        $this->cleanUsers();

        $container = static::getContainer();
        $em = $container->get('doctrine.orm.entity_manager');

        $user = $this->generateAndReturnUser((new User)->setEmail('toPatch@example.com'));

        $response = $this->createClientWithCredentials($user)->request('PATCH', 'api/users/'.$user->getId(), [
            'headers' => ['content-type' => 'application/merge-patch+json'],
            'json' => ['roles' => ['ROLE_USER', 'ROLE_TEST']]
        ]);
        $this->assertResponseIsSuccessful();

        $newUserRepository = $em->getRepository(User::class);
        $newUser = $newUserRepository->find($user->getId());
        $this->assertSame(['ROLE_USER', 'ROLE_TEST'], $newUser->getRoles());
    }

    public function testUserPatchOther()
    {
        $this->cleanUsers();
        $patcher = $this->generateAndReturnUser((new User)->setEmail('patcher@example.com'));

        $user = $this->generateAndReturnUser((new User)->setEmail('toPatch@example.com'));

        $response = $this->createClientWithCredentials($patcher)->request('PATCH', 'api/users/'.$user->getId(), [
            'headers' => ['content-type' => 'application/merge-patch+json'],
            'json' => []
        ]);
        $this->assertJsonContains(['hydra:description' => 'Access Denied.']);
        $this->assertResponseStatusCodeSame(403);
    }
}
