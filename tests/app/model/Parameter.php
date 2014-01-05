<?php

/**
 * Product Entity
 *
 * @Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @Table(name="parameter")
 */
class Parameter
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string")
     * @var string
     */
    protected $key;

    /**
     * @Column(type="string",length=50)
     * @var integer
     */
    protected $value;

	/**
     * @ManyToMany(targetEntity="Product", mappedBy="parameters")
     * @JoinTable(name="product_parameter",
     *      joinColumns={@JoinColumn(name="product_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="parameter_id", referencedColumnName="id")}
     *      )
     * @var \Doctrine\Common\Collections\ArrayCollection()
     */
    protected $products;


	public function __construct()
	{
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function getId()
	{
		return $this->id;
	}

	public function __toString()
	{
		return $this->key . ' ' . $this->value;
	}

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getProducts()
    {
        return $this->products;
    }
}
