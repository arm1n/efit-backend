<?php

namespace AppBundle\Response;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streamed response for server sent events.
 *
 * @subpackage Response
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class SSE
{
    /** @var integer $sleepTime Seconds event loop is interrupted between to iterations. */
    public $sleepTime = 3;

    /** @var integer $retryTime Seconds client can reconnect with `retry` field. */
    public $retryTime = 3;

    /** @var string $eventName Name of the event for `event` field. */
    public $eventName = null;

    /** @var integer $keepAliveTime Seconds after comment is send. */
    public $keepAliveTime = 300;

    /** @var callable $fetchData Closure to get event data. */
    public $fetchData = null;

    /** @var callable $exitStream Closure to close stream. */
    public $exitStream = null;

    /** @var RequestStack $requestStack */
    private $requestStack;

    /** @var int $start Start time for keep alive comments. */
    private $start;

    /** @var int $id Id counter for `id` field. */
    private $id;

    /** @constructor */
    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
        
        $this->id = intval(
            $requestStack
                ->getCurrentRequest()
                ->headers
                ->get('Last-Event-ID', 0)
        );

        $this->_setupBuffering();
    }

    /**
     * Creates a streaming response object for server-sent event.
     * 
     * @return Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getResponse()
    {
        return new StreamedResponse(
            [$this, 'streamCallback'],
            Response::HTTP_OK,
            [
                'Content-Type' => 'text/event-stream',
                'Access-Control-Allow-Origin' => '*',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }

    /**
     * The actual stream response callback.
     *
     * @return void
     */
    public function streamCallback() {
        $this->start = time();

        while(!$this->_exitStream()) {
            $this->_writeComment();
            $this->_writeMessage();
            $this->_flushContent();
            $this->_closeSession();
            
            sleep($this->sleepTime);
        };
    }

    /**
     * Sets up buffering environment for SSE.
     *
     * @return void
     */
    private function _setupBuffering()
    {
        // disable time limit
        @set_time_limit(0);
        
        // prevent buffering
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }

        @ini_set('zlib.output_compression', 0);
        
        // turn on implicity flushing
        @ini_set('implicit_flush', true);
        while (@ob_get_level() !== 0) {
            @ob_end_flush();
        }

        @ob_implicit_flush(true);
    }

    /**
     * Checks if stream loop should be interrupted.
     *
     * @return bool
     */
    private function _exitStream()
    {
        if (!is_callable($this->exitStream)) {
            return false;
        }

        return call_user_func($this->exitStream);
    }

    /**
     * Writes comment for keep alive mechanism.
     *
     * @return void
     */
    private function _writeComment()
    {
        $connectionTime = time() - $this->start;
        if ($connectionTime % $this->keepAliveTime === 0) {
            $comment = sprintf(': %s', sha1(mt_rand()) . PHP_EOL);

            echo $comment . PHP_EOL;
        }
    }

    /**
     * Writes server-sent message with `id`, 
     * `data`, `event` and `retry` fields.
     *
     * @return void
     */
    private function _writeMessage()
    {
        if (!is_callable($this->fetchData)) {
            return;
        }

        $message = '';
        $message .= sprintf('id: %s', $this->id++ . PHP_EOL);
        $message .= sprintf('retry: %s', $this->retryTime * 1000 . PHP_EOL);
        $message .= sprintf('data: %s', call_user_func($this->fetchData) . PHP_EOL);

        if (!empty($this->eventName)) {
            $message .= sprintf('event: %s', $this->eventName . PHP_EOL);
        }

        echo $message . PHP_EOL;
    }

    /**
     * Tries to flush contents.
     *
     * @return void
     */
    private function _flushContent()
    {
        @ob_flush();
        @flush();
    }

    /**
     * Tries to save current session.
     *
     * @return void
     */
    private function _closeSession()
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if ($session===null || !$session->isStarted()) {
            return;
        }     
        
        $session->save();
    }
}