<?php

namespace apiadmin\modules;

/**
 * module definition class
 */
class module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'apiadmin\modules\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        
        parent::init();
        
        // custom initialization code goes here
    } 
}
