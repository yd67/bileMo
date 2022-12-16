<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class PhoneController extends AbstractController
{
    public $serializer;
    public $phoneRepo ;
    public $cachePool ;

    public function __construct(SerializerInterface $serializer ,PhoneRepository $phoneRepo,TagAwareCacheInterface $cachePool)
    {
        $this->serializer = $serializer;
        $this->phoneRepo = $phoneRepo ;
        $this->cachePool = $cachePool ;
    }

    /**
     * Cette méthode permet de récupérer l'ensemble des telephones.
     * 
     * Vous pouvez appliquer une pagination en rajoutant les paramètres pages et limit à votre requête
     *
     * @OA\Response(
     *     response=200,
     *     description="Retourne l'ensemble des telephones.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"phoneGroup"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Phones")
     * @Route("/api/phones", name="all_phones", methods={"GET"})
     */
    public function getAllPhones(Request $request): JsonResponse
    {
        $page = $request->get('page');
        $limit = $request->get('limit');

        $idCache = 'getAllPhones-'.$page.$limit ;

        $jsonPhones = $this->cachePool->get($idCache, function (ItemInterface $item) use ( $page, $limit) {
            $item->tag("phoneCache");
            
            $context = SerializationContext::create()->setGroups(['phoneGroup']) ;
            $phoneData = $this->phoneRepo->findAllWithPagination($page, $limit);

            return $this->serializer->serialize($phoneData,'json',$context);
        });

        return  new JsonResponse($jsonPhones,Response::HTTP_OK,[],true);
    }

     /**
     *  Cette méthode permet de récupérer le détail d'un telephone
     * 
     * @OA\Response(
     *     response=200,
     *     description="Retourne le detail d'un telephone . ",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Phone::class, groups={"phoneGroup"}))
     *     )
     * )
     * @OA\Tag(name="Phones")
     * @Route("/api/phones/{id}", name="detail_phone", methods={"GET"})
     */
    public function getPhone($id): JsonResponse
    {
        $phone = $this->phoneRepo->find($id) ;

        if ($phone) {
            $context = SerializationContext::create()->setGroups(['phoneGroup']) ;
            
            $jsonPhone = $this->serializer->serialize($phone,'json',$context) ;

            return new JsonResponse($jsonPhone,Response::HTTP_OK,[],true) ;
        }

         return new JsonResponse('no phone for this id',Response::HTTP_NOT_FOUND) ;

    }
}
