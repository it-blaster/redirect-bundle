<?php

namespace ItBlaster\RedirectBundle\Model;

use ItBlaster\RedirectBundle\Model\om\BaseRedirectQuery;
use Symfony\Component\HttpFoundation\Request;

class RedirectQuery extends BaseRedirectQuery
{

    public function findRedirect(Request $request)
    {
        $redirect_object = $this->findExactRedirect($request);

        if (!$redirect_object && $request->getRequestUri() != '/') {
            $url = $request->getRequestUri();

            $redirect_object = RedirectQuery::create()
                ->filterByOldUrl('%' . $url, \Criteria::LIKE)
                ->findOne();

            if (!$redirect_object) {
                $redirect_object = RedirectQuery::create()
                    ->filterByOldUrl('%' . $url . '%', \Criteria::LIKE)
                    ->findOne();

                if (!$redirect_object && $request->getPathInfo() != '/') {
                    $url = $request->getPathInfo();
                    $redirect_object = RedirectQuery::create()
                        ->filterByOldUrl('%' . $url . '%', \Criteria::LIKE)
                        ->findOne();
                }
            }
        }

        return $redirect_object;
    }

    public function findExactRedirect(Request $request)
    {
        $url = str_replace(array('http://', 'https://'), '', $request->getUri());

        $redirect_object = RedirectQuery::create()
            ->filterByOldUrl('%'.$url, \Criteria::LIKE)
            ->findOne();

        return $redirect_object;
    }

}
