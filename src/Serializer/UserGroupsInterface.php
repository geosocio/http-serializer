<?php

namespace GeoSocio\SerializeResponse\Serializer;

interface UserGroupsInterface
{

    /**
     * Get Groups for user.
     *
     * @param mixed $data
     *   Data that was returned from the controller that is being serialized.
     *
     * @return string[]
     */
    public function getGroups($data = null);
}
