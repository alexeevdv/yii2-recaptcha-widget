<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RecaptchaWidget extends \yii\widgets\InputWidget
{
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

    const JS_API_URL = 'https://www.google.com/recaptcha/api.js';
    
    public function init()
    {
        parent::init();
        
        if($this->siteKey === null )
        {
            if ($this->component === null || $this->component->siteKey === null)
            {
                throw new InvalidConfigException(Yii::t('recaptcha', '"siteKey" param is required.'));
            }
        }
        
        if ($this->theme !== null && !in_array($this->theme, ['dark', 'light']))
        {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong theme value "{value}". Only "dark" and "light" are allowed.', [
                'value' => $this->theme,
            ]));
        }
        
        if ($this->type !== null && !in_array($this->type, ['image', 'audio']))
        {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong type value "{value}". Only "image" and "audio" are allowed.', [
                'value' => $this->type,
            ]));
        }
        
        if ($this->size !== null && !in_array($this->size, ['compact', 'normal']))
        {
            throw new InvalidConfigException(Yii::t('recaptcha', 'Wrong size value "{value}". Only "compact" and "normal" are allowed.', [
                'value' => $this->size,
            ]));
        }
    }
    
    protected function getLanguageSuffix()
    {
        $currentAppLanguage = Yii::$app->language;
        $langsExceptions = ['zh-CN', 'zh-TW', 'zh-TW'];
        if (strpos($currentAppLanguage, '-') === false) {
            return $currentAppLanguage;
        }
        if (in_array($currentAppLanguage, $langsExceptions)) {
            return $currentAppLanguage;
        } else {
            return substr($currentAppLanguage, 0, strpos($currentAppLanguage, '-'));
        }
    }

    public function run()
    {        
        $this->view->registerJsFile(self::JS_API_URL);
        
        $this->view->registerJsFile(
            self::JS_API_URL . '?hl=' . $this->getLanguageSuffix(),
            [
                'position' => $this->view::POS_HEAD,
                'async' => true,
                'defer' => true
            ]
        );

        $options = [
            "class" => "g-recaptcha",
            "data-sitekey" => $this->getParam('siteKey'),
        ];

        foreach(['theme', 'type', 'size', 'tabindex', 'callback'] as $param)
        {
            if ($this->getParam($param) !== null)
            {
                $options['data-'.$param] = $this->getParam($param);
            }
        }
        if ($this->getParam('expiredCallback') !== null)
        {
            $options['data-expired-callback'] = $this->getParam('expiredCallback');
        }
        
        $options = ArrayHelper::merge($options, $this->options);

        return Html::tag("div", "", $options);
    }

    public function getComponent()
    {
        if (!Yii::$app->has($this->componentId))
        {
            return null;
        }
        return Yii::$app->{$this->componentId};
    }

    public function getParam($name)
    {
        if ($this->{$name} !== null)
        {
            return $this->{$name};
        }
        if ($this->component !== null)
        {
            return $this->component->{$name};
        }
        return null;
    }
}
