<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 13.03.18
 * Time: 13:00
 */

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hall
 * @package AppBundle\Entity
 *
 * @ORM\Table(name="hall")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HallRepository")
 */
class Hall
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="hall_name")
     */
    private $hallName;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getHallName()
    {
        return $this->hallName;
    }

    /**
     * @param string $hallName
     */
    public function setHallName($hallName)
    {
        $this->hallName = $hallName;
    }


}