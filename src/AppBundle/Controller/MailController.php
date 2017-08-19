<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Request\ParamFetcher;
use AppBundle\Controller\ApiController;

/**
 * REST controller for sending emails to super admins.
 *
 * @subpackage Controller
 * @package AppBundle
 * @author Armin Pfurtscheller
 */
class MailController extends ApiController
{
    private $mailer;
    public function __construct(ValidatorInterface $validator, \Swift_Mailer $mailer)
    {
        parent::__construct($validator);
        $this->mailer = $mailer;
    }

    /**
     * Sends an email through 
     * 
     * @View()
     * @Post("/api/mail")
     * @Security("has_role('ROLE_USER')")
     * @RequestParam(name="_name", description="Sender name for mail.")
     * @RequestParam(name="_mail", description="Sender address for mail.")
     * @RequestParam(name="_subject", description="Subject for mail.")
     * @RequestParam(name="_message", description="Content for mail.")
     *
     * @return Symfony\Component\HttpFoundation\Response
     */
    public function sendMailAction(ParamFetcher $paramFetcher)
    {
        $name = $paramFetcher->get('_name');
        $mail = $paramFetcher->get('_mail');
        $subject = $paramFetcher->get('_subject');
        $message = $paramFetcher->get('_message');

        try {
            $email = new \Swift_Message($subject);
            $email->setReplyTo([$mail => $name]);
            $email->setFrom([$mail => $name]);
            $email->setBody($message);

            if (!$this->mailer->send($email)) {
                $this->apiException(
                    'Your email could not be sent!', 
                    Response::HTTP_SERVICE_UNAVAILABLE
                );
            }

            return ['success' => true];
        } catch(\Exception $e) {
            $this->apiException(
                'Your email could not be sent!', 
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }
    }
}