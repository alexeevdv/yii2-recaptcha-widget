<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\i18n\PhpMessageSource;

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
     * Optional. The name of your callback function to be executed when the user submits a successful CAPTCHA response.
     * The user's response, g-recaptcha-response, will be the input for your callback function.
     * @var string
     */
    public $callback;

    /**
     * Optional. The name of your callback function to be executed when the recaptcha
     * response expires and the user needs to solve a new CAPTCHA.
     * @var string
     */
    public $expiredCallback;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        // Initialize translations
        Yii::$app->i18n->translations['recaptcha*'] = [
            'class' => PhpMessageSource::class,
            'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages',
            'sourceLanguage' => 'en',
            'forceTranslation' => true,
            'fileMap' => [
                'recaptcha' => 'recaptcha.php',
            ],
        ];

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
}
