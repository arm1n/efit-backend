<?php

namespace AppBundle\Handler;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\LengthRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionRequiredHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\XmlSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Context;

use AppBundle\Exception\ApiException;

/**
 * Implements the SubscribingHandlerInterface for serializing exceptions:
 * https://github.com/FriendsOfSymfony/FOSRestBundle/issues/1500
 * http://jmsyst.com/libs/serializer/master/handlers
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class JMSExceptionHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     * @return array
     */
    public static function getSubscribingMethods()
    {
        return [
            [
                'format' => 'json',
                'type' => \Exception::class,
                'method' => 'serializeToJson',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
            ],
            [
                'format' => 'xml',
                'type' => \Exception::class,
                'method' => 'serializeToXml',
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
            ],
        ];
    }
    /**
     * Serializes array data to json.
     * 
     * @param JsonSerializationVisitor $visitor
     * @param Exception $exception
     * @param array $type
     * @param Context $context
     *
     * @return array
     */
    public function serializeToJson(JsonSerializationVisitor $visitor, \Exception $exception, array $type, Context $context)
    {
        $data = $this->convertToArray($exception, $context);
        return $visitor->visitArray($data, $type, $context);
    }
    /**
     * Serializes array data to xml.
     * 
     * @param XmlSerializationVisitor $visitor
     * @param Exception $exception
     * @param array $type
     * @param Context $context
     */
    public function serializeToXml(XmlSerializationVisitor $visitor, \Exception $exception, array $type, Context $context)
    {
        $data = $this->convertToArray($exception, $context);
        if (null === $visitor->document) {
            $visitor->document = $visitor->createDocument(null, null, true);
        }
        foreach ($data as $key => $value) {
            $entryNode = $visitor->document->createElement($key);
            $visitor->getCurrentNode()->appendChild($entryNode);
            $visitor->setCurrentNode($entryNode);
            $node = $context->getNavigator()->accept($value, null, $context);
            if (null !== $node) {
                $visitor->getCurrentNode()->appendChild($node);
            }
            $visitor->revertCurrentNode();
        }
    }

    /**
     * Converts an exception to an array with `message` and `code`.
     * 
     * @param \Exception $exception
     *
     * @return array
     */
    protected function convertToArray(\Exception $exception)
    {       
        return [
            'message' => $this->_getMessage($exception),
            'code' => $this->_getCode($exception)
        ];
    }

    /**
     * Provides exception message for `ApiException`, normalizes
     * all messages from `HttpException` and falls back to default
     * message `An unknown server error occured!` if general error. 
     * 
     * @param  \Exception $exception
     * @return string
     */
    private function _getMessage(\Exception $exception)
    {
        // http://api.symfony.com/3.3/Symfony/Component/HttpKernel/Exception.html
        switch(true) {
            case $exception instanceof ApiException: // custom
                return $exception->getMessage();

            case $exception instanceof BadRequestHttpException: // 400
                return 'The request data contains invalid data!';
        
            case $exception instanceof UnauthorizedHttpException: // 401
                return 'This operation requires authorization!';
                
            case $exception instanceof AccessDeniedHttpException: // 403
                return 'You have no access to this resource!';
                
            case $exception instanceof NotFoundHttpException: // 404
                return 'The resource could not be found!';
                
            case $exception instanceof MethodNotAllowedHttpException: // 405
                return 'This method is not allowed!';
                
            case $exception instanceof NotAcceptableHttpException: // 406
                return 'The resource is not acceptable!';
                
            case $exception instanceof ConflictHttpException: // 409
                return 'There is a conflict with this resource!';
                
            case $exception instanceof GoneHttpException: // 410
                return 'The resource has gone!';
                
            case $exception instanceof LengthRequiredHttpException: // 411
                return 'The resource requires length data!';
                
            case $exception instanceof PreconditionFailedHttpException: // 412
                return 'The resource precondition failed!';
                
            case $exception instanceof UnsupportedMediaTypeHttpException: // 415
                return 'This media type is not supported!';
                
            case $exception instanceof UnprocessableEntityHttpException: // 422
                return 'This entity is not processable!';
                
            case $exception instanceof PreconditionRequiredHttpException: // 428
                return 'This resource requires precondition!';
                
            case $exception instanceof TooManyRequestsHttpException: // 429
                return 'There were too many requests!';
                
            case $exception instanceof ServiceUnavailableHttpException: // 503
                return 'This service is unavailable!';
                
            default:
                return 'An unknown server error occured!'; // 500
        }
    }

    /**
     * Extracts exception's code in case it's a `HttpException` or
     * will set 500 error code if it's a general server exception.
     * 
     * @param  \Exception $exception
     * @return int
     */
    private function _getCode(\Exception $exception)
    {
        if ($exception instanceof HttpException) {
            return $exception->getStatusCode();
        }

        return 500;
    }
}



