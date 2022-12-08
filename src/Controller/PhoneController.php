<?php

namespace App\Controller;

use App\Repository\PhoneRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/api/phones", name="all_phones", methods={"GET"})
     */
    public function getAllPhones(Request $request): JsonResponse
    {
        $page = $request->get('page');
        $limit = $request->get('limit');

        $idCache = 'getAllPhones-'.$page.$limit ;

        $jsonPhones = $this->cachePool->get($idCache, function (ItemInterface $item) use ( $page, $limit) {
            $item->tag("phoneCache");
            $phoneData = $this->phoneRepo->findAllWithPagination($page, $limit);

            return $this->serializer->serialize($phoneData,'json',['groups' => 'phoneGroup']);
        });

        return  new JsonResponse($jsonPhones,Response::HTTP_OK,[],true);
    }

     /**
     * @Route("/api/phones/{id}", name="phone", methods={"GET"})
     */
    public function getPhone($id): JsonResponse
    {
        $phone = $this->phoneRepo->find($id) ;

        if ($phone) {
            $jsonPhone = $this->serializer->serialize($phone,'json',['groups' => 'phoneGroup']) ;

            return new JsonResponse($jsonPhone,Response::HTTP_OK,[],true) ;
        }

         return new JsonResponse('no phone for this id',Response::HTTP_NOT_FOUND) ;

    }
}
