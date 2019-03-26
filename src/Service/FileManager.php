<?php

namespace App\Service;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileManager
{
    /** @var Filesystem */
    protected $fs;

    /** @var LoggerInterface */
    protected $logger;

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
     * @param LoggerInterface $logger
     */
    public function __construct(Filesystem $filesystem, ParameterBagInterface $parameterBag, LoggerInterface $logger)
    {
        $this->fs = $filesystem;
        $this->logger = $logger;

        $this->uploadPath = '/uploads/cros-'.date('Y').'/';
        $this->publicDir  = $parameterBag->get('public_dir');
    }

    public function uploadFile(UploadedFile $file, $type = null)
    {
        $now = new \DateTime();

        $uploadPath = $type
            ? $this->uploadPath.$type.'/'
            : $this->uploadPath . $now->format("Y") . '/' . $now->format("m") . '/';

        $filename = $file->getClientOriginalName() . '.'
            . hash('sha256', $file->getClientOriginalName()) . '.' . $file->guessExtension();

        if (!$this->fs->exists($this->publicDir.$uploadPath)) {
            $this->fs->mkdir($this->publicDir.$uploadPath);
        }

        $pathname = $uploadPath.$filename;
        $savePath = $this->publicDir.$uploadPath;

        $file->move($savePath, $filename);

        return $pathname;
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