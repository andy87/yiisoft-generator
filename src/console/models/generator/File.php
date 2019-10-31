<?php

namespace andy87\yii2\generator\console\models\generator;

use yii\base\Model;

/**
 * Class File
 *
 *      Model
 *
 *  Писал его давно - нихрена не помню как оно работает.
 *      Но точно работает с файлами: открывает, создаёт, обновляет, ! НО не удаляет.
 *
 *      Создаёт все дирректории на пути к файлу если они отсутствуют.
 *
 * @package console\models\generator
 *
 */
class File extends Model
{
    /**
     * @var string Путь к файлу
     */
    public $path        = '';

    /**
     * @var string Дирректория файла
     */
    public $dir         = '';

    /**
     * @var string Имя файла (до последней точки)
     */
    public $baseName    = '';

    /**
     * @var string Имя файла (до последней точки)
     */
    public $name        = '';

    /**
     * @var string Расширение файла
     */
    public $extend      = '';

    /**
     * @var string Контент файла
     */
    public $content     = '';

    /**
     * @var string Хэш файла
     */
    public $hash        = '';

    /**
     * @var array Ошибки в работе кода
     */
    public $error       = [];

    /**
     * @param $path
     */
    public function open( $path )
    {
        if ( !empty( $path ) )
        {
            if ( file_exists( $path ) )
            {
                $this->content = file_get_contents( $path );

                $this->setups();

            } else {

                $this->error = [
                    'status'        => "error",
                    'description'   => 'File not Exists'
                ];
            }

        } else {

            $this->error = [
                'status'        => "error",
                'description'   => '$path is required'
            ];
        }
    }

    /**
     *
     */
    public function setups()
    {
        $data = pathinfo($this->path);

        $this->dir      = $data['dirname'];
        $this->baseName = $data['basename'];
        $this->name     = $data['filename'];
        $this->extend   = $data['extension'];
    }

    /**
     * @param string $path
     * @param string $content
     *
     * @return array
     */
    public function create( $path = '', $content = '' )
    {
        if ( strlen($path) )
        {
            try {

                $this->path     = $path;

                $this->update( $content );

            } catch ( \Exception $e ) {

                $this->error    = [
                    'status'        => "catch",
                    'description'   => $e->getMessage()
                ];
            }

        } else {

            $this->error    = [
                'status'        => "error",
                'description'   => "$path is required"
            ];
        }

        $this->save();

        return [
            'path'      => $path,
            'status'    => ( count($this->error) ) ? $this->error : 'OK'
        ];
    }

    /**
     * @param $prototype
     *
     * @return File
     */
    public function generate( $prototype )
    {
        return $this->create( $prototype->path, $prototype->content );
    }

    /**
     * @param string $content
     *
     * @return File
     */
    public function update( $content )
    {
        if ( !empty( $this->path ) )
        {
            $this->content = $content;

            $this->save();

        } else {

            $this->error = [
                'status'        => "error",
                'description'   => '$path is required'
            ];
        }

        return $this;
    }

    /**
     * @return File
     */
    public function save()
    {
        $this->path = str_replace(['\\', '//','--'], ['/','/','-'], $this->path );

        if ( !empty( $this->path ) )
        {
            try {

                $this->createDir( $this->path );

                $resp   = fopen( $this->path, "w" );

                $result = ( file_put_contents( $this->path, $this->content ) ) ? true : false;

                fclose( $resp );

            } catch ( \Exception $e ) {

                echo $e->getMessage();

                exit();
            }

            if ( !$result )
            {
                $this->error = [
                    'status'        => "error",
                    'description'   => 'File not Saved'
                ];
            }

        } else {

            $this->error = [
                'status'        => "error",
                'description'   => '$path is required'
            ];
        }

        return $this;
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

                chmod( $root, 755 );
            }
        }
    }
}
