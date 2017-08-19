<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Custom exception for Api.
 *
 * @subpackage Exception
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class ApiException extends HttpException
{
}