<?php

use DutchCodingCompany\FilamentSocialite\Http\Controllers\SocialiteLoginController;
use DutchCodingCompany\FilamentSocialite\Http\Middleware\PanelFromUrlQuery;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

foreach (Filament::getPanels() as $panel) {
    if (! $panel->hasPlugin('filament-socialite')) {
        continue;
    }

    // Retrieve slug for route name.
    $slug = $panel->getPlugin('filament-socialite')->getSlug();

    $domains = $panel->getDomains();

    foreach ((empty($domains) ? [null] : $domains) as $domain) {
        $redirectRoute = "socialite.{$panel->generateRouteName('oauth.redirect')}";

        Route::domain($domain)
            ->middleware($panel->getMiddleware())
            ->name($redirectRoute)
            ->get("/$slug/oauth/{provider}", [SocialiteLoginController::class, 'redirectToProvider']);
    }
}

Route::match(['get', 'post'], "/oauth/callback/{provider}", [SocialiteLoginController::class, 'processCallback'])
    ->middleware([
        PanelFromUrlQuery::class,
        ...config('filament-socialite.middleware'),
    ])
    ->name('oauth.callback');
