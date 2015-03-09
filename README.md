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

Credits
-------

It-Blaster <it-blaster@yandex.ru>