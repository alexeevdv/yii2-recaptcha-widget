<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

class RecaptchaValidator extends \yii\validators\Validator
{    
    public $skipOnEmpty = false;

    public $component = 'recaptcha';

    /**
     * Secret key
     * @var string
     */
    public $secret;

    public function init()
    {
        parent::init();

        if (empty($this->secret))
        {
            if (!Yii::$app->has($this->component) || !strlen(Yii::$app->{$this->component}->secret))
            {
                throw new InvalidConfigException("`secret` param is required");
            }
            $this->secret = Yii::$app->{$this->component}->secret;
        }

        if ($this->message === null)
        {
            $this->message = Yii::t("yii", "The verification code is incorrect.");
        }
    }

    protected function validateValue($value)
    {
        if (empty($value))
        {
            $value = Yii::$app->request->post("g-recaptcha-response");
            if (!$value)
            {
                return [$this->message, []];
            }
        }

        $request = "https://www.google.com/recaptcha/api/siteverify?".http_build_query([
            "secret" => $this->secret,
            "response" => $value,
            "remoteip" => Yii::$app->request->userIP,
        ]);

        $response = Json::decode(file_get_contents($request));

        if (!isset($response['success']))
        {
            throw new Exception('Invalid reCAPTCHA verify response.');
        }

        return $response['success'] ? null : [$this->message, []];
    }    
}
