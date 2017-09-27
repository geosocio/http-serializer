<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

/**
 * Group Resolver Manager Interface.
 */
interface GroupResolverManagerInterface extends GroupResolverInterface
{
    /**
     * Adds a group resolver to the manager.
     *
     * @param GroupResolverInterface $resolver
     *
     * @return self
     */
    public function addResolver(GroupResolverInterface $resolver);
}