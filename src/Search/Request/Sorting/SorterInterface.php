<?php

/*
 * This file is part of Monsieur Biz' Search plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Search\Request\Sorting;

use Elastica\Query;
use MonsieurBiz\SyliusSearchPlugin\Search\Request\RequestConfiguration;

interface SorterInterface
{
    public function apply(Query $query, RequestConfiguration $requestConfiguration): void;
}
