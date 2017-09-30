# Http Serializer [![Build Status](https://travis-ci.org/geosocio/http-serializer.svg?branch=develop)](https://travis-ci.org/geosocio/http-serializer) [![Coverage Status](https://coveralls.io/repos/github/geosocio/http-serializer/badge.svg)](https://coveralls.io/github/geosocio/http-serializer)
Serializes a Controller Request & Response.

### Request
`POST` & `PUT` requests may have their contents deserialized into an object. Any
request with a content body (e.g. `PATCH`) may have the contents decoded into
an array.

### Response
A controller's response will serialized into the same format of the
request. If you need more advanced handling, you may always return a `Response`
object without interference.


### Groups
Groups may be applied to the request or the response (or both) with the
groups annotation.
* Request Groups `GeoSocio\HttpSerializer\Annotation\RequestGroups`
* Response Groups `GeoSocio\HttpSerializer\Annotation\ResponseGroups`
* Request & Response Groups `Symfony\Component\Serializer\Annotation\Groups`

They may also be applied with a `GroupResolver`.

## Example
```php
/**
 * @Route("/post/{post}")
 * @Method({"GET"})
 *
 * @Groups({"show"})
 */
public function showAction(Post $post) {
    return $post;
}

/**
 * @Route("/post")
 * @Method({"POST"})
 *
 * @RequestGroups({"create"})
 * @ResponseGroups({"show"})
 */
public function createAction(Post $post) {
    $em = $this->doctrine->getEntityManager();
    $em->persist($post);
    $em->flush();
    return $post;
}

/**
 * @Route("/post/{post}")
 * @Method({"PUT"})
 *
 * @RequestGroups({"replace"})
 * @ResponseGroups({"show"})
 */
public function replaceAction(Post $post, Post $content) {
    $em = $this->doctrine->getEntityManager();
    $em->merge($content);
    $em->flush();
    return $post;
}

/**
 * @Route("/post/{post}")
 * @Method({"PATCH"})
 *
 * @RequestGroups({"update"})
 * @ResponseGroups({"show"})
 */
public function updateAction(Post $post, array $content) {
    $em = $this->doctrine->getEntityManager();
    $post = $this->serializer->denormalizer($content, $post);
    $em->flush();
    return $post;
}

/**
 * @Route("/post/{post}")
 * @Method({"DELETE"})
 */
public function deleteAction(Post $post) {
    $em = $this->doctrine->getEntityManager();
    $em->remove($post);
    $em->flush();
    return '';
}
```
