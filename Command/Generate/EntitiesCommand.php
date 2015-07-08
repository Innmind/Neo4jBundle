<?php

namespace Innmind\Neo4jBundle\Command\Generate;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class EntitiesCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('neo4j:generate:entities')
            ->setDescription('Generate entities from the configuration files')
            ->addOption(
                'manager',
                'm',
                InputOption::VALUE_OPTIONAL,
                '',
                'default'
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $uow = $this
            ->getContainer()
            ->get('innmind_neo4j.registry')
            ->getManager($input->getOption('manager'))
            ->getUnitOfWork();
        $metadatas = $uow
            ->getMetadataRegistry()
            ->getMetadatas();
        $map = $uow->getIdentityMap();

        $output->writeln(sprintf(
            '<success>Creating entities for the manager "<fg=cyan>%s</fg=cyan>"</success>',
            $input->getOption('manager')
        ));

        $generator = $this
            ->getContainer()
            ->get('innmind_neo4j.generator');

        foreach ($metadatas as $meta) {
            try {
                $generator->generate($meta, $map);

                $output->writeln(sprintf(
                    'Class "<success>%s</success>" created',
                    $meta->getClass()
                ));
            } catch (\Exception $e) {
                $output->writeln(sprintf(
                    '<error>Class "<success>%s</success>" couldn\'t be created</error>',
                    $meta->getClass()
                ));
            }
        }
    }
}
