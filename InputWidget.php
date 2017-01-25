<?php

namespace alexeevdv\recaptcha;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class InputWidget extends \yii\widgets\InputWidget
{
    
    /**
     * HTML input name
     * @var string
     */
    public $name = "recaptcha";

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
     * Html attributes
     * @var array
     */
    public $options = [];

    const JS_API_FILE = "https://www.google.com/recaptcha/api.js";
    
    public function run()
    {
        if(empty($this->siteKey))
        {
            if (!Yii::$app->has("recaptcha") || !strlen(Yii::$app->recaptcha->siteKey))
            {
                throw new InvalidConfigException("`siteKey` param is required");
            }
            $this->siteKey = Yii::$app->recaptcha->siteKey;
        }

        $this->view->registerJsFile(self::JS_API_FILE);
        
        $options = ArrayHelper::merge([
            "class" => "g-recaptcha",
            "data-sitekey" => $this->siteKey,
        ], $this->options);

        return Html::tag("div", "", $options);
    }
}
