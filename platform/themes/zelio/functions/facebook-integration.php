<?php

use Botble\Theme\Supports\ThemeSupport;
use Illuminate\Routing\Events\RouteMatched;

app('events')->listen(RouteMatched::class, fn () => ThemeSupport::registerFacebookIntegration());
