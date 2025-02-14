<?php

namespace App\Contracts\Repositories;

use App\Contracts\Methods\ExistsByNameInterface;

interface DeveloperRepositoryInterface extends AbstractRepositoryInterface, ExistsByNameInterface
{
}
