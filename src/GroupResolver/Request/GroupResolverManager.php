<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Request;

use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class GroupResolverManager implements GroupResolverManagerInterface
{
    /**
     * @var GroupResolverInterface[]
     */
    protected $resolvers = [];

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, string $type) : array
    {
        $resolved = array_map(function ($resolver) use ($request, $type) {
            return $resolver->resolve($request, $type);
        }, $this->resolvers);

        return array_merge(...$resolved);
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(GroupResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;

        return $this;
    }
}
