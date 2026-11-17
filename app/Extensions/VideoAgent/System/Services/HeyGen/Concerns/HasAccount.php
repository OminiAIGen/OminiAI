<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasAccount
{
    /**
     * Account information and remaining prepaid wallet balance. HeyGen also
     * treats this as the API-key health check, so a 401 here means the key is
     * wrong rather than the account being empty.
     *
     * @return array<string, mixed>
     */
    public function currentUser(): array
    {
        return $this->get('/v3/users/me');
    }
}
