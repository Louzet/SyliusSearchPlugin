<?php

/*
 * This file is part of Monsieur Biz' Search plugin for Sylius.
 *
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusSearchPlugin\Search\Request\Aggregation;

use Elastica\Query\AbstractQuery;
use Elastica\QueryBuilder;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class ProductAttributesAggregation implements AggregationBuilderInterface
{
    private ProductAttributeAggregation $productAttributeAggregationBuilder;

    public function __construct()
    {
        $this->productAttributeAggregationBuilder = new ProductAttributeAggregation();
    }

    public function build($aggregation, array $filters)
    {
        if (!$this->isSupport($aggregation)) {
            return null;
        }

        $qb = new QueryBuilder();

        $currentFilters = array_filter($filters, function (AbstractQuery $filter): bool {
            return !$filter->hasParam('path') || false === strpos($filter->getParam('path'), 'attributes.');
        });

        $filterQuery = $qb->query()->bool();
        foreach ($currentFilters as $filter) {
            $filterQuery->addMust($filter);
        }

        $attributesAggregation = $qb->aggregation()->nested('attributes', 'attributes');
        /** @phpstan-ignore-next-line */
        foreach ($aggregation as $subAggregation) {
            $subAggregationObject = $this->productAttributeAggregationBuilder->build($subAggregation, $filters);
            if (null === $subAggregationObject || false === $subAggregationObject) {
                continue;
            }
            $attributesAggregation->addAggregation($subAggregationObject);
        }

        if (0 == \count($attributesAggregation->getAggs())) {
            return false;
        }

        return $qb->aggregation()->filter('attributes')
            ->setFilter($filterQuery)
            ->addAggregation($attributesAggregation)
        ;
    }

    /**
     * @param string|array|object $aggregation
     */
    private function isSupport($aggregation): bool
    {
        if (!\is_array($aggregation)) {
            return false;
        }
        foreach ($aggregation as $subAggregation) {
            if ($subAggregation instanceof ProductAttributeInterface) {
                return true;
            }
        }

        return false;
    }
}
