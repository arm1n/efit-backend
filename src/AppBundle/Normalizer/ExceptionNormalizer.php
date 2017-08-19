<?php

namespace AppBundle\Normalizer;

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
use AppBundle\Exception\ApiException;

/**
 * Normalizer for exceptions via FOSRest bundle.
 *
 * @subpackage Normalizer
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ExceptionNormalizer implements NormalizerInterface
{
	/**
	 * Normalizes thrown http exception from Symfony into common JSON format.
	 * 
	 * @param  \Exception $exception
	 * @param  string $format
	 * @param  array  $context
	 * @return array
	 */
    public function normalize($exception, $format = null, array $context = array())
    {
    	return [
    		'message' => $this->_getMessage($exception),
    		'code' => $this->_getCode($exception)
    	];
    }

    /**
     * Enables all exceptions to be normalized for proper JSON responses.
     *  
     * @param  mixed $data
     * @param  string $format
     * @return boolean
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof \Exception;
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