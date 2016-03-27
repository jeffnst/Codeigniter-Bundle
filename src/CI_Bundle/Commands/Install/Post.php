<?php

namespace CI_Bundle\Commands\Install;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

/**
 * Post Command
 *
 * @package     CI-Bundle
 * @author      David Sosa Valdes
 * @link        https://github.com/davidsosavaldes/Craftsman
 * @copyright   Copyright (c) 2016, David Sosa Valdes.
 * @version     1.0.0
 */
class Post extends Command
{   
    /**
     * InputInterface instance
     * @var object
     */
    protected $_input;

    /**
     * OutputInterface instance
     * @var object
     */
    protected $_output;

    /**
     * Style instance
     * @var object
     */
    protected $_style;

    /**
     * Configure default attributes
     */
    protected function configure()
    {
        $this
            ->setName('install:post')
            ->setDescription('Post install command');
    }

    /**
     * Initialize the objects
     * 
     * @param  InputInterface  $input 
     * @param  OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->_input  = $input;
        $this->_output = $output;
        $this->_style  = new SymfonyStyle($input, $output);
    }

    /**
     * Execute the command
     * 
     * @param InputInterface  $input  
     * @param OutputInterface $output 
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->title('CI-Bundle: Post-Install');

        $filesystem      = new Filesystem();
        
        $app_core_path   = APPPATH.'core/';
        $app_config_path = APPPATH.'config/';
        $app_bundle_path = APPPATH.'bundles/';
        $app_config_file = $app_config_path.'config.php';
        
        $core_files      = glob(BUNDLEPATH.'core/*.php');
        $config_files    = glob(BUNDLEPATH.'config/*.php');

        try 
        {
            $this->section('Core Files');
            
            $this->listing($core_files);    

            if ($this->confirm('Copy core files to ['.$app_core_path.']?')) 
            {
                foreach ($core_files as $file) 
                {
                    if (! $filesystem->copy($file, $app_core_path.basename($file))); 
                    {
                        $this->warning('['.basename($file).'] Ignored...');
                    }
                }
                $this->success('Core files moved!');
            }   

            $this->section('Config Files'); 

            $this->listing($config_files);    

            if ($this->confirm('Move config files to ['.$app_config_path.']?')) 
            {
                foreach ($config_files as $file) 
                {
                    if (! $filesystem->copy($file, $app_config_path.basename($file))); 
                    {
                        $this->warning('['.basename($file).'] Ignored...');
                    }
                }                
                $this->success('Config files moved!');
            }

            if ($filesystem->exists($app_config_file)) 
            {
                if ($this->confirm('Replace the sublcass prefix in ['.$app_config_file.']')) 
                {
                    $_config = file_get_contents($app_config_file);
                    $block   = '$config["subclass_prefix"] = "Bundle_";';
                    
                    $filesystem->dumpFile(
                        $app_config_file, 
                        preg_replace(
                            "/^.*('subclass_prefix').*$/m", 
                            str_replace('"', "'", $block), 
                            $_config
                        )
                    );                    
                }
            }
            else
            {
                $this->warning('The configuration file does not exist on ['.$app_config_file.'].');
            }

            $this->section('Bundle directory');    

            if (! $filesystem->exists($app_bundle_path)) 
            {
                if ($this->confirm('Create the directory ['.$app_bundle_path.']')) 
                {
                    if ($filesystem->mkdir($app_bundle_path)) 
                    {
                         $this->success('Bundle directory created!');
                    }
                    else
                    {
                         $this->warning("Bundle directory couldn't be created!");
                    }
                }            
            }
            else
            {
                $this->warning('Bundle directory already exist!');
            }
        } 
        catch (IOExceptionInterface $e) 
        {
            $this->error($e->getMessage());
        }        
    }

    /**
     * Call some methods easily
     * 
     * @param  string $name      
     * @param  mixed  $arguments   
     */
    public function __call($name = '', $arguments = NULL)
    {
        switch ($name) 
        {
            case 'title':
            case 'section':
            case 'text':
            case 'listing':
            case 'table':
            case 'newLine':
            case 'note':
            case 'caution':
            case 'progressStart':
            case 'progressAdvance':
            case 'progressFinish':
            case 'ask':
            case 'askHidden':
            case 'confirm':
            case 'choice':
            case 'success':
            case 'warning':
            case 'error':
                return call_user_func_array(array($this->_style, $name), $arguments);

            case 'getArgument':
                return call_user_func_array(array($this->_input,'getArgument'), $arguments);

            case 'getOption':
                return call_user_func_array(array($this->_input, 'getOption'), $arguments);
        }
    }
}

/* End of file PostCommand.php */
/* Location: .//Users/dsv/Sites/repositories/ci-bundle/src/Install/PostCommand.php */
