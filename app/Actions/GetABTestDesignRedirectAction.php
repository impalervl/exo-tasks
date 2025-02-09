<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\ABTest\ABTestService;
use Core\Helpers\Config;
use Core\Helpers\Cookie;
use Exads\ABTestData;
use Exads\ABTestException;

class GetABTestDesignRedirectAction
{
    /**
     * @throws ABTestException
     */
    public function handle(int $promotionId): string
    {
        $cookieName    = Config::get('ab-test.cookie_name');
        $cookie        = Cookie::get($cookieName) ?: '';
        $ignoreSession = Config::get('ab-test.ignore_session');

        if ($cookie !== '' && !$ignoreSession) {
            $cookie = json_decode($cookie, true) ?? [];
            if ($this->isValidCookie($cookie) && $cookie['promotion_id'] === $promotionId) {
                return $this->generateRedirectUrl();
            }
        }

        $testData = new ABTestData($promotionId);
        $design   = (new ABTestService($testData))->getAssignedDesign($promotionId);
        Cookie::set($cookieName, json_encode(['promotion_id' => $promotionId, 'design_id' => $design->id]));

        return $this->generateRedirectUrl();
    }

    private function generateRedirectUrl(): string
    {
        return '/ab-design-page';
    }

    private function isValidCookie(array $cookieData): bool
    {
        return isset($cookieData['promotion_id'], $cookieData['design_id']);
    }
}
