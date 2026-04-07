<?php

namespace Habbo\Gamedata\Http\Controllers;

use Habbo\Gamedata\Services\ExternalOverrideTextsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class ExternalOverrideTextsController
{
    public function show(
        ExternalOverrideTextsService $service,
        ?string $requestedHash = null
    ): Response|RedirectResponse {
        $currentHash = $service->hash();

        if ($requestedHash !== $currentHash) {
            return redirect()->to($service->hashedUrl(), 302);
        }

        return response($service->content(), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => '"' . $currentHash . '"',
        ]);
    }
}
