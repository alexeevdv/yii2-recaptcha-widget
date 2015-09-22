<?php

namespace alexeevdv\recaptcha;

class Validator extends \yii\validators\Validator {
    
    public $skipOnEmpty = false;

    /**
     * Secret key
     * @var string
     */
    public $secret;

    public function init() {
        
        parent::init();

        if (empty($this->secret)) {
            if (!\yii::$app->has("recaptcha") || empty(\yii::$app->recaptcha->secret)) {
                throw new \yii\base\InvalidConfigException("`secret` param is required");
            }
            $this->secret = \yii::$app->recaptcha->secret;
        }

        if ($this->message === null) {
            $this->message = \yii::t("yii", "The verification code is incorrect.");
        }
    }

    protected function validateValue($value) {
        if (empty($value)) {
            $value = \yii::$app->request->post("g-recaptcha-response");
            if (!$value) {
                return [$this->message, []];
            }
        }

        $request = "https://www.google.com/recaptcha/api/siteverify?".http_build_query([
            "secret" => $this->secret,
            "response" => $value,
            "remoteip" => \yii::$app->request->userIP,
        ]);

        $response = \yii\helpers\Json::decode(file_get_contents($request));

        if (!isset($response['success'])) {
            throw new \yii\base\Exception('Invalid reCAPTCHA verify response.');
        }

        return $response['success'] ? null : [$this->message, []];
    }    
}
