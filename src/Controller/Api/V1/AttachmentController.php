<?php

namespace App\Controller\Api\V1;

use App\Service\FileManager;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Use to save files that gives from CKEditor downloader
 *
 * Class AttachmentController
 * @package App\Controller\Api\V1
 *
 * @Route("/api/v1/", name="api_v1__comment__")
 * @IsGranted("ROLE_CMS_USER")
 */
class AttachmentController extends AbstractController
{
    /**
     * @Route("attachment/upload_public", methods={"POST"})
     *
     * @param Request $request
     * @param FileManager $fileManager
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function uploadPublicFile(Request $request, FileManager $fileManager, LoggerInterface $logger)
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('upload', null);

        if (!$file) {
            return $this->error('Не передан файл.');
        }

        try {
            $pathname = $fileManager->uploadFile($file);
        } catch (\Exception $e) {
            $logger->error(__FILE__.' | Throw exception when try to move file in upload directory', [
                'error'     => $e->getMessage(),
            ]);
            return $this->error('Не удалось сохранить файл.');
        }

        return $this->success($pathname);
    }

    /**
     * Generate success response
     *
     * @param   string  $url    Uploaded file absolute URL
     *
     * @return  \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function success($url)
    {
        return $this->json([
            'uploaded'  => true,
            'url'       => $url,
        ]);
    }

    /**
     * Generate error response
     *
     * @param $msg
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function error($msg)
    {
        return $this->json([
            'uploaded'  => false,
            'error'     => [
                'message'   => $msg
            ],
        ]);
    }
}