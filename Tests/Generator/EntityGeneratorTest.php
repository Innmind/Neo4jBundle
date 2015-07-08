<?php

namespace Innmind\Neo4jBundle\Tests\Generator;

use Innmind\Neo4jBundle\Generator\EntityGenerator;
use Innmind\Neo4j\ONM\Mapping\NodeMetadata;
use Innmind\Neo4j\ONM\Mapping\RelationshipMetadata;
use Innmind\Neo4j\ONM\Mapping\Property;
use Innmind\Neo4j\ONM\IdentityMap;
use Memio\Memio\Config\Build;

class EntityGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $g;
    protected $printer;

    public function setUp()
    {
        $this->g = new EntityGenerator;
        $this->printer = Build::prettyPrinter();
    }

    public function testGenerateNode()
    {
        $meta = new NodeMetadata;
        $meta
            ->setClass('Innmind\Entity\Resource')
            ->addProperty(
                (new Property)
                    ->setName('uri')
                    ->setType('string')
            )
            ->addProperty(
                (new Property)
                    ->setName('created')
                    ->setType('date')
            )
            ->addProperty(
                (new Property)
                    ->setName('referrer')
                    ->setType('relationship')
                    ->addOption('relationship', 'Referrer')
            );
        $map = new IdentityMap;
        $map->addClass('Referrer');

        $expected = <<<EOF
<?php
namespace Innmind\Entity;

use DateTime;
use Referrer;

class Resource
{
    /**
     * @var string
     */
    protected \$uri;

    /**
     * @var date
     */
    protected \$created;

    /**
     * @var relationship
     */
    protected \$referrer;

    public function getUri()
    {
        return \$this->uri;
    }

    public function setUri(\$uri)
    {
        \$this->uri = (string) \$uri;

        return \$this;
    }

    public function getCreated()
    {
        return \$this->created;
    }

    public function setCreated(DateTime \$created)
    {
        \$this->created = \$created;

        return \$this;
    }

    public function getReferrer()
    {
        return \$this->referrer;
    }

    public function setReferrer(Referrer \$referrer)
    {
        \$this->referrer = \$referrer;

        return \$this;
    }
}

EOF;

        $output = $this->g->generate($meta, $map);

        $this->assertSame(
            $expected,
            $this->printer->generateCode($output)
        );
    }

    public function testGenerateRelationship()
    {
        $meta = new RelationshipMetadata;
        $meta
            ->setClass('Innmind\Entity\Referrer')
            ->addProperty(
                (new Property)
                    ->setName('uri')
                    ->setType('string')
            )
            ->addProperty(
                (new Property)
                    ->setName('created')
                    ->setType('date')
            )
            ->addProperty(
                (new Property)
                    ->setName('start')
                    ->setType('startNode')
                    ->addOption('node', 'Resource')
            )
            ->addProperty(
                (new Property)
                    ->setName('end')
                    ->setType('endNode')
                    ->addOption('node', 'Resource')
            );
        $map = new IdentityMap;
        $map->addClass('Resource');

        $expected = <<<EOF
<?php
namespace Innmind\Entity;

use DateTime;
use Resource;

class Referrer
{
    /**
     * @var string
     */
    protected \$uri;

    /**
     * @var date
     */
    protected \$created;

    /**
     * @var startNode
     */
    protected \$start;

    /**
     * @var endNode
     */
    protected \$end;

    public function getUri()
    {
        return \$this->uri;
    }

    public function setUri(\$uri)
    {
        \$this->uri = (string) \$uri;

        return \$this;
    }

    public function getCreated()
    {
        return \$this->created;
    }

    public function setCreated(DateTime \$created)
    {
        \$this->created = \$created;

        return \$this;
    }

    public function getStart()
    {
        return \$this->start;
    }

    public function setStart(Resource \$start)
    {
        \$this->start = \$start;

        return \$this;
    }

    public function getEnd()
    {
        return \$this->end;
    }

    public function setEnd(Resource \$end)
    {
        \$this->end = \$end;

        return \$this;
    }
}

EOF;

        $output = $this->g->generate($meta, $map);

        $this->assertSame(
            $expected,
            $this->printer->generateCode($output)
        );
    }
}
