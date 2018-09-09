<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 08/09/2018
 * Time: 17:35
 */

namespace App\Controller\Api;

use App\Entity\League;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class LeagueController
 * @package App\Controller\Api
 * @Route("/", condition="context.getHost() in ['localhost', '127.0.0.1']")
 */
class LeagueController extends Controller
{
    /**
     * @Route("/api/leagues", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getLeaguesAction(Request $request): JsonResponse
    {
        $leagues = $this->getDoctrine()->getRepository('App:League')->findAll();

        $arrayJson = [];
        /** @var League $league */
        foreach ($leagues as $league) {
            $arrayJson[] = $league->toJson();
        }
        return new JsonResponse($arrayJson);

    }

    /**
     * @Route("/api/league/{id}", methods={"GET"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     */
    public function getLeagueAction(Request $request, int $id): JsonResponse
    {
        $league = $this->getDoctrine()->getRepository('App:League')->find($id);
        if (!$league) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'League does not exist with id:' . $id
            ], 404);
        }

        return new JsonResponse($league->toJson());

    }

    /**
     * @Route("/api/league/{id}", methods={"DELETE"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     */
    public function deleteLeagueAction(Request $request, int $id): JsonResponse
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');

        $league = $this->getDoctrine()->getRepository('App:League')->find($id);
        if (!$league) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'League does not exist with id:' . $id
            ], 404);
        }

        $this->getDoctrine()->getManager()->remove($league);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'result' => true,
        ]);
    }
}