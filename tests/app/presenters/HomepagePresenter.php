<?php

class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var \Doctrine\ORM\EntityManager $em */
    protected $em;

    /** @var \Foxy\MediaStorage $mediaStorage */
    protected $mediaStorage;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function injectEntityManager(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param \Foxy\MediaStorage $em
     */
    public function injectMediaStorage(\Foxy\MediaStorage $mediaStorage)
    {

        $this->mediaStorage = $mediaStorage;
    }

    public function actionDefault()
    {
        $this->template->categories = $this->em->getRepository('Category')->findAll();
        $this->template->products = $this->em->getRepository('Product')->findAll();
        $this->template->parameters = $this->em->getRepository('Parameter')->findAll();
        $this->template->users = $this->em->getRepository('User')->findAll();
    }

    public function actionDetail($model, $id)
    {
        $this->template->entity = $this->em->find($model, $id);
        $component = strtolower($model). 'Form';
        $this[$component]->setInstance($this->template->entity);
    }

    public function createComponentCategoryForm()
    {
        return new CategoryForm($this->em);
    }

    public function createComponentProductForm()
    {
        return new ProductForm($this->em);
    }

    public function createComponentParameterForm()
    {
        return new ParameterForm($this->em);
    }

    public function createComponentUserForm()
    {
        return new UserForm($this->em);
    }
}
