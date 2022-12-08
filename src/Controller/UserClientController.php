<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserClientController extends AbstractController
{
    /** @var UserRepository */
    public $userRepo;

    /** @var SerializerInterface */
    public $serializer;

    /** @var ValidatorInterface */
    public $validator;

    /** @var ntityManagerInterface */
    public $em;

    /** @var TagAwareCacheInterface */
    public $cachePool;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepo, SerializerInterface $serializer, ValidatorInterface $validator, TagAwareCacheInterface $cachePool)
    {
        $this->em = $entityManager;
        $this->userRepo = $userRepo;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->cachePool = $cachePool;
    }

    /**
     * @Route("/api/users", name="all_user", methods={"GET"})
     */
    public function getAllUsers(): JsonResponse
    {
        $idCache = 'getAllUser';

        $jsonUsers = $this->cachePool->get($idCache, function (ItemInterface $item) {
            $item->tag("userCache");

            $context = SerializationContext::create()->setGroups(['userGroup']);
            $userData = $this->userRepo->findAll();

            return $this->serializer->serialize($userData, 'json', $context);
        });

        return  new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/api/users/{id}", name="detail_user", methods={"GET"})
     */
    public function getDetailUser($id): JsonResponse
    {
        $user = $this->userRepo->find($id);

        if ($user) {
            $context = SerializationContext::create()->setGroups(['userGroup']);

            $jsonUser = $this->serializer->serialize($user, 'json', $context);

            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        }

        return new JsonResponse('cet utilisateur n\'existe pas', Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/api/users", name="add_client_user", methods={"POST"})
     */
    public function addClientUser(Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($this->getUser());

        $errors = $this->validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $this->em->persist($user);
        $this->em->flush();

        $context = SerializationContext::create()->setGroups(['userGroup']);
        $jsonBooks = $this->serializer->serialize($user, 'json', $context);

        $location = $urlGenerator->generate('detail_user', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBooks, Response::HTTP_CREATED, ["location" => $location], true);
    }
}
