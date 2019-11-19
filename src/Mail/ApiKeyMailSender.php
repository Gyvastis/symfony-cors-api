<?php

namespace App\Mail;

use Swift_Mailer;
use Swift_Message;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\SmtpEnvelope;
use Symfony\Component\Mailer\Transport;

class ApiKeyMailSender
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * ApiKeyMailSender constructor.
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param string $email
     * @param string $apiKey
     */
    public function send(string $email, string $apiKey): void
    {
        $email = (new Swift_Message())
            ->setFrom('noreply@handyproxy.io')
            ->setTo($email)
            ->setSubject('Your API key | HandyProxy.io')
            ->setBody($this->getEmailMessage($apiKey))
            ->setContentType('text/html');

        $this->mailer->send($email);
    }

    private function getEmailMessage(string $apiKey): string
    {
        return sprintf('
        <html xmlns="https://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
            <head>
                <!--[if gte mso 9]><xml>
                <o:OfficeDocumentSettings>
                <o:AllowPNG/>
                <o:PixelsPerInch>96</o:PixelsPerInch>
                </o:OfficeDocumentSettings>
                </xml><![endif]-->
                <title>Christmas Email template</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
                <meta name="format-detection" content="telephone=no">
                <!--[if !mso]><!-->
                <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
                <!--<![endif]-->
            </head>
            <body>
                <p>Hello there! Thanks for joining <a href="https://handyproxy.io">handyproxy.io</a>! Here is your API key:</p>
                <p>
                    <code>%s</code>
                </p>
                <p>
                    Cheers!
                    <br />
                    Vaidas
                    <br /><br />
                    If you like it follow me on <a href="https://twitter.com/VaidasBagdonas">Twitter</a>
                </p>
            </body>
        </html>
        ', $apiKey);
    }
}