<?php

namespace Innmind\Neo4jBundle;

use Innmind\Neo4j\ONM\EntityManagerInterface;

/**
 * Holds all the managers declared in the app
 */
class ManagerRegistry
{
    protected $managers = [];
    protected $default = 'default';

    /**
     * Set the name to use as default manager
     *
     * @param string $name
     *
     * @return ManagerRegistry self
     */
    public function setDefaultManager($name)
    {
        $this->default = (string) $name;

        return $this;
    }

    /**
     * Add an entity manager
     *
     * @param string $name
     * @param EntityManagerInterface $em
     *
     * @return ManagerRegistry self
     */
    public function addManager($name, EntityManagerInterface $em)
    {
        $this->managers[(string) $name] = $em;

        return $this;
    }

    /**
     * Return the maager with the given name
     * (or the default one if none provided)
     *
     * @param string $name
     *
     * @throws InvalidArgumentException if no manager found for the given name
     *
     * @return EntityManagerInterface
     */
    public function getManager($name = null)
    {
        $name = (string) $name ?: $this->default;

        if (!isset($this->managers[$name])) {
            throw new \InvalidArgumentException(sprintf(
                'No entity manager found with the name "%s"',
                $name
            ));
        }

        return $this->managers[$name];
    }
}
