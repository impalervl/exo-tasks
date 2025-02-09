<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Actions\GetDesignAction;
use Core\Helpers\Config;
use Core\Helpers\Cookie;
use Exads\ABTestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetABDesignPageController
{
    private GetDesignAction $getDesignAction;

    public function __construct()
    {
        $this->getDesignAction = new GetDesignAction();
    }

    public function index(Request $request): Response
    {
        $cookieData  = $this->getCookieData($request);
        $promotionId = $cookieData['promotion_id'] ?? $this->getDesignAction->getFallbackPromotionId();
        $designId    = $cookieData['design_id'] ?? null;

        if (!$designId) {
            // no design found in cookie. assign design for available promotion or random promotion from fallback
            return $this->redirectToPromotion($promotionId);
        }

        try {
            $design = $this->getDesignAction->handle($promotionId, $designId);
        } catch (ABTestException) {
            // if design or promotion were deleted, assign new promotion.
            return $this->redirectToPromotion($this->getDesignAction->getFallbackPromotionId());
        }

        $this->handleIgnoreSession();

        return new JsonResponse(['name' => $design->name, 'id' => $design->id, 'promotion_id' => $promotionId]);
    }

    protected function getCookieData(Request $request): array
    {
        $cookieName = Config::get('ab-test.cookie_name');
        return json_decode($request->cookies->get($cookieName, '{}'), true);
    }

    protected function redirectToPromotion(int $promotionId): RedirectResponse
    {
        return new RedirectResponse('promotion-designs/' . $promotionId);
    }

    protected function handleIgnoreSession(): void
    {
        if (Config::get('ab-test.ignore_session')) {
            Cookie::delete(Config::get('ab-test.cookie_name'));
        }
    }
}
