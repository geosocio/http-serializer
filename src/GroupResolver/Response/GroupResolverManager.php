<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

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
    public function resolve($object) : array
    {
        $resolved = array_map(function ($resolver) use ($object) {
            if ($resolver->supports($object)) {
                return $resolver->resolve($object);
            }

            return [];
        }, $this->resolvers);

        return array_merge(...$resolved);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($object) : bool
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($object)) {
                return true;
            }
        }

        return false;
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
