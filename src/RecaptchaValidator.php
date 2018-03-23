<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\Json;
use yii\validators\Validator;
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
        /** @var Request $request */
        $request = Instance::ensure('request', Request::class);
        if (empty($value)) {
            if ($request->getIsPost()) {
                $value = $request->post('g-recaptcha-response');
            } else if ($request->getIsGet()) {
                $value = $request->get('g-recaptcha-response');
            }

            if (!$value) {
                return [$this->message, []];
            }
        }

        $request = 'https://www.google.com/recaptcha/api/siteverify?' . http_build_query([
            'secret' => $this->secret,
            'response' => $value,
            'remoteip' => $request->getUserIP(),
        ]);

        // TODO: use yii2-httpclient
        $response = Json::decode(file_get_contents($request));

        if (isset($response['success'])) {
            return null;
        }

        return [$this->message, []];
    }
}
