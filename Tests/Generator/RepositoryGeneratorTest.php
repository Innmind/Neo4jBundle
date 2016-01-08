<?php

namespace Innmind\Neo4jBundle\Tests\Generator;

use Innmind\Neo4jBundle\Generator\RepositoryGenerator;
use Memio\Memio\Config\Build;

class RepositoryGeneratorTest extends \PHPUnit_Framework_TestCase
{
    protected $g;
    protected $printer;

    public function setUp()
    {
        $this->g = new RepositoryGenerator;
        $this->printer = Build::prettyPrinter();
    }

    public function testGenerate()
    {
        $file = $this->g->generate('Innmind\Entity\ResourceRepository');

        $expected = <<<EOF
<?php

namespace Innmind\Entity;

use Innmind\Neo4j\ONM\Repository;

class ResourceRepository extends Repository
{
}

EOF;

        $this->assertSame(
            $expected,
            $this->printer->generateCode($file)
        );
    }
}
