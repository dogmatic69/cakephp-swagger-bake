<?php
declare(strict_types=1);

namespace SwaggerBake\Lib\Extension\CakeSearch;

use ReflectionClass;

/**
 * Class FilterDecorator
 *
 * @package SwaggerBake\Lib\Extension\CakeSearch
 */
class FilterDecorator
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $comparison;

    /**
     * @var \Search\Model\Filter\Base
     */
    private $filter;

    /**
     * @param \Search\Model\Filter\Base $filter Filter\Base
     * @throws \ReflectionException
     */
    public function __construct(\Search\Model\Filter\Base $filter)
    {
        $reflection = new ReflectionClass($filter);
        $property = $reflection->getProperty('_defaultConfig');
        $property->setAccessible(true);
        //$configs = $property->getValue($filter);

        $this->name = $filter->name();
        $this->comparison = $reflection->getShortName();
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getComparison(): string
    {
        return $this->comparison;
    }
}
