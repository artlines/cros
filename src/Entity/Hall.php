<?php
/**
 * Created by PhpStorm.
 * User: alf1kk
 * Date: 13.03.18
 * Time: 13:00
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Hall
 * @package App\Entity
 *
 * @ORM\Table(name="hall")
 * @ORM\Entity(repositoryClass="App\Repository\HallRepository")
 */
class Hall
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
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