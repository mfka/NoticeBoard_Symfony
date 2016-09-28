<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Category {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Notice", mappedBy="categories")
     */
    private $notices;
    
    

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Category
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->notices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add notices
     *
     * @param \AppBundle\Entity\Notice $notices
     * @return Category
     */
    public function addNotice(\AppBundle\Entity\Notice $notices)
    {
        $this->notices[] = $notices;

        return $this;
    }

    /**
     * Remove notices
     *
     * @param \AppBundle\Entity\Notice $notices
     */
    public function removeNotice(\AppBundle\Entity\Notice $notices)
    {
        $this->notices->removeElement($notices);
    }

    /**
     * Get notices
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotices()
    {
        return $this->notices;
    }

    public function __toString(){
        return $this->name;
    }
}
