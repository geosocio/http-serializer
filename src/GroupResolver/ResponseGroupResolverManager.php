<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class ResponseGroupResolverManager implements ResponseGroupResolverInterface
{
    /**
     * @var ResponseGroupResolverInterface[]
     */
    protected $resolvers = [];

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, $object) : array
    {
        $resolved = array_map(function ($resolver) use ($request, $object) {
            if ($resolver->supports($request, $object)) {
                return $resolver->resolve($request, $object);
            }

            return [];
        }, $this->resolvers);

        return array_merge(...$resolved);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, $object) : bool
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($request, $object)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(ResponseGroupResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;

        return $this;
    }
}
