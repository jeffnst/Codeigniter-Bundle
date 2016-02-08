UNDER CONSTRUCTION (02/08/2016)
-------------------------------

Codeigniter Bundle
-----------------

Codeigniter-Bundle implements Hierarchical Model View Controller(HMVC) pattern into Codeigniter Framework. HMVC is an evolution of the MVC pattern using groups of independent components, typically model, controller and view, arranged in an application modules (sub-directory) that can be dropped into other CodeIgniter applications.

##Why should I use Bundles?

Every Codeigniter proyect development process starts slow because there is so much configuration to do before you can start writing the code. 

Here's a list of key advantages to implementing the HMVC pattern in your development cycle:
* Modularization. Reduction of dependencies between the parts of the application.
* Organization. Get everything organized in different folders.
* Reusability. Easy to reuse.
* Extendibility. Makes the application more extensible without sacrificing ease of maintenance.

##Inspiration

Codeigniter-Bundle is inspired by this authors:
* [wiredesignz/codeigniter-modular-extensions-hmvc](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc)
* [jenssegers/codeigniter-hmvc-modules](https://github.com/jenssegers/codeigniter-hmvc-modules)

These works are a valuable effort to implement a HMVC environment in Codeigniter, however, Codeigniter-Bundle intends to use all the available Codeigniter Code to create a simple but functional modular system.

##Features

Since the introduction of CodeIgniter 2.0 there has been support for packages built right inside CodeIgniter called Third-party Application Packages. This extension adds to this packages the extra functionality they needed for full modular support.

Here's a list of features:

* Autoload packages (libraries, driver, helper files, custom config files, language files and models)
* Extend Codeigniter framework controllers, config files, libraries or helpers.
* Create/Run migrations per bundle using [CLI Craftsman](https://github.com/davidsosavaldes/Craftsman).
* Create templates and assets (like css, js, images, etc) per bundle using [Attire](https://github.com/davidsosavaldes/Attire).
 
##Notes

To use Bundle functionality, controllers must extend the `Bundle_Controller`.

    <?php
    class Foo extends Bundle_Controller 
    {
      function __construct()
      {
          parent::__construct();
      }
    }
Each bundle may contain a `config/routes.php` file where routing and a default controller can be defined for that module using:

    <?php
    $route['<bundle_uri>'] = '<controller_name>';
    $route['default_controller'] = '<controller_name>';
    
.....
