<?php
/**
 * Created by PhpStorm.
 * User: arzurchris
 * Date: 09/09/2018
 * Time: 12:56
 */

namespace App\Security;

use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtTokenAuthenticator extends AbstractGuardAuthenticator
{
    const INVALID_TOKEN = 'Invalid Token';
    const TOKEN_NOT_FOUND = 'Token Not Found';

    private $jwtEncoder;
    private $em;

    public function __construct(JWTEncoderInterface $jwtEncoder, EntityManagerInterface $em)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->em = $em;
    }

    public function getCredentials(Request $request)
    {
        $extractor = new AuthorizationHeaderTokenExtractor(
            'Bearer',
            'Authorization'
        );

        $token = $extractor->extract($request);

        if (!$token) {
            return;
        }

        return $token;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        try {
            $data = $this->jwtEncoder->decode($credentials);
        } catch (JWTDecodeFailureException $e) {
            $data = false;
        }
        if ($data === false) {
            throw new AuthenticationException(self::INVALID_TOKEN);
        }


        $username = $data['username'];

        return $this->em
            ->getRepository('App:User')
            ->findOneBy(['username' => $username]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse(array(
            'result'  => false,
            'message' => $exception instanceof AuthenticationException
                ? $exception->getMessage() : self::INVALID_TOKEN
        ), 401);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // do nothing - let the controller be called
    }

    public function supportsRememberMe()
    {
        return false;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        // called when authentication info is missing from a
        // request that requires it
        return new JsonResponse(array(
            'result'  => false,
            'message' => 'Authentication required'
        ), 401);
    }

    public function supports(Request $request): bool
    {

        $rPathInfo = $request->getPathInfo();
        $rIsDELETE = $request->isMethod('DELETE');
        $rIsGET = $request->isMethod('GET');
        $rIsPOST = $request->isMethod('POST');
        $rIsPUT = $request->isMethod('PUT');

        //        if ($rPathInfo === '/api/tokens' && $rIsPOST) {
        //            return true;
        //        }
        if ($rPathInfo === '/api/teams' && ($rIsPOST || $rIsGET)) {
            return true;
        }
        if (strpos($rPathInfo, '/api/team/') === 0 && ($rIsPOST || $rIsPUT)) {
            return true;
        }
        if ($rPathInfo === '/api/leagues' && $rIsGET) {
            return true;
        }
        if (strpos($rPathInfo, '/api/league/') === 0 && ($rIsGET || $rIsDELETE)) {
            return true;
        }
        return false;
    }

}
