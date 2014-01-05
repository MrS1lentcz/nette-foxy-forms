<?php

/**
 * Product Entity
 *
 * @Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @Table(name="product")
 */
class Product
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string",nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @Column(type="integer")
     * @var integer
     */
    protected $price;

    /**
     * @ManyToOne(targetEntity="Category", inversedBy="products")
     * @JoinColumn(name="category_id", referencedColumnName="id")
     * @var Category
     */
    protected $category;

	/**
	 * @ManyToMany(targetEntity="Parameter", inversedBy="products")
	 * @var \Doctrine\Common\Collections\ArrayCollection()
	 */
	protected $parameters;


	public function __construct()
	{
		$this->price = 1;
		$this->parameters = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function getId()
	{
		return $this->id;
	}

	public function __toString()
	{
		return $this->name;
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

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    public function getParameters()
    {
        return $this->parameters;
    }
}
