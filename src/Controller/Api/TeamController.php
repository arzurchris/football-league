<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 08/09/2018
 * Time: 16:07
 */

namespace App\Controller\Api;


use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TeamControllerTest
 * @package App\Controller\Api
 * @Route("/", condition="context.getHost() in ['localhost', '127.0.0.1']")
 */
class TeamController extends Controller
{
    /**
     * @Route("api/teams", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getTeamsAction(Request $request): JsonResponse
    {
        $teams = $this->getDoctrine()->getRepository('App:Team')->findAll();

        $arrayJson = [];
        /** @var Team $team */
        foreach ($teams as $team) {
            $arrayJson[] = $team->toJson();
        }
        return new JsonResponse($arrayJson);

    }

    /**
     * @Route("api/teams", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function postTeamsAction(Request $request): JsonResponse
    {
        $name = $request->get('name');
        if (empty($name)) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'Parameter not defined : name'
            ], 400);
        }

        $leagueId = $request->get('leagueId');
        if (empty($leagueId)) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'Parameter not defined : leagueId'
            ], 400);
        }
        $league = $this->getDoctrine()->getRepository('App:League')->find($leagueId);
        if (!$league) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'League does not exist with id:' . $leagueId
            ], 404);
        }

        $team = new Team();
        $team->setName($name);
        $team->setLeague($league);

        $this->getDoctrine()->getManager()->persist($team);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'result' => true,
            'id'     => $team->getId()
        ]);
    }

    /**
     * @Route("api/team/{id}", methods={"PUT"}, requirements={"id"="\d+"})
     * @param Request $request
     * @param int     $id
     * @return JsonResponse
     */
    public function putTeamAction(Request $request, int $id): JsonResponse
    {
        $team = $this->getDoctrine()->getRepository('App:Team')->find($id);
        if (!$team) {
            return new JsonResponse([
                'result'  => false,
                'message' => 'Team does not exist with id:' . $id
            ], 404);
        }
        $content = json_decode($request->getContent());

        $name = $content->name ?? null;
        if ($name) {
            $team->setName($name);
        }
        $strip = $content->strip ?? null;
        if ($strip) {
            $team->setStrip($strip);
        }

        $this->getDoctrine()->getManager()->persist($team);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse([
            'result' => true,
        ]);

    }
}