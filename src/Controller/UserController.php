<?php

namespace App\Controller;

use App\Entity\User;
use App\Exception\ConstraintViolationException;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class UserController extends AbstractController
{
    // Properties
    private UserRepository $userRepository;
    private SerializerInterface $serializer;
    private TagAwareCacheInterface $cache;
    private EntityManagerInterface $manager;
    private ClientRepository $clientRepository;
    private ValidatorInterface $validator;
    private UrlGeneratorInterface $urlGenerator;
    private UserPasswordHasherInterface $passwordHasher;


    /**
     * 
     * The class constructor
     * 
     * @param UserRepository $userRepository The repository for user entities.
     * @param EntityManagerInterface $manager The entity manager used to manage entities.
     * @param TagAwareCacheInterface $cache The cache service used to invalidate cached user data.
     * @param SerializerInterface $serializer The serializer used to serialize the user objects into JSON.
     * @param ClientRepository $clientRepository The repository for client entities.
     * @param ValidatorInterface $validator The validator used to validate user data.
     * @param UrlGeneratorInterface $urlGenerator The URL generator used to generate resource URLs.
     * @param UserPasswordHasherInterface $passwordHasher The password hasher used to hash user passwords.
     * 
     */
    public function __construct(UserRepository $userRepository, SerializerInterface $serializer, TagAwareCacheInterface $cache, 
    EntityManagerInterface $manager, ClientRepository $clientRepository, ValidatorInterface $validator, UrlGeneratorInterface $urlGenerator, 
    UserPasswordHasherInterface $passwordHasher) 
    {
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->cache = $cache;
        $this->manager = $manager;
        $this->clientRepository = $clientRepository;
        $this->validator = $validator;
        $this->urlGenerator = $urlGenerator;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Retrieve users associated with the authenticated client.
     *  
     * This method retrieves a list of users associated with the authenticated client.
     * It utilizes caching to enhance performance by storing the serialized user data.
     * 
     * 
     * @return JsonResponse A JSON response containing the serialized user information.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns the users list',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Tag(name: 'Users')]
    #[Route('/api/client/users', name: 'getUsersByClient', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the list of users')]
    public function getUsersByClient(): JsonResponse
    {
        // The client is the authenticated user
        $client = $this->getUser();

        $idCache = "userCache-" . $client->getId();

        $jsonUsers = $this->cache->get($idCache, function (ItemInterface $item) use ($client) {
            $item->tag("userCache-" . $client->getId());
            $users =  $this->userRepository->findBy(["client" => $client]);
            $context = SerializationContext::create()->setGroups(["getUsers"]);
            return $this->serializer->serialize($users, 'json', $context);
        });

        return new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    /**
     * Retrieve details of a user associated with the authenticated client.
     * 
     * This method retrieves the details of a specific user associated with the authenticated client.
     * 
     * @OA\Response(
     *     response=200,
     *     description="Returns a user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"getUsers"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="The user's ID",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Users")
     * 
     * @param User $user The user entity to retrieve details for.
     * 
     * @return JsonResponse A JSON response containing the serialized user information.
     * 
     */
    #[OA\Response(
        response: 200,
        description: 'Returns a user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\Tag(name: 'Users')]
    #[Route('/api/client/user/{id}', name: 'getUser', methods:['GET'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to obtain the user\'s details')]
    public function getClientUser(User $user): JsonResponse
    {
        // The client is the authenticated user
        $idClient = $this->getUser()->getId();
        
        if (empty($user) && $user->getId() !== $idClient) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $this->serializer->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }


    /**
     * Delete a user associated with the authenticated client.
     * 
     * This method deletes a specific user associated with the authenticated client.
     * 
     * 
     * @param User $user The user entity to be deleted.
     * 
     * @return JsonResponse A JSON response indicating the success of the deletion.
     */
    #[OA\Response(
        response: 204,
        description: 'Deletes a user',
    )]
    #[OA\Tag(name: 'Users')]
    #[Route('/api/client/user/{id}', name: 'deleteUser', methods:['DELETE'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to delete a user')]
    public function deleteUser(User $user): JsonResponse
    {
        // The client is the authenticated user
        $idClient = $this->getUser()->getId();

        $user = $this->userRepository->findOneBy(["id" => $user->getId(), "client" => $idClient]);

        if (empty($user)) {
            return new JsonResponse(null, Response::HTTP_NOT_FOUND);
        }

        $this->cache->invalidateTags(["userCache-" . $idClient]);
        $this->manager->remove($user);
        $this->manager->flush();
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Add a new user associated with the authenticated client.
     * 
     * This method creates a new user associated with the authenticated client.
     * 
     * 
     * 
     * @param Request $request The HTTP request object containing user data.
     * 
     * @return JsonResponse A JSON response indicating the success of the user creation along with the new user's details.
     * @throws ConstraintViolationException If there are validation errors, it throws a custom HTTP exception.
     */
    #[OA\Response(
        response: 200,
        description: 'Creates a user',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: User::class, groups: ['getUsers']))
        )
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['createUser']))
    )]
    #[OA\Tag(name: 'Users')]
    #[Route('/api/client/user', name: 'addUser', methods:['POST'])]
    #[IsGranted('ROLE_USER', message: 'You do not have sufficient rights to create a user')]
    public function addUser(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        // The client is the authenticated user
        $idClient = $this->getUser()->getId();

        $user->setClient($this->clientRepository->find($idClient));

        $errors = $this->validator->validate($user);

        if ($errors->count() > 0) {
            throw new ConstraintViolationException(400, $errors);
        }

        // Password hashing
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user, 
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        $this->cache->invalidateTags(["userCache-" . $idClient]);
        $this->manager->persist($user);
        $this->manager->flush();

        $context = SerializationContext::create()->setGroups(["getUsers"]);
        $jsonUser = $this->serializer->serialize($user, 'json', $context);

        $location = $this->urlGenerator->generate('getUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
