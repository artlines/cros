<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 15.02.18
 * Time: 14:57
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;


/**
 * Stage
 *
 * @ORM\Table(name="tg_subscribe")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TgSubscribeRepository")
 */
class TgSubscribe
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="chat_id", type="integer")
     */
    private $chatId;

    /**
     * @var int
     *
     * @ORM\Column(name="lecture_id", type="integer")
     */
    private $lectureId;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getChatId()
    {
        return $this->chatId;
    }

    /**
     * @param int $chatId
     */
    public function setChatId($chatId)
    {
        $this->chatId = $chatId;
    }

    /**
     * @return int
     */
    public function getLectureId()
    {
        return $this->lectureId;
    }

    /**
     * @param int $lectureId
     */
    public function setLectureId($lectureId)
    {
        $this->lectureId = $lectureId;
    }



}