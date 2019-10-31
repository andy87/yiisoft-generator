<?php

namespace andy87\yii2\generator\console\models\generator;

use yii\gii\generators\crud\Generator;

/**
 * Class Model
 *
 *      Model
 *
 *  Класс для генерации Crud'ов
 *
 * @package console\models\generator
 */
class Crud extends Root
{
    /**
     * Crud constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->src .= '/crud/default/';
    }

    /**
     * @param string $tableName
     * @param object $data
     *
     * @return array
     */
    public function create( $tableName, $data )
    {
        $resp = parent::build( new Generator(), $tableName, $data );

        return $resp;
    }
}