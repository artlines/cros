<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileManager
{
    /** @var Filesystem */
    protected $fs;

    /**
     * Path where file will be upload
     * @var string
     */
    protected $uploadPath;

    /**
     * Absolute path to public folder
     * @var string
     */
    protected $publicDir;

    /**
     * FileManager constructor.
     * @param Filesystem $filesystem
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(Filesystem $filesystem, ParameterBagInterface $parameterBag)
    {
        $this->fs = $filesystem;

        $this->uploadPath = '/uploads/crs-'.date('Y').'/';
        $this->publicDir  = $parameterBag->get('public_dir');
    }

    /**
     * @author Evgeny Nachuychenko e.nachuychenko@nag.ru
     * @param $base64_string
     * @param null $type
     * @return string
     */
    public function uploadBase64($base64_string, $type = null)
    {
        $uploadPath = $type ? $this->uploadPath.$type.'/' : $this->uploadPath;

        $data = explode( ',', $base64_string);

        /** Get extension */
        preg_match("/data:(.*)\/(.*);base64/", $data[0], $matches);
        $ext = $matches[2];

        $filename = substr(hash('sha256', $data[1].microtime()), 0, 20).'.'.$ext;

        if (!$this->fs->exists($this->publicDir.$uploadPath)) {
            $this->fs->mkdir($this->publicDir.$uploadPath);
        }

        $file = fopen($this->publicDir.$uploadPath.$filename, 'a+');
        fwrite($file, base64_decode($data[1]));
        fclose($file);

        return $uploadPath.$filename;
    }
}