<?php

namespace ItBlaster\RedirectBundle\Admin;

use Artsofte\MainBundle\Model\RedirectQuery;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class RedirectAdmin extends Admin
{
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 1000,
    );
    protected $perPageOptions = array(1000, 2000);
    protected $maxPerPage = 1000;
    protected $maxPageLinks = 1000;

    /**
     * Список
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        if ($this->useModel()) {
            $listMapper
                ->add('Model')
                ->add('ObjectId')
                ->add('Locale');
        }

        $listMapper
            ->add('OldUrl', null, array(
                'label' => 'Старый адрес'
            ))
            ->add('NewUrl', null, array(
                'label' => 'Новый адрес'
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * Конфигурем форму редактирования
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        if ($this->useModel()) {
            $formMapper
                ->add('Model', 'text', array(
                    'disabled'  => true,
                    'required'  => false
                ))
                ->add('ObjectId', 'text', array(
                    'disabled'  => true,
                    'required'  => false
                ))
                ->add('Locale', 'choice', array(
                    'label'         => 'Локаль',
                    'required'      => false,
                    'choices'       => $this->getLocaleChoices(),
                    'empty_data'    => false
                ));
        }
        $formMapper
            ->add('OldUrl', 'text', array(
                'label' => 'Старый адрес',
                'attr'  => array(
                    'maxlength' => 255
                )
            ))
            ->add('NewUrl', 'text', array(
                'label'     => 'Новый адрес',
                'required'  => false,
                'attr'      => array(
                    'maxlength' => 255
                )
            ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        if ($this->useModel()) {
            $showMapper
                ->add('Model')
                ->add('ObjectId')
                ->add('Locale');
        }
        $showMapper
            ->add('OldUrl')
            ->add('NewUrl')
        ;
    }

    /**
     * Чистка кеша после создания объекта
     *
     * @param mixed $object
     * @return mixed|void
     */
    public function postPersist($object)
    {
        $this->cleanUp();
    }

    /**
     * Чистка кеша после обновления объекта
     *
     * @param mixed $object
     * @return mixed|void
     */
    public function postUpdate($object)
    {
        $this->cleanUp();
    }

    /**
     * Удаляем записи без урла
     *
     * @throws \Exception
     * @throws \PropelException
     */
    protected function cleanUp()
    {
        return \ItBlaster\RedirectBundle\Model\RedirectQuery::create()->filterByOldUrl(NULL)->delete();
    }

    /**
     * Локали
     *
     * @return array
     */
    protected function getLocaleChoices()
    {
        $locales = array();
        foreach ($this->getConfig('it_blaster_redirect.locales') as $locale) {
            $locales[$locale] = $locale;
        }
        return $locales;
    }

    /**
     * Убираем экспорт
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
    }

    /**
     * Возвращает конфиг из config.yml
     *
     * @param $config_name
     * @return mixed
     */
    protected function getConfig($config_name)
    {
        return $this->getConfigurationPool()->getContainer()->getParameter($config_name);
    }

    /**
     * Используются ли редиректы у моделей
     *
     * @return bool
     */
    protected function useModel()
    {
        return $this->getConfig('it_blaster_redirect.use_model') == 'true';
    }
}
