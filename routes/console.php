<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['api.auth']]);
