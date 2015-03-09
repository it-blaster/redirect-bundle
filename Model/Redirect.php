<?php

namespace ItBlaster\RedirectBundle\Model;

use ItBlaster\RedirectBundle\Model\om\BaseRedirect;

class Redirect extends BaseRedirect
{
    public function __toString()
    {
        return $this->isNew() ? 'Новый редирект' : (string) $this->getOldUrl();
    }
}
