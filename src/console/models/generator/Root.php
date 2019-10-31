<?php

namespace andy87\yii2\console\models\generator;

use yii\BaseYii;
use yii\base\Model;

use andy87\yii2\console\components\Generator;

/**
 * Class Model
 *
 *      Model
 *
 *  Общий класс для генерации Model'ей & Crud'ов
 *
 * Children:
 *      \console\models\generator\Model
 *      \console\models\generator\Crud
 *
 * @package console\models\generator
 */
class Root extends Model
{
    /**
     * @var string;
     */
    public $src = '@yii/../yii2-gii/src/generators';

    /**
     * Root constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->src = BaseYii::getAlias( $this->src );
    }

    /**
     * @param \yii\gii\Generator $generator
     * @param string $tableName
     * @param object $data
     *
     * @return array
     */
    public function build( $generator, $tableName, $data )
    {
        $data = Generator::insertTableCase( $data, $tableName );

        foreach( $data as $key => $val )
        {
            $generator->{$key} = $val;
        }

        $files  = $generator->generate();

        $resp   = $this->generate( $files );

        return $resp;
    }

    /**
     * @param array $files
     *
     * @return array
     */
    public function generate( $files = [] )
    {
        $resp   = [];
        $file   = new File();

        foreach ( $files as $item )
        {
            $result = $file->generate( $item );

            $resp[] = $result;
        }

        return $resp;
    }

    /**
     * @param $path
     * @param string $break
     */
    public function createDir( $path, $break = '.php' )
    {
        $DS     = DIRECTORY_SEPARATOR;
        $path   = str_replace(['//','\\','/'], $DS, $path);
        $map    = explode( $DS, $path );
        $root   = '';

        foreach( $map as $dir )
        {
            if ( strpos( $dir, $break ) !== false ) break;

            $root   = $root . $dir . $DS;

            if( !is_dir( $root ) AND !is_file( $root ) )
            {
                mkdir( $root );
            }
        }
    }

    /**
     * @return false|null|string
     */
    public static function getAllTables()
    {
        try {

            $result = \Yii::$app->db->createCommand("SHOW TABLES")->queryColumn();

            unset( $result['migration'] );

            return $result;

        } catch ( \Exception $e ) {

            echo "Catch " . __FUNCTION__. ". Error : " . $e->getMessage();
            exit();
        }
    }

    /**
     * @param $sql string
     *
     * @return \yii\db\Command
     */
    public function db( $sql )
    {
        try {

            return \Yii::$app->db->createCommand( $sql );

        } catch ( \Exception $e ) {

            echo "Catch " . __FUNCTION__. ". Error : " . $e->getMessage();
            exit();
        }
    }
}