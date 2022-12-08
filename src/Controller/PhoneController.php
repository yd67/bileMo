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
            
            $context = SerializationContext::create()->setGroups(['phoneGroup']) ;
            $phoneData = $this->phoneRepo->findAllWithPagination($page, $limit);

            return $this->serializer->serialize($phoneData,'json',$context);
        });

        return  new JsonResponse($jsonPhones,Response::HTTP_OK,[],true);
    }

     /**
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
