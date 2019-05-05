<?php

namespace Core;

class BaseController
{
    /**
     * @var Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Assign the container on property container
     *
     * @param Psr\Container\ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Return container's property
     *
     * @param  string $property
     * @return mixed
     */
    public function __get($property)
    {
        return $this->container->get($property);
    }
}
