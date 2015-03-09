RedirectBundle
====================

[![Build Status](https://scrutinizer-ci.com/g/it-blaster/redirect-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/redirect-bundle/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/it-blaster/redirect-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/redirect-bundle/?branch=master)

Вспомогательный бандл для работы с редиректами на сайте. Если у вас был старый сайт, а теперь вы переехали на новый сайт, написанный на symfony 2, и вы хотите чтобы по старым ссылкам открывались страницы нового сайта, то этот бандл для вас.

Installation
------------

Добавьте <b>ItBlasterRedirectBundle</b> в `composer.json`:

```js
{
    "require": {
        "it-blaster/redirect-bundle": "dev-master"
	},
}
```

Теперь запустите композер, чтобы скачать бандл командой:

``` bash
$ php composer.phar update it-blaster/redirect-bundle
```

Композер установит бандл в папку проекта `vendor/it-blaster/redirect-bundle`.

Далее подключите бандл в ядре `AppKernel.php`:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ItBlaster\RedirectBundle\ItBlasterRedirectBundle(),
    );
}
```

Use redirect in models
-------
С помощью <b>RedirectBundle</b> редиректы можно добавлять в формы редактирования сущностей. Для этого необходимо:
1. указать в файле `config.yml` параметр `it_blaster_redirect.use_model`
``` bash
it_blaster_redirect:
    locales: ['ru', 'en','uk','cs']
    use_model: true
```
2. подключить бихейвор `RedirectBehavior` в `config.yml`
``` bash
 propel:
     ...
     behaviors:
         ...
         it_blaster_redirect: ItBlaster\RedirectBundle\Behavior\RedirectBehavior
```
3. в файле `schema.yml` добавить бихейвор `it_blaster_redirect` к описанию молели:
``` bash
    <table name="news" description="Новости">
        <column name="id"                   type="integer"  required="true" primaryKey="true" autoIncrement="true" />
        <column name="title"                type="varchar"  required="true" primaryString="true" />
        <column name="alias"                type="varchar"  required="true" />
        <column name="date"                 type="date"     required="true" />
        <column name="short_desc"           type="longvarchar" />
        <column name="full_desc"            type="longvarchar" />
        <column name="active"               type="boolean" />

        <behavior name="i18n">
            <parameter name="i18n_columns" value="title, short_desc, full_desc" />
        </behavior>

        <behavior name="it_blaster_redirect" >
            <parameter name="i18n" value="false" />
        </behavior>
    </table>

    <table name="news_i18n" description="Новости. Языковые версии">
        <behavior name="it_blaster_redirect" >
            <parameter name="i18n" value="true" />
        </behavior>
    </table>
```

Use Redirect
-------
Для начала необходимо переопределить контроллер 404 ошибки. Для этого в файле `config.yml`
``` bash
twig:
    exception_controller: AppMainBundle:Layout:showException
```
Далее в методе showException контроллера Layout перед тем как отдать 404-страницу проверяем есть ли текущий адрес в таблице редиректов:
``` php
    public function showExceptionAction(Request $request, FlattenException $exception, DebugLoggerInterface $logger = null)
    {
        $redirect = $this->oldRedirect($request, $exception);
        if ($redirect) {
            return $this->redirect($redirect);
        }
        ...
    }

    /**
     * Если ссылка пользователя ведёт на старый сайт, редиректим на новый сайт
     *
     * @param Request $request
     * @param FlattenException $exception
     */
    protected function oldRedirect(Request $request, FlattenException $exception)
    {
        $redirect_object = RedirectQuery::create()
            ->filterByOldUrl('%'.$request->getUri().'%', \Criteria::LIKE)
            ->findOne();
        if ($redirect_object) { //ща средиректим
            return $redirect_object->getNewUrl(); //адрес на указанную ссылку
        }
    }
```

Если у вас стоит параметр `use_model: true`, то в операторе `if ($redirect_object) {` необходимо добавить проверку `if ($redirect_object->getNewUrl()) {`. Если этот оператор не выполняется, то смотреть на поля `model`, `object_id` и пытаться средиректить на страницу объекта. Например:
``` php
    protected function oldRedirect(Request $request, FlattenException $exception)
    {
        $redirect_object = RedirectQuery::create()
            ->filterByOldUrl('%'.$request->getUri().'%', \Criteria::LIKE)
            ->findOne();
        if ($redirect_object) { //ща средиректим
            if ($redirect_object->getNewUrl()) {
                return $redirect_object->getNewUrl(); //адрес на указанную ссылку
            } else {
                $model = $redirect_object->getModel();
                $object_id = $redirect_object->getObjectId();
                if ($model && $object_id) {
                    $route_name = false;
                    $route_params = array();
                    switch ($model) {
                        case 'news_i18n':
                            $object = NewsQuery::create()->findOneById($object_id);
                            if ($object) {
                                $route_name = 'news-item';
                                $route_params = array('alias'=>$object->getAlias());
                            }
                            break;
                        ...
                    }
                    if ($route_name) {
                        return $this->generateUrl($route_name, $route_params); //адрес на объект
                    }
                }
            }
        }
        return false;
    }
```
Credits
-------

It-Blaster <it-blaster@yandex.ru>