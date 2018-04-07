<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\i18n\PhpMessageSource;
use yii\web\JsExpression;

/**
 * Class Recaptcha
 * @package alexeevdv\recaptcha
 */
class Recaptcha extends Component
{
    /**
     * Site key
     * @var string
     */
    public $siteKey;

    /**
     * Secret key
     * @var string
     */
    public $secret;

    /**
     * Optional. Color theme of the widget
     * @var string
     */
    public $theme;

    /**
     * Optional. The type of CAPTCHA to serve
     * @var string
     */
    public $type;

    /**
     * Optional. The size of the widget
     * @var string
     */
    public $size;

    /**
     * Optional. The tabindex of the widget and challenge.
     * If other elements in your page use tabindex, it should be set to make user navigation easier.
     * @var integer
     */
    public $tabindex;

    /**
     * Optional. The name of your callback function, executed when the user submits a successful response.
     * The g-recaptcha-response token is passed to your callback.
     * @var string|JsExpression
     */
    public $callback;

    /**
     * Optional. The name of your callback function, executed when the reCAPTCHA response expires
     * and the user needs to re-verify.
     * @var string|JsExpression
     */
    public $expiredCallback;

    /**
     * Optional. The name of your callback function, executed when reCAPTCHA encounters an error
     * (usually network connectivity) and cannot continue until connectivity is restored. If you specify
     * a function here, you are responsible for informing the user that they should retry.
     * @var string|JsExpression
     */
    public $errorCallback;

    /**
     * I18n component
     * @var string|\yii\i18n\I18N|array
     */
    public $i18n = 'i18n';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initTranslations();

        if ($this->theme !== null && !in_array($this->theme, ['dark', 'light'])) {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong theme value "{value}". Only "dark" and "light" are allowed.', [
                'value' => $this->theme,
            ]));
        }

        if ($this->type !== null && !in_array($this->type, ['image', 'audio'])) {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong type value "{value}". Only "image" and "audio" are allowed.', [
                'value' => $this->type,
            ]));
        }

        if ($this->size !== null && !in_array($this->size, ['compact', 'normal'])) {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong size value "{value}". Only "compact" and "normal" are allowed.', [
                'value' => $this->size,
            ]));
        }

        parent::init();
    }

    /**
     * Initializes translations for component
     */
    protected function initTranslations()
    {
        /** @var \yii\i18n\I18N $i18n */
        $i18n = Instance::ensure($this->i18n, \yii\i18n\I18N::class);
        $i18n->translations['recaptcha*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages',
            'sourceLanguage' => 'en',
            'fileMap' => [
                'recaptcha' => 'recaptcha.php',
            ],
        ];
    }
}
