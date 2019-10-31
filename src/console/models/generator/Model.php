<?php

namespace andy87\yii2\console\models\generator;

use yii\gii\generators\model\Generator;

/**
 * Class Model
 *
 *      Model
 *
 *  Класс для генерации Model'ей
 *
 * @package console\models\generator
 */
class Model extends Root
{
    /**
     * Model constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);


    }

    /**
     * @param string $tableName
     * @param object $data
     * @return array
     */
    public function create( $tableName, $data )
    {
        $resp = parent::build( new Generator(), $tableName, $data );

        return $resp;
    }
}