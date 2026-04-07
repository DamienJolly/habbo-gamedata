<?php

namespace Habbo\Gamedata\Http\Controllers;

use Habbo\Gamedata\Services\FigureDataService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class FigureDataController
{
    public function show(
        FigureDataService $service,
        ?string $requestedHash = null
    ): Response|RedirectResponse {
        $currentHash = $service->hash();

        if ($requestedHash !== $currentHash) {
            return redirect()->to($service->hashedUrl(), 302);
        }

        return response($service->content(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => '"' . $currentHash . '"',
        ]);
    }
}
