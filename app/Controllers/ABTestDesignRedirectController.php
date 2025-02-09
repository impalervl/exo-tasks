<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Actions\GetABTestDesignRedirectAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ABTestDesignRedirectController
{
    private GetABTestDesignRedirectAction $getABTestDesignRedirectAction;

    public function __construct()
    {
        $this->getABTestDesignRedirectAction = new GetABTestDesignRedirectAction();
    }

    /**
     * @throws \Exception
     */
    public function index(Request $request): RedirectResponse
    {
        $promotion = (int) $request->attributes->get('promotion');

        try {
            $url = $this->getABTestDesignRedirectAction->handle($promotion);
        } catch (\Throwable $exception) {
            //no promotion available log error and redirect to the default page
            return new RedirectResponse('/');
        }

        return new RedirectResponse($url);
    }
}
