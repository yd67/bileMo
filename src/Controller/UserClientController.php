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
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

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
     * Cette méthode permet de récupérer l'ensemble de vos utilisateurs
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne l'ensemble des utilisateurs.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"userGroup"}))
     *     )
     * )
     * 
     * @OA\Tag(name="users")
     * @Route("/api/users", name="all_user", methods={"GET"})
     */
    public function getAllUsers(): JsonResponse
    {
        $idCache = 'getAllUser';

        $jsonUsers = $this->cachePool->get($idCache, function (ItemInterface $item) {

            $item->tag("userCache");
            $item->expiresAfter(1800);

            $context = SerializationContext::create()->setGroups(['userGroup']);
            // $userData = $this->userRepo->findAll();
            $userData = $this->userRepo->findBy(['client' => $this->getUser()]) ;

            return $this->serializer->serialize($userData, 'json', $context);
        });

        return  new JsonResponse($jsonUsers, Response::HTTP_OK, [], true);
    }

    /**
     * Cette methode permet de voir en detail un de vos utilisateurs
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne le detail d'un utilisateur.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"userGroup"}))
     *     )
     * )
     * 
     * @OA\Tag(name="users")
     * @Route("/api/users/{id}", name="detail_user", methods={"GET"})
     */
    public function getDetailUser($id): JsonResponse
    {
        $user = $this->userRepo->find($id);

        if ($user) {

            if ($user->getClient() != $this->getUser()) {
                return new JsonResponse('Aucun utilisateur trouvé', Response::HTTP_NOT_FOUND);
            }

            $context = SerializationContext::create()->setGroups(['userGroup']);
            $jsonUser = $this->serializer->serialize($user, 'json', $context);

            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        }

        return new JsonResponse('Aucun utilisateur trouvé', Response::HTTP_NOT_FOUND);
    }

    /**
     * Cette méthode  permet d'ajouter un nouvel utilisateur
     * 
     * @OA\RequestBody(@Model(type=User::class, groups={"createUser"}))
     * 
     *  @OA\Response(
     *     response=201,
     *     description="Retourne les informations de utilisateur qui vient d'etre créer .",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"userGroup"}))
     *     )
     * )
     * 
     * @OA\Tag(name="users")
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

        $this->cachePool->invalidateTags(['userCache']);

        $context = SerializationContext::create()->setGroups(['userGroup']);
        $jsonBooks = $this->serializer->serialize($user, 'json', $context);

        $location = $urlGenerator->generate('detail_user', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonBooks, Response::HTTP_CREATED, ["location" => $location], true);
    }


     /**
      * Cette méthode  permet de supprimer un de vos utilisateurs
      
     * @OA\Tag(name="users")
     * @Route("/api/users/{id}", name="delete_user", methods={"DELETE"})
     */
    public function deleteClientUser($id)
    {
        $user = $this->userRepo->find($id) ;

        if ($user) {

            if ( $user->getClient() !== $this->getUser() ) {
                $message = 'Aucun utilisateur trouvé';
                return new JsonResponse($message,Response::HTTP_NOT_FOUND ) ;
            }
            
            $this->em->remove($user);
            $this->em->flush();

            $this->cachePool->invalidateTags(['userCache']);
    
            return new JsonResponse('l\'utilisateur a bien été supprimer', Response::HTTP_NO_CONTENT );
        }

        return new JsonResponse('Aucun utilisateur trouvé', Response::HTTP_NOT_FOUND);
    }
}
