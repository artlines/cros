<?php

namespace App\Manager;

use AppBundle\Entity\Lecture;
use AppBundle\Entity\TgChat;
use AppBundle\Service\Telegram;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Twig_Environment;

class TgChatManager
{
    /** @var EntityManager */
    private $em;

    /** @var Twig_Environment */
    private $twig;

    /** @var \Telegram  */
    private $bot;

    /**
     * TgChatManager constructor.
     * @param EntityManager $entityManager
     * @param $bot_token
     * @throws \Exception
     */
    public function __construct(EntityManager $entityManager, $bot_token, Twig_Environment $twig)
    {
        $this->em = $entityManager;
        $this->twig = $twig;

        if (!$bot_token) {
            throw new \Exception("Bot token is not set in parameters.yml");
        }
        $this->bot = new Telegram($bot_token);
    }

    /**
     * Add Lecture with lectureId to TgChat subscribes
     *
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
     * Remove Lecture with lectureId from TgChat subscribes
     *
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

    /**
     * Look for coming soon lectures and notify TgChats about lecture start time
     */
    public function checkAndNotifySubscribes()
    {
        date_default_timezone_set("Europe/Moscow");
        $moscow_tz = new \DateTimeZone('Europe/Moscow');
        $time_now = (new \DateTime())->setTimezone($moscow_tz);
        $time_plus_15 = (new \DateTime('+ 16 minutes'))->setTimezone($moscow_tz);

        $qb = $this->em->getRepository('App:Lecture')->createQueryBuilder('l');
        $lectures = $qb->where(
            "l.date = ?1 AND l.startTime >= ?2 AND l.startTime <= ?3"
        )->setParameters(array(
            '1' => $time_now->format("Y-m-d"),
            '2' => $time_now->format("H:i"),
            '3' => $time_plus_15->format("H:i")
        ))->getQuery()->getResult();

        $lecturesCount = count($lectures);
        $chatsNotified = 0;
        /** @var Lecture $lecture */
        foreach ($lectures as $lecture)
        {
            $lectureDate = $lecture->getDate();
            $lectureStart = $lecture->getStartTime()->setDate($lectureDate->format("Y"), $lectureDate->format("m"), $lectureDate->format("d"));
            $minDiff = date_diff($time_now, $lectureStart)->i;

            /** @var TgChat $chat */
            foreach ($lecture->getChats() as $chat)
            {
                if ($chat->getIsActive() && $chat->isAllowNotify()) {
                    $this->_sendMsg(
                        $chat,
                        $this->twig->render('telegram_bot/notify.html.twig', [
                            'minDiff' => $minDiff,
                            'startTime' => $lectureStart->format("H:i"),
                            'lecture' => $lecture
                        ])
                    );
                    $chatsNotified++;
                }
            }
        }

        return [$lecturesCount, $chatsNotified];
    }

    /**
     * Update current state of TgChat
     *
     * @param TgChat $tgChat
     * @param array $newState
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateState($tgChat, $newState)
    {
        $tgChat->setState($newState);
        $this->em->persist($tgChat);
        $this->em->flush();
    }

    /**
     * Reset state of TgChat
     *
     * @param TgChat $tgChat
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function resetState($tgChat)
    {
        $this->updateState($tgChat, array());
    }

    /**
     * @param $lectureId
     * @return Lecture
     * @throws EntityNotFoundException
     */
    private function _findLecture($lectureId)
    {
        $lecture = $this->em->getRepository('App:Lecture')->find($lectureId);
        if (!$lecture) {
            throw new EntityNotFoundException("Lecture not found.");
        }
        return $lecture;
    }

    /**
     * @param TgChat $tgChat
     * @param $msg
     */
    private function _sendMsg($tgChat, $msg)
    {
        $this->bot->sendMessage([
            'chat_id' => $tgChat->getChatId(),
            'parse_mode' => 'HTML',
            'text' => $msg
        ]);
    }

}
