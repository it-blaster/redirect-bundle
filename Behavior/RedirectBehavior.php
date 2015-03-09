<?php

namespace ItBlaster\RedirectBundle\Behavior;

/**
 * Редиректы
 *
 * Class RedirectBehavior
 * @package ItBlaster\RedirectBundle\Behavior
 */
class RedirectBehavior extends \Behavior
{
    protected $i18n = NULL;
    protected $builder;

    /**
     * @throws InvalidArgumentException
     */
    public function modifyTable()
    {
        $this->i18n = $this->getParameter('i18n') == 'true';
    }

    /**
     * Аттрибуты
     */
    public function objectAttributes()
    {
        $table_name = $this->getTable()->getName().($this->i18n ? '' : '_i18n');
        $attributes = '
protected $redirect_class_alias = "' . $table_name . '"; //название класса в венгерском стиле
protected $redirect_object = NULL; //объект редиректа
protected $redirect; //название класса в венгерском стиле';
        return $attributes;
    }

    /**
     * Добавляем методы в модель
     *
     * @param $builder
     * @return string
     */
    public function objectMethods($builder)
    {
        $this->builder = $builder;
        $script = '';

        $this->addGetRedirectClassAlias($script);

        if ($this->i18n) {
            $this->methodGetRedirect($script);
            $this->addGetRedirectObject($script);
            $this->addFindRedirectObject($script);
            $this->addSetRedirect($script);
            $this->addSaveRedirect($script);
        } else {
            $this->addDeleteRedirects($script);
        }

        return $script;
    }

    /**
     * Алиас класса
     *
     * @param $script
     */
    protected function addGetRedirectClassAlias(&$script)
    {
        $script .= '
/**
 * Алиас класса
 *
 * @return string
 */
public function getRedirectClassAlias()
{
    return $this->redirect_class_alias;
}
    ';
    }

    /**
     * Возвращает старый адрес
     *
     * @param $script
     */
    protected function methodGetRedirect(&$script)
    {
        $script .= '
/**
 * Возвращает старый адрес
 *
 * @return string
 */
public function getRedirect()
{
    return $this->getRedirectObject() ? $this->getRedirectObject()->getOldUrl() : "";
}
    ';
    }

    /**
     * Устанавливаем старый адрес
     *
     * @param $script
     */
    protected function addSetRedirect(&$script)
    {
        $script .= '
/**
 * Устанавливаем старый адрес
 *
 * @return string
 */
public function setRedirect($value)
{
    $this->redirect = $value;
    $redirect_object = $this->getRedirectObject();
    if ($value && !$redirect_object) {
        $redirect_object = $this->findRedirectObject();
    }
    if ($redirect_object && ($redirect_object->getOldUrl()!=$value || !$value)) {
        $this->modifiedColumns[] = "redirect";
    }
}
    ';
    }

    /**
     * Сохраняем редирект
     *
     * @param $script
     */
    protected function addSaveRedirect(&$script)
    {
        $script .= '
/**
 * Сохраняем редирект
 */
public function saveRedirect()
{
    if ($this->redirect) {
        $redirect_object = $this->findRedirectObject();
        $redirect_object
            ->setLocale($this->getLocale())
            ->setOldUrl($this->redirect)
            ->save();
    } /* else {
        $redirect_object = $this->getRedirectObject();
        if ($redirect_object) {
            $redirect_object->delete();
        }
    } */
}
    ';
    }

    /**
     * Возвращает объект редиректа
     * Если объекта редиректа нет, создаёт его
     *
     * @param $script
     */
    protected function addFindRedirectObject(&$script)
    {
        $script .= '
/**
 * Возвращает объект редиректа
 * Если объекта редиректа нет, создаёт его
 *
 * @return string
 */
public function findRedirectObject()
{
    $redirect_object = $this->getRedirectObject();
    if (!$redirect_object && $this->getId()) {
        $redirect_object = new \ItBlaster\RedirectBundle\Model\Redirect();
        $redirect_object
            ->setModel($this->getRedirectClassAlias())
            ->setLocale($this->getLocale())
            ->setObjectId($this->getId())
            ->save();
        $this->redirect_object = $redirect_object;
    }
    return $redirect_object;
}
    ';
    }

    /**
     * Объект редиректа
     *
     * @param $script
     */
    protected function addGetRedirectObject(&$script)
    {
        $script .= '
/**
 * Объект редиректа
 *
 * @return string
 */
public function getRedirectObject()
{
    if ($this->redirect_object===NULL) {
        $this->redirect_object = \ItBlaster\RedirectBundle\Model\RedirectQuery::create()
            ->filterByModel($this->getRedirectClassAlias())
            ->filterByLocale($this->getLocale())
            ->filterByObjectId($this->getId())
            ->findOne();
    }
    return $this->redirect_object;
}
    ';
    }

    /**
     * Метод сохранения редиректов в postSave
     *
     * @param $builder
     * @return string
     */
    public function postSave($builder)
    {
        $this->builder = $builder;
        $script = '';
        if ($this->i18n) {
            $script .= "\$this->saveRedirect(); //После сохранения объекта сохраняем редиректы";
        }
        return $script;
    }

    /**
     * Удаление связанных объектов редиректов
     *
     * @param $script
     */
    public function addDeleteRedirects(&$script)
    {
        $script .= '
/**
 * Удаление связанных объектов редиректов
 *
 * @return boolean
 */
public function deleteRedirects()
{
    return BaseRedirectQuery::create()
        ->filterByModel($this->getRedirectClassAlias())
        ->filterByObjectId($this->getId())
        ->delete();
}
        ';
    }

    /**
     * Удаляем связанные объекты редиректов
     * перед удалением текущего объекта
     *
     * @param $builder
     * @return string
     */
    public function preDelete($builder)
    {
        $this->builder = $builder;
        $script = '';
        if (!$this->i18n) {
            $script .= "
\$this->deleteRedirects(); //Перед удалением объекта удаляем прикреплённые объекты редиректов";
        }
        return $script;
    }
}