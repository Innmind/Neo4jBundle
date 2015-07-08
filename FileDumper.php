<?php

namespace Innmind\Neo4jBundle;

use Memio\Model\File;
use Memio\Memio\Config\Build;
use Symfony\Component\Filesystem\Filesystem;

class FileDumper
{
    protected $filesystem;
    protected $printer;

    public function __construct()
    {
        $this->filesystem = new Filesystem;
        $this->printer = Build::prettyPrinter();
    }

    /**
     * Dump the code for the following class
     *
     * @param string $class
     * @param File $file
     *
     * @return FileDumper self
     */
    public function dump($class, File $file)
    {
        $path = $this->getClassPath((string) $class);

        if ($this->filesystem->exists($path)) {
            $this->filesystem->copy($path, $path . '~', true);
        }

        $code = $this->printer->generateCode($file);

        $this->filesystem->dumpFile($path, $code);

        return $this;
    }

    /**
     * Return the file path for the given class
     *
     * It assumes the working directory is src
     *
     * @param string $class
     *
     * @return string
     */
    protected function getClassPath($class)
    {
        return sprintf(
            'src/%s.php',
            str_replace('\\', '/', $class)
        );
    }
}
