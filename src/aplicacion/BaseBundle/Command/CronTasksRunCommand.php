<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CronTasksRunCommand
 *
 * @author soporte
 */
namespace aplicacion\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\StringInput;

class CronTasksRunCommand extends ContainerAwareCommand
{
    private $output;

    protected function configure()
    {
        $this
            ->setName('crontasks:run')
            ->setDescription('Corre los comandos que sean necesarios de acuerdo al tiempo real y su intervalo')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$output->writeln('<comment>Corriendo commandos programados...</comment>');

        $this->output = $output;
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        //$crontasks = $em->getRepository('BaseBundle:CronTask')->findAll();
        $crontasks = $em->getRepository('BaseBundle:CronTask')->findBy(array('activa'=>true));
        foreach ($crontasks as $crontask) {
            // Get the last run time of this task, and calculate when it should run next
            $lastrun = $crontask->getLastrun() ? $crontask->getLastRun()->format('U') : 0;
            $nextrun = $lastrun + $crontask->getIntervalo();

            // We must run this task if:
            // * time() is larger or equal to $nextrun
            $run = (time() >= $nextrun);

            if ($run) {
                //$output->writeln(sprintf('Corriendo tarea programada <info>%s</info>', $crontask->getNombre()));

                // Set $lastrun for this crontask
                $crontask->setLastrun(new \DateTime());

                try {
                    $commands = $crontask->getComandos();
                    foreach ($commands as $command) {
                       // $output->writeln(sprintf('Ejecutando comando <comment>%s</comment>...', $command));

                        // Run the command
                        $this->runCommand($command);
                    }

                    //$output->writeln('<info>CORRECTO</info>');
                } catch (\Exception $e) {
                    //$output->writeln('<error>ERROR</error>');
                }

                // Persist crontask
                $em->persist($crontask);
            } else {
                //$output->writeln(sprintf('Saltando tarea programada <info>%s</info>', $crontask->getNombre()));
            }
        }

        // Flush database changes
        $em->flush();

        //$output->writeln('<comment>Terminado!</comment>');
    }

    private function runCommand($string)
    {
        // Split namespace and arguments
        $namespace = explode(' ', $string);
        $namespace=$namespace[0];
        // Set input
        $command = $this->getApplication()->find($namespace);
        $input = new StringInput($string);

        // Send all output to the console
        $returnCode = $command->run($input, $this->output);

        return $returnCode != 0;
    }
}