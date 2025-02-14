<?php

namespace App\Contracts\Repositories;

use App\Contracts\Methods\ExistsByNameInterface;

interface PublisherRepositoryInterface extends AbstractRepositoryInterface, ExistsByNameInterface
{
}
