<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\validators\Validator;

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
     * Recaptcha component ID
     * @var string
     */
    public $componentId = 'recaptcha';

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
            if ($this->component === null || $this->component->secret === null) {
                throw new InvalidConfigException(Yii::t('recaptcha', '"secret" param is required.'));
            }
            $this->secret = $this->component->secret;
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
        if (empty($value)) {
            if (Yii::$app->request->isPost) {
                $value = Yii::$app->request->post('g-recaptcha-response');
            } else {
                $value = Yii::$app->request->get('g-recaptcha-response');
            }

            if (!$value) {
                return [$this->message, []];
            }
        }

        $request = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query([
            'secret' => $this->secret,
            'response' => $value,
            'remoteip' => Yii::$app->request->userIP,
        ]);

        // TODO: use yii2-httpclient
        $response = Json::decode(file_get_contents($request));

        if (isset($response['success'])) {
            return null;
        }

        return [$this->message, []];
    }

    /**
     * @return Recaptcha|null
     */
    public function getComponent()
    {
        if (Yii::$app->has($this->componentId)) {
            return Yii::$app->{$this->componentId};
        }
        return null;
    }
}
