<?php

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
     * @Column(type="string",nullable=true,options={"widget"="image"})
     * @var string
     * @ NOTE has not getter/setter methods
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
	}

	public function __toString()
	{
		return $this->name;
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
}
