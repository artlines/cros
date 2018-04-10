<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Lecture;
use AppBundle\Entity\TgChat;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;

class TgSubscribeManager
{
    /** @var EntityManager */
    private $em;

    /**
     * TgSubscribeManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param integer $tgChatId
     * @param integer $lectureId
     * @return bool
     */
    public function subcribe($tgChatId, $lectureId)
    {
        try {
            /** @var Lecture $lecture */
            $lecture = $this->_findLecture($lectureId);
            /** @var TgChat $tgChat */
            $tgChat = $this->_findTgChat($tgChatId);
        } catch (EntityNotFoundException $e) {
            return false;
        }

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
     * @param integer $tgChatId
     * @param integer $lectureId
     * @return bool
     */
    public function unsubcribe($tgChatId, $lectureId)
    {
        try {
            /** @var Lecture $lecture */
            $lecture = $this->_findLecture($lectureId);
            /** @var TgChat $tgChat */
            $tgChat = $this->_findTgChat($tgChatId);
        } catch (EntityNotFoundException $e) {
            return false;
        }

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

    /**
     * @param $lectureId
     * @return null|Lecture
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

    /**
     * @param $tgChatId
     * @return TgChat
     * @throws EntityNotFoundException
     */
    private function _findTgChat($tgChatId)
    {
        $tgChat = $this->em->getRepository('AppBundle:TgChat')->findOneBy(['chatId' => $tgChatId]);
        if (!$tgChat) {
            throw new EntityNotFoundException("TgChat not found.");
        }
        return $tgChat;
    }

}
