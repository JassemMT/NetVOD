<?php
declare(strict_types=1);
namespace netvod\mail;

class MailSender {
    private string $domain;
    private string $apiKey;
    private string $data;
    
    public function __construct(string $email, string $message) {
        $domain = "sandbox1234567890.mailgun.org"; // Ton domaine Mailgun
        $apiKey = "key-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"; // Ta clé privée

        $data = [
            'from'    => 'NetVod <no-reply@tondomaine.com>',
            'to'      => $email,
            'subject' => 'Hello depuis Mailgun',
            'text'    => $message
        ];
    }


    public function send(): string {
        $ch = curl_init();
        
        $domain = $this->domain;
        $apiKey = $this->apiKey;
        $data = $this->data;

        curl_setopt($ch, CURLOPT_URL, "https://api.mailgun.net/v3/$domain/messages");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "api:$apiKey");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $result = curl_exec($ch);
        curl_close($ch);
    
        return $result;
    }
}