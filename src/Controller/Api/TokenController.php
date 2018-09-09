<?php

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

/**
 * Class TokenController
 * @package AppBundle\Controller\Api
 */
class TokenController extends Controller
{
    /**
     * @Route("/api/tokens", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException
     */
    public function newTokenAction(Request $request): JsonResponse
    {
        $user = $this->getDoctrine()
            ->getRepository('App:User')
            ->findOneBy(['username' => $request->getUser()]);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        $isValid = $this->get('security.password_encoder')->isPasswordValid($user, $request->getPassword());

        if (!$isValid) {
            throw new BadCredentialsException();
        }

        // TODO find an alternative to lexik encoder
        $token = $this->get('lexik_jwt_authentication.encoder')->encode(['username' => $user->getUsername()]);

        return new JsonResponse(['token' => $token]);
    }
}