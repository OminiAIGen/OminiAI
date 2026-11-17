<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns;

trait HasAccount
{
    /**
     * The cheapest authenticated call Rendley exposes, used by the admin
     * "Test connection" button to prove a key before anyone spends credits.
     *
     * @return array<string, mixed>
     */
    public function me(): array
    {
        return $this->get('/v1/users/me');
    }

    /**
     * @return array<string, mixed>
     */
    public function workspaces(): array
    {
        return $this->get('/v1/workspaces');
    }
}
