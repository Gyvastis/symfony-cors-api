<?php

namespace App\Controller;

use App\Entity\Proxy;
use App\Enum\ProxyNormalizeFormat;
use App\Mail\ApiKeyMailSender;
use App\Repository\ProxyRepository;
use App\Repository\UserRepository;
use App\Serializer\Normalizer\ProxyNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class ApiController extends AbstractController
{
    /**
     * @Route("/key", name="apiKey", methods={"GET"})
     */
    public function getKey(
        Request $request,
        UserRepository $userRepository,
        ApiKeyMailSender $apiKeyMailSender
    ): JsonResponse {
        $response = [
            'message' => '',
            'data' => []
        ];

//        $currentIp = $this->container->get('request_stack')->getMasterRequest()->getClientIp();
//        $serverIp = $this->getParameter('server_ip');
//
//        if ($currentIp !== $serverIp) {
//            $response['message'] = 'Forbidden.';
//
//            return new JsonResponse($response, Response::HTTP_FORBIDDEN);
//        }

        $email = $request->get('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Required param `email` not set or is not valid.';

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->createUser($email);

        if (!$user) {
            $response['message'] = sprintf('User with `email` `%s` already exists.', $email);

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        $apiKeyMailSender->send($user->getEmail(), $user->getApiKey());

        $response['message'] = 'An email with an API key has been sent to you.';

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
