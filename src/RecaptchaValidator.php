<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client as HttpClient;
use yii\httpclient\Exception as HttpClientException;
use yii\validators\Validator;
use yii\web\ErrorHandler;
use yii\web\Request;

/**
 * Class RecaptchaValidator
 * @package alexeevdv\recaptcha
 */
class RecaptchaValidator extends Validator
{
    /**
     * @inheritdoc
     */
    public $skipOnEmpty = false;

    /**
     * Recaptcha component
     * @var string|array|Recaptcha
     */
    public $component = 'recaptcha';

    /**
     * @var array|string|HttpClient
     */
    public $apiClient = [
        'class' => HttpClient::class,
        'baseUrl' => 'https://www.google.com/recaptcha/api',
    ];

    /**
     * Secret key
     * @var string
     */
    public $secret;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->secret === null) {
            $component = Instance::ensure($this->component, Recaptcha::class);
            if ($component->secret === null) {
                throw new InvalidConfigException(Yii::t('recaptcha', '"secret" param is required.'));
            }
            $this->secret = $component->secret;
        }

        if ($this->message === null) {
            $this->message = Yii::t('recaptcha', 'The verification code is incorrect.');
        }
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    protected function validateValue($value)
    {
        if (!$value) {
            return [$this->message, []];
        }

        /** @var Request $request */
        $request = Instance::ensure('request', Request::class);
        /** @var HttpClient $httpClient */
        $httpClient = Instance::ensure($this->apiClient, HttpClient::class);
        try {
            $response = $httpClient
                ->get('siteverify', [
                    'secret' => $this->secret,
                    'response' => $value,
                    'remoteip' => $request->getUserIP(),
                ])
                ->send()
                ->getData();
        } catch (HttpClientException $httpException) {
            try {
                /** @var ErrorHandler $errorHandler */
                $errorHandler = Instance::ensure('errorHandler', ErrorHandler::class);
                $errorHandler->logException($httpException);
            } catch (InvalidConfigException $e) {
                Yii::trace($e->getMessage(), __METHOD__);
            }
            return [$this->message, []];
        }

        if (ArrayHelper::getValue($response, 'success', false)) {
            return null;
        }

        Yii::trace(ArrayHelper::getValue($response, 'error-codes'), __METHOD__);

        return [$this->message, []];
    }
}
