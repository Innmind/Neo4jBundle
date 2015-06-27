<?php

namespace Innmind\Neo4jBundle\Generator;

use Innmind\Neo4j\ONM\Mapping\Metadata;
use Innmind\Neo4j\ONM\Mapping\Property;
use Innmind\Neo4j\ONM\IdentityMap;
use Memio\Model\Property as PropertyView;
use Memio\Model\PhpDoc\PropertyPhpdoc;
use Memio\Model\PhpDoc\VariableTag;
use Memio\Model\Method;
use Memio\Model\Argument;
use Memio\Model\Object;
use Memio\Model\File;
use Memio\Model\FullyQualifiedName;

class EntityGenerator
{
    /**
     * Generate the code to create a class corresponding to the given metadata
     *
     * @param Metadata $meta
     * @param IdentityMap $map
     *
     * @return File
     */
    public function generate(Metadata $meta, IdentityMap $map)
    {
        $properties = [];
        $methods = [];

        foreach ($meta->getProperties() as $property) {
            $properties[] = $this->generateProperty($property);
            $methods[] = $this->generateGetter($property);
            $methods[] = $this->generateSetter($property);
        }

        $object = Object::make($meta->getClass());

        foreach ($properties as $prop) {
            $object->addProperty($prop);
        }
        foreach ($methods as $method) {
            $object->addMethod($method);
        }

        $file = File::make(null)->setStructure($object);

        $this->appendUses($file, $meta, $map);

        return $file;
    }

    /**
     * Generate the property code
     *
     * @param Property $property
     *
     * @return PropertyView
     */
    protected function generateProperty(Property $property)
    {
        return PropertyView::make($property->getName())
            ->makeProtected()
            ->setPhpdoc(
                PropertyPhpdoc::make()->setVariableTag(
                    VariableTag::make($property->getType())
                )
            );
    }

    /**
     * Generate the setter for the given property
     *
     * @param Property $property
     *
     * @return Method
     */
    protected function generateSetter(Property $property)
    {
        $name = $property->getName();
        $method = new Method(sprintf(
            'set%s',
            $this->camelize($name)
        ));

        $type = $property->getType();
        $castable = [
            'int' => 'int',
            'integer' => 'int',
            'float' => 'float',
            'bool' => 'bool',
            'boolean' => 'bool',
            'string' => 'string',
        ];

        if (isset($castable[$type])) {
            $cast = sprintf('(%s)', $castable[$type]);
        } else {
            $cast = '';
        }

        switch ($type) {
            case 'date':
                $type = 'DateTime';
                break;

            case 'relationship':
                $type = $property->getOption('relationship');
                break;

            case 'startNode':
            case 'endNode':
                $type = $property->getOption('node');
                break;
        }

        $method->addArgument(new Argument($type, $name));
        $method->setBody(<<<EOF
        \$this->$name = $cast \$$name;

        return \$this;
EOF
        );

        return $method;
    }

    /**
     * Generate the getter for the given property
     *
     * @param Property $property
     *
     * @return Method
     */
    protected function generateGetter(Property $property)
    {
        $name = $property->getName();

        return Method::make(sprintf('get%s', $this->camelize($name)))
            ->setBody(<<<EOF
        return \$this->$name;
EOF
            );
    }

    /**
     * Camelize a string
     *
     * @param string $string
     *
     * @return string
     */
    protected function camelize($string)
    {
        $pieces = explode('_', (string) $string);
        $pieces = array_map('strtolower', $pieces);
        $pieces = array_map('ucfirst', $pieces);

        return implode('', $pieces);
    }

    /**
     * Add use statements to the file if any needed
     *
     * @param File $file
     * @param Metadata $meta
     * @param IdentityMap $map
     *
     * @return void
     */
    protected function appendUses(File $file, Metadata $meta, IdentityMap $map)
    {
        $classes = [];

        foreach ($meta->getproperties() as $prop) {
            switch ($prop->getType()) {
                case 'date':
                    $classes[] = 'DateTime';
                    break;

                case 'relationship':
                    $classes[] = $map->getClass(
                        $prop->getOption('relationship')
                    );
                    break;

                case 'startNode':
                case 'endNode':
                    $classes[] = $map->getClass($prop->getOption('node'));
                    break;
            }
        }

        $classes = array_unique($classes);

        foreach ($classes as $class) {
            $file->addFullyQualifiedName(
                FullyQualifiedName::make(
                    $class
                )
            );
        }
    }
}
