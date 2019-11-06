<?php

namespace console\controllers;

use andy87\yii2\generator\console\controllers\GeneratorController as Source;

/**
 * Class GenerateController
 *
 *      Controller
 *
 * @package console\controllers
 */
class GenerateController extends Source
{
    /**
     *      Init
     */
    public function init ()
    {
        parent::init();

        $this->config = [
            'model'         => (object) [
                'modelClass'            => '#TableName#',
                'ns'                    => 'common\\models\\items\\source',
                'baseClass'             => 'common\\models\\core\\BaseModel'
            ],
            'crud'          => (object) [
                'modelClass'            => 'common\\models\\items\\source\\#TableName#',
                'viewPath'              => 'backend\views\source\#table-name#',
                'baseControllerClass'   => 'backend\\controllers\\core\\BackendController',
                'searchModelClass'      => 'backend\models\search\#TableName#Search',
                'controllerClass'       => 'backend\controllers\source\#TableName#Controller',
            ]
        ];

    }
    


    // php yii generate/items
    /** генерация файлов по шаблону `/common/tpl/custom.php`
     *
     * @param string $items
     */
    public function actionItems( $items = 'all' )
    {
        $data   = (object) [
            'ns'                => 'common\\models\\items',
            'modelClass'        => '#TableName#',
            'baseClass'         => 'common\\models\\items\\source\\#TableName#',
        ];

        $this->createCustomModel( $items, $data, 'custom' );
    }
}