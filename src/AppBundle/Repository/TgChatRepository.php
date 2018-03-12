<?php

namespace AppBundle\Repository;

class TgChatRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param integer $chat_id
     * @return object|null
     */
    public function findOneByChatId($chat_id)
    {
        $query = $this->getEntityManager()
            ->createQuery('
                    SELECT tch FROM AppBundle:TgChat tch
                    WHERE tch.chatId = :chatId
                ')->setParameter('chatId', $chat_id);
        try{
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e){
            return null;
        }
    }
}
