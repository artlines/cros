<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Lecture;
use AppBundle\Entity\TgChat;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class TgChatManager
{
    /** @var EntityManager */
    private $em;

    /**
     * TgChatManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $bot_token)
    {
        $this->em = $entityManager;
        $this->bot_token = $bot_token;
    }

    /**
     * @param TgChat $tgChat
     * @param integer $lectureId
     * @return bool
     * @throws EntityNotFoundException
     */
    public function subscribeLecture($tgChat, $lectureId)
    {
        $lecture = $this->_findLecture($lectureId);
        if (!$tgChat->getLectures()->contains($lecture)) {
            $tgChat->addLecture($lecture);
        }

        try {
            $this->em->persist($tgChat);
            $this->em->persist($lecture);
            $this->em->flush();
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param TgChat $tgChat
     * @param integer $lectureId
     * @return bool
     * @throws EntityNotFoundException
     */
    public function unsubscribeLecture($tgChat, $lectureId)
    {
        $lecture = $this->_findLecture($lectureId);
        if ($tgChat->getLectures()->contains($lecture)) {
            $tgChat->removeLecture($lecture);
        }

        try {
            $this->em->persist($tgChat);
            $this->em->persist($lecture);
            $this->em->flush();
        } catch (\Doctrine\ORM\OptimisticLockException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function checkAndNotifySubscribes()
    {
        $deb = $this->em->getRepository('AppBundle:TgChat')->findBy(['allowNotify' => true]);

        return $deb;
    }

    /**
     * @param $lectureId
     * @return Lecture
     * @throws EntityNotFoundException
     */
    private function _findLecture($lectureId)
    {
        $lecture = $this->em->getRepository('AppBundle:Lecture')->find($lectureId);
        if (!$lecture) {
            throw new EntityNotFoundException("Lecture not found.");
        }
        return $lecture;
    }

    private function _sendMsg($tgChat, $msg)
    {
        $bot = new \Telegram($this->bot_token);


    }

}
