<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

use Symfony\Component\HttpFoundation\Request;

/**
 * Group Resolver Manager.
 */
class RequestGroupResolverManager implements RequestGroupResolverManagerInterface
{
    /**
     * @var RequestGroupResolverInterface[]
     */
    protected $resolvers = [];

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, string $type) : array
    {
        $resolved = array_map(function ($resolver) use ($request, $type) {
            if ($resolver->supports($request, $type)) {
                return $resolver->resolve($request, $type);
            }

            return [];
        }, $this->resolvers);

        return array_merge(...$resolved);
    }

     /**
      * {@inheritdoc}
      */
    public function supports(Request $request, string $type) : bool
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($request, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function addResolver(RequestGroupResolverInterface $resolver)
    {
        $this->resolvers[] = $resolver;

        return $this;
    }
}
