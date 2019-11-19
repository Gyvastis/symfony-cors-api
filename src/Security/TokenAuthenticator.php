<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class TokenAuthenticator extends AbstractGuardAuthenticator
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request)
    {
        return $request->query->has('token');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     */
    public function getCredentials(Request $request)
    {
        $headers = $request->headers->all();
        $origin = null;
        $sameOrigin = false;

//        if(isset($headers['origin']) && is_array($headers['origin']) && count($headers['origin']) > 0) {
//            $origin = $headers['origin'][0];
//        }
        if (isset($headers['sec-fetch-site']) && is_array($headers['sec-fetch-site']) && count($headers['sec-fetch-site']) > 0) {
            $sameOrigin = strpos($headers['sec-fetch-site'][0], 'same') >= 0;
            $isCors = false;

            if (isset($headers['sec-fetch-mode']) && is_array($headers['sec-fetch-mode']) && count($headers['sec-fetch-mode']) > 0) {
                $isCors = $headers['sec-fetch-mode'][0] === 'cors';
            }

            $sameOrigin = $isCors && $sameOrigin;
        }

        return [
            'token' => $request->query->get('token'),
            'origin' => $origin,
            'same_origin' => $sameOrigin
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $apiKey = $credentials['token'];

        if (null === $apiKey) {
            return;
        }

        // if a User object, checkCredentials() is called
        return $this->em->getRepository(User::class)->findOneBy(['apiKey' => $apiKey]);
    }

    /**
     * @param array $credentials
     * @param User $user
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
//        return $user->getRestrictToOrigin() ? $user->getRestrictToOrigin() === $credentials['origin'] : true;
        return $user->getRestrictToOrigin() ? $credentials['same_origin'] : true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        /** @var User $user */
        $user = $token->getUser();
        $user->setApiHitCount((int)$user->getApiHitCount() + 1);
        $user->setLastApiHitAt(new DateTime('now'));
        $this->em->flush();

        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}