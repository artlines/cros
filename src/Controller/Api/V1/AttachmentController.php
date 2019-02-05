<?php

namespace App\Controller\Api\V1;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
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
     * @param ContainerInterface $container
     * @param LoggerInterface $logger
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @throws \Exception
     */
    public function uploadPublicFile(Request $request, ContainerInterface $container, LoggerInterface $logger)
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('upload', null);

        if (!$file) {
            return $this->error('Не передан файл.');
        }

        try {
            $attach_params = $container->getParameter('attachments');
        } catch (InvalidArgumentException $e) {
            return $this->error('На сервере не установлены необходимые параметры для загрузки файлов.');
        }

        $uploadsPath = $attach_params['uploads_path'] ?? null;
        $publicSavePath = $attach_params['public_save_path'] ?? null;
        if (!$uploadsPath || !$publicSavePath) {
            return $this->error('На сервере не установлены необходимые параметры для загрузки файлов.');
        }

        $now = new \DateTime();
        $path = '/' . $now->format("Y") . '/' . $now->format("m");
        $filename = $file->getClientOriginalName() . '.'
            . hash('sha256', $file->getClientOriginalName()) . '.' . $file->guessExtension();

        $pathname = '/' . $uploadsPath . $path . '/' . $filename;
        $savePath = $publicSavePath . $path;

        try {
            $res = $file->move($savePath, $filename);
        } catch (\Exception $e) {
            $logger->error(__FILE__.' | Throw exception when try to move file in upload directory', [
                'error'     => $e->getMessage(),
                'save_path' => $savePath,
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