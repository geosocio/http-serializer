<?php

namespace GeoSocio\HttpSerializer\GroupResolver;

/**
 * Group Resolver Manager Interface.
 */
interface ResponseGroupResolverManagerInterface extends ResponseGroupResolverInterface
{
    /**
     * Adds a group resolver to the manager.
     *
     * @param ResponseGroupResolverInterface $resolver
     *
     * @return self
     */
    public function addResolver(ResponseGroupResolverInterface $resolver);
}
