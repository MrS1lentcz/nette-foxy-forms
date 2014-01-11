<?php

# @Foxy\Annotations\Widget(name="upload")

/**
 * Category Entity
 *
 * @Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @Table(name="category")
 */
class Category
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string",unique=true,nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @Column(type="string",nullable=true)
     * @Widget(name="upload")
     * @var string
     */
    protected $image;

    /**
     * @OneToMany(targetEntity="Category", mappedBy="category")
     * @var \Doctrine\Common\Collections\ArrayCollection()
     */
    protected $products;



	public function __construct()
	{
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
		$this->createdDatetime = new \Datetime();
	}

	public function __toString()
	{
		return (string) $this->name;
	}

	public function getId()
	{
		return $this->id;
	}

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getCreatedDatetime()
    {
        return $this->createdDatetime;
    }

    public function setCreatedDatetime($createdDatetime)
    {
        $this->createdDatetime = $createdDatetime;
        return $this;
    }
}
