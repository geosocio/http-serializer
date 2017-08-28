<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

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
        $resolved = array_map(function ($resolver) use ($request) {
            return $resolver->resolve($request);
        }, $this->resolvers);

        return array_merge(...$resolved);
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(GroupResolverInterface $resolver) : self
    {
        $this->resolvers[] = $resolver;

        return $this;
    }
}
