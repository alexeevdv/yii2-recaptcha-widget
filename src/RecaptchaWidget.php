<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
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
    const JS_API_URL = 'https://www.google.com/recaptcha/api.js';

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
     * Recaptcha component name
     * @var string
     */
    public $componentId = 'recaptcha';

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
        if ($this->siteKey === null) {
            if ($this->component === null || $this->component->siteKey === null) {
                throw new InvalidConfigException(Yii::t('recaptcha', '"siteKey" param is required.'));
            }
        }

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

        // If language is not set then we trying to guess it from application settings
        if ($this->language === null) {
            $this->language = $this->getLanguage();
        }

        parent::init();
    }

    /**
     * @return string
     */
    protected function getLanguage()
    {
        // According to docs https://developers.google.com/recaptcha/docs/language
        $langsExceptions = ['zh-CN', 'zh-TW', 'zh-TW', 'en-GB', 'fr-CA', 'de-AT', 'de-CH', 'pt-BR', 'pt-PT', 'es-419'];

        $language = Yii::$app->language;
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
        $url = static::JS_API_URL;

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
            'data-sitekey' => $this->getParam('siteKey'),
        ];

        foreach (['theme', 'type', 'size', 'tabindex', 'callback'] as $param) {
            if ($this->getParam($param) !== null) {
                $options['data-' . $param] = $this->getParam($param);
            }
        }

        if ($this->getParam('expiredCallback') !== null) {
            $options['data-expired-callback'] = $this->getParam('expiredCallback');
        }

        $options = ArrayHelper::merge($options, $this->options);

        return Html::tag('div', '', $options);
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

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getParam($name)
    {
        if ($this->{$name} !== null) {
            return $this->{$name};
        }
        if ($this->component !== null) {
            return $this->component->{$name};
        }
        return null;
    }
}
