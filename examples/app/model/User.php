<?php

/**
 * User Entity
 *
 * @Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @Table(name="foxy_user")
 */
class User
{
    /**
     * @Id @Column(type="integer") @GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @Column(type="string",unique=true,options={"widget"="email"})
     * @var string
     */
    protected $email;

    /**
     * @Column(type="string",nullable=true,options={"widget"="password"})
     * @var string
     */
    protected $password;

    /**
     * @Column(type="datetime",name="created_datetime")
     * @var Datetime
     */
    protected $createdDatetime;

    /**
     * @Column(type="time",name="work_time_from")
     * @var Datetime
     */
    protected $workTimeFrom;

    /**
     * @Column(type="time",name="work_time_to")
     * @var Datetime
     */
    protected $workTimeTo;

    /**
     * @Column(type="date",nullable=true,name="worst_day_in_year")
     * @var Datetime
     */
	protected $worstDayInYear;

    /**
     * @OneToMany(targetEntity="Product", mappedBy="author")
     * @var \Doctrine\Common\Collections\ArrayCollection()
     */
    protected $products;



	public function __construct()
	{
		$this->products = new \Doctrine\Common\Collections\ArrayCollection();
		$this->createdDatetime = new \Datetime();
		$this->workTimeFrom = new Datetime();
	}

	public function __toString()
	{
		return $this->email;
	}

	public function getId()
	{
		return $this->id;
	}

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
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

    public function getWorkTimeFrom()
    {
        return $this->workTimeFrom;
    }

    public function setWorkTimeFrom($workTimeFrom)
    {
        $this->workTimeFrom = $workTimeFrom;
        return $this;
    }

    public function getWorkTimeTo()
    {
        return $this->workTimeTo;
    }

    public function setWorkTimeTo($workTimeTo)
    {
        $this->workTimeTo = $workTimeTo;
        return $this;
    }

    public function getWorstDayInYear()
    {
        return $this->worstDayInYear;
    }

    public function setWorstDayInYear($worstDayInYear)
    {
        $this->worstDayInYear = $worstDayInYear;
        return $this;
    }

	public function getProducts()
	{
		return $this->products;
	}
}
