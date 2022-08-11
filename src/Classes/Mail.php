<?php

namespace App\Classes;

use Mailjet\Client;
use Mailjet\Resources;

class Mail 
{
    private $api_key = '2f9bcc3e60860105e8e0a5f99139262b';
    private $api_key_secret = 'caa3534cb93ba32007f8a747d7d97fdc';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "capdemat89@yahoo.com",
                        'Name' => "The shop Project"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4128290,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}