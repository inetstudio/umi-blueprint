<?php
use Inet\Proxy\Mail\iMail;
use Symfony\Component\Dotenv\Dotenv;
use Getresponse\Sdk\GetresponseClientFactory;
use Getresponse\Sdk\Client\Operation\OperationResponse;
use Getresponse\Sdk\Operation\TransactionalEmails\CreateTransactionalEmail\CreateTransactionalEmail;
use Getresponse\Sdk\Operation\Model\CreateTransactionalEmail as ModelCreateTransactionalEmail;
use Getresponse\Sdk\Operation\Model\FromFieldReference;
use Getresponse\Sdk\Operation\Model\TransactionalEmailContent;
use Getresponse\Sdk\Operation\Model\TransactionalEmailRecipients;
use Getresponse\Sdk\Operation\Model\TransactionalEmailRecipientTo;
use Getresponse\Sdk\Operation\FromFields\GetFromFields\GetFromFields;
use Getresponse\Sdk\Client\Operation\FailedOperationResponse;
use Getresponse\Sdk\Client\Exception\MalformedResponseDataException;

class getResponseMail implements iMail
{
    /**
     * Отправляет письмо через getResponse.com
     *
     * @inheritdoc
     */
    public function sendMail($emails = [], $subject = '', $content = 'default', $filePath = '', $tags = []): OperationResponse {
        $emails = is_array($emails) ? $emails : [$emails];
        if (empty($emails))
            throw new Exception('Empty Email list');

        $client = GetresponseClientFactory::createWithApiKey(static::getApiKey());

        foreach ($emails as $email) {
            // this id is from a list of the used api available senders
            $from = new FromFieldReference('R');
            $transactionalEmailContent = new TransactionalEmailContent();
            $transactionalEmailContent->setHtml($content);
            $to = new TransactionalEmailRecipientTo($email);
            $recipients = new TransactionalEmailRecipients($to);

            $createTransactionalEmail = new CreateTransactionalEmail(
                new ModelCreateTransactionalEmail(
                    $from,
                    $subject,
                    $transactionalEmailContent,
                    $recipients
                )
            );

            $response = $client->call($createTransactionalEmail);
        }

        return $response ?? FailedOperationResponse::createAsIncomplete();
    }

    /**
     * @return array
     * @throws MalformedResponseDataException
     */
    public function getAvailableFromFields(): array {
        $client = GetresponseClientFactory::createWithApiKey(static::getApiKey());
        $fromFields = new GetFromFields();
        $response = $client->call($fromFields);

        // getData returns decoded data as an array
        return $response->getData();
    }

    /**
     * @return mixed
     */
    private static function getApiKey() {
        $dotenv = new Dotenv();
        $dotenv->load(CURRENT_WORKING_DIR . '/.env');

        return $_ENV['GET_RESPONSE_API_KEY'] ?? null;
    }
}