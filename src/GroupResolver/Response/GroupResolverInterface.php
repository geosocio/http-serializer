<?php

namespace GeoSocio\HttpSerializer\GroupResolver\Response;

/**
 * Verification Interface.
 */
interface GroupResolverInterface
{
    /**
     * Generate the groups from the object.
     *
     * @param object $object
     *
     * @return array An array of groups.
     */
    public function resolve($object) : array;

    /**
     * Deteremine if resolver supports the currect subject.
     *
     * @param object $object
     *
     * @return bool
     */
    public function supports($object) : bool;
}
