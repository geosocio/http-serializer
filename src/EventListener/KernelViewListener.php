<?php

namespace GeoSocio\SerializeResponse\EventListener;

use GeoSocio\SerializeResponse\Serializer\UserGroupsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Listen for Exceptions.
 */
class KernelViewListener
{

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    /**
     * @var string[]
     */
    protected $defaultGroups;

    /**
     * Creates the Event Listener.
     */
    public function __construct(
        SerializerInterface $serializer,
        NormalizerInterface $normalizer,
        TokenStorageInterface $tokenStorage,
        array $defaultGroups = []
    ) {
        $this->serializer = $serializer;
        $this->normalizer = $normalizer;
        $this->tokenStorage = $tokenStorage;
        $this->defaultGroups = $defaultGroups;
    }

    /**
     * Handle the Kernel Exception.
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event) : Response
    {
        $request = $event->getRequest();
        $result = $event->getControllerResult();

        $status = 200;
        switch ($request->getMethod()) {
            case Request::METHOD_POST:
                $status = 201;
                break;
            case Request::METHOD_DELETE:
                $status = 204;
                break;
        }

        // Allow the groups to be modified on each item in a collection.
        if (is_iterable($result) && $this->isCollection($result)) {
            if (is_object($result) && method_exists($result, 'toArray')) {
                $result = $result->toArray();
            }

            $result = array_map(function ($item) {
                return $this->normalize($item);
            }, $result);
        } else {
            $result = $this->normalize($result);
        }

        $response = new Response(
            $this->serializer->serialize($result, $request->getRequestFormat()),
            $status,
            $headers
        );
        $event->setResponse($response);

        return $response;
    }

    /**
     * Determines if the iterable is a collection or an associative array.
     */
    protected function isCollection(iterable $data) : bool
    {
        $keys = array_keys($data);
        return $keys === array_keys($keys);
    }

    /**
     * Get a user from the Security Token Storage.
     */
    protected function getUser() :? UserInterface
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();

        if (!is_object($user)) {
            return null;
        }

        return $user;
    }

    /**
     * Normalize an object.
     */
    protected function normalize($result)
    {
        $groups = $this->defaultGroups;

        $user = $this->getUser();
        if ($user instanceof UserGroupsInterface) {
            $groups = $user->getGroups($result);
        }

        $context = [
            'groups' => $groups,
            'enable_max_depth' => true,
        ];

        return $this->normalizer->normalize($result, null, $context);
    }
}
