# Http Serializer [![Build Status](https://travis-ci.org/geosocio/http-serializer.svg?branch=develop)](https://travis-ci.org/geosocio/http-serializer) [![Coverage Status](https://coveralls.io/repos/github/geosocio/http-serializer/badge.svg)](https://coveralls.io/github/geosocio/http-serializer)
Serializes a Controller Request & Response.

## Example
This controller's response would get serialized into the same format of the
request.
```php
public function showAction(Post $post) {
    return $post;
}
```

## Configuration
Define a service in your configuration like this:
```yaml
app.return_listener:
    class: GeoSocio\SerializeResponse\EventListener\KernelViewListener
    arguments:
        - '@serializer'
        - '@serializer'
        - '@security.token_storage'
        -
            - 'anonymous'
    tags:
        - { name: kernel.event_listener, event: kernel.view }
```

You can customize the Serialization Groups that are used by implementing
`GeoSocio\SerializeResponse\Serializer\UserGroupsInterface`. Then `getGroups`
will be executed on the currently authenticated user and the object that is
being normalized will be passed as an argument.
