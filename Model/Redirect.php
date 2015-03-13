<?php

namespace ItBlaster\RedirectBundle\Model;

use ItBlaster\RedirectBundle\Model\om\BaseRedirect;

class Redirect extends BaseRedirect
{
    /**
     * @return string
     */
    public function __toString()
    {
        $str = $this->isNew() ? 'Новый редирект' : (string) $this->getOldUrl();
        if (mb_strlen($str, "UTF-8")>50) {
            $str = mb_substr($str, 0, 50, "utf-8")."...";
        }
        return $str;
    }
}
