<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class task extends Command
{
    protected function configure()
    {
        $this->setName('task')
            ->addArgument('name', Argument::OPTIONAL, "your name")
            ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('del task');
    }

    protected function execute(Input $input, Output $output)
    {


        $output->writeln("ok");
    }

    protected function execute_bak(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $name = $name ?: 'thinkphp';

        if ($input->hasOption('city')) {
            $city = PHP_EOL . 'From ' . $input->getOption('city');
        } else {
            $city = '';
        }

        $output->writeln("Hello," . $name . '!' . $city);
    }
}