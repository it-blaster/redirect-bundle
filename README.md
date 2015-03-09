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

Credits
-------

It-Blaster <it-blaster@yandex.ru>