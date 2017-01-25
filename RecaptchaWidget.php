<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class RecaptchaWidget extends \yii\widgets\InputWidget
{
    /**
     * Recaptcha component name
     * @var string
     */
    public $component = "recaptcha";

    /**
     * Site key
     * @var string 
     */
    public $siteKey;

    /**
     * Html attributes
     * @var array
     */
    public $options = [];

    const JS_API_FILE = "https://www.google.com/recaptcha/api.js";
    
    public function run()
    {        
        if(empty($this->siteKey))
        {
            if (!Yii::$app->has($this->component) || !strlen(Yii::$app->{$this->component}->siteKey))
            {
                throw new InvalidConfigException("`siteKey` param is required");
            }
            $this->siteKey = Yii::$app->{$this->component}->siteKey;
        }

        $this->view->registerJsFile(self::JS_API_FILE);
        
        $options = ArrayHelper::merge([
            "class" => "g-recaptcha",
            "data-sitekey" => $this->siteKey,
        ], $this->options);

        return Html::tag("div", "", $options);
    }
}
