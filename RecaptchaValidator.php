<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Json;

class RecaptchaValidator extends \yii\validators\Validator
{    
    public $skipOnEmpty = false;

    public $componentId = 'recaptcha';

    /**
     * Secret key
     * @var string
     */
    public $secret;

    public function init()
    {
        parent::init();

        if ($this->secret === null)
        {
            if ($this->component === null || $this->component->secret === null)
            {
                throw new InvalidConfigException(Yii::t('recaptcha', '"secret" param is required.'));
            }
            $this->secret = $this->component->secret;
        }

        if ($this->message === null)
        {
            $this->message = Yii::t('recaptcha', 'The verification code is incorrect.');
        }
    }

    protected function validateValue($value)
    {
        if (empty($value))
        {
            if (Yii::$app->request->isPost)
            {
                $value = Yii::$app->request->post("g-recaptcha-response");
            }
            else
            {
                $value = Yii::$app->request->get("g-recaptcha-response");
            }

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
            throw new Exception('recaptcha', 'Invalid reCAPTCHA verify response.');
        }

        return $response['success'] ? null : [$this->message, []];
    }

    public function getComponent()
    {
        if (!Yii::$app->has($this->componentId))
        {
            return null;
        }
        return Yii::$app->{$this->componentId};
    }
}
