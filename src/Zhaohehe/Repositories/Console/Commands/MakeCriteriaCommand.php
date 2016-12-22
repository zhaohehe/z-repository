<?php
/*
 * Sometime too hot the eye of heaven shines
 */

namespace Zhaohehe\Repositories\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Zhaohehe\Repositories\Console\Commands\Creators\CriteriaCreator;

/**
 * Class MakeCriteriaCommand
 *
 * @package Zhaohehe\Repositories\Console\Commands
 */
class MakeCriteriaCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var CriteriaCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;


    /**
     * MakeCriteriaCommand constructor.
     *
     * @param CriteriaCreator $creator
     */
    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct();

        $this->creator = $creator;

        $this->composer = app()['composer'];
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();

        $options = $this->option();

        $this->writeRepository($arguments, $options);

        $this->composer->dumpAutoloads();
    }


    /**
     * @param $arguments
     * @param $options
     */
    protected function writeRepository($arguments, $options)
    {
        $repository = $arguments['criteria'];

        $model = $options['model'];

        if ($this->creator->create($repository, $model)) {

            $this->info('Successfully created the criteria class');
        }
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['criteria', InputArgument::REQUIRED, 'The criteria name.']
        ];
    }


    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null],
        ];
    }

}