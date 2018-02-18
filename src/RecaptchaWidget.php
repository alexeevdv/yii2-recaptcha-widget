<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class RecaptchaWidget
 * @package alexeevdv\recaptcha
 */
class RecaptchaWidget extends InputWidget
{
    /**
     * @var string
     */
    public $apiJs = 'https://www.google.com/recaptcha/api.js';

    /**
     * Site key
     * @var string
     */
    public $siteKey;

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
     * Recaptcha component
     * @var string|array|Recaptcha
     */
    public $component = 'recaptcha';

    /**
     * Html attributes
     * @var array
     */
    public $options = [];

    /**
     * Forces the widget to render in a specific language
     * @var string
     */
    public $language;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        try {
            $component = Instance::ensure($this->component, Recaptcha::class);
            Yii::configure($this, ArrayHelper::merge(
                array_filter([
                    'siteKey' => $component->siteKey,
                    'theme' => $component->theme,
                    'type' => $component->type,
                    'size' => $component->size,
                    'tabindex' => $component->tabindex,
                    'callback' => $component->callback,
                    'expiredCallback' => $component->expiredCallback,
                ]),
                array_filter([
                    'siteKey' => $this->siteKey,
                    'theme' => $this->theme,
                    'type' => $this->type,
                    'size' => $this->size,
                    'tabindex' => $this->tabindex,
                    'callback' => $this->callback,
                    'expiredCallback' => $this->expiredCallback,
                ])
            ));
        } catch (InvalidConfigException $e) {
        }

        if ($this->siteKey === null) {
            throw new InvalidConfigException(Yii::t('recaptcha', '"siteKey" param is required.'));
        }

        if ($this->theme !== null) {
            if (!in_array($this->theme, ['dark', 'light'])) {
                throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong theme value "{value}". Only "dark" and "light" are allowed.', [
                    'value' => $this->theme,
                ]));
            }
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

        // If language is not set then we trying to guess it from application settings
        if ($this->language === null) {
            $this->language = $this->getApplicationLanguage();
        }

        parent::init();
    }

    /**
     * @return string
     */
    protected function getApplicationLanguage()
    {
        // According to docs https://developers.google.com/recaptcha/docs/language
        $langsExceptions = ['zh-CN', 'zh-TW', 'zh-TW', 'en-GB', 'fr-CA', 'de-AT', 'de-CH', 'pt-BR', 'pt-PT', 'es-419'];

        $language = ArrayHelper::getValue(Yii::$app, 'language', 'en-US');
        if (strpos($language, '-') === false) {
            return $language;
        }

        if (in_array($language, $langsExceptions)) {
            return $language;
        }

        return substr($language, 0, strpos($language, '-'));
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $url = $this->apiJs;

        if ($this->language !== false) {
            $url .= '?hl=' . $this->language;
        }

        $this->view->registerJsFile($url, [
            'position' => View::POS_HEAD,
            'async' => true,
            'defer' => true
        ]);

        $options = [
            'class' => 'g-recaptcha',
            'data-sitekey' => $this->siteKey,
        ];

        foreach (['theme', 'type', 'size', 'tabindex', 'callback'] as $param) {
            $options['data-' . $param] = $this->$param;
        }

        if ($this->expiredCallback !== null) {
            $options['data-expired-callback'] = $this->expiredCallback;
        }

        $options = ArrayHelper::merge($options, $this->options);

        return Html::tag('div', '', $options);
    }
}
