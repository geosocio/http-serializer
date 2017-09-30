<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

/**
 * Group Resolver Manager Interface.
 */
interface RequestGroupResolverManagerInterface extends RequestGroupResolverInterface
{
    /**
     * Adds a group resolver to the manager.
     *
     * @param RequestGroupResolverInterface $resolver
     *
     * @return self
     */
    public function addResolver(RequestGroupResolverInterface $resolver);
}
