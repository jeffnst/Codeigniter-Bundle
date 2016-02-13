UNDER CONSTRUCTION (02/08/2016)
-------------------------------

Codeigniter Bundle
-----------------

Codeigniter-Bundle implements a Modular pattern into Codeigniter Framework using groups of independent components, typically model, controller and view, arranged in application modules (sub-directory) that can be dropped into other CodeIgniter applications.

Since the introduction of CodeIgniter 2.0 there has been support for packages built right inside CodeIgniter called *Third-party Application Packages*. This extension adds to this packages the extra functionality they needed for full modular support.

##Why should I use Bundles?

Every Codeigniter proyect development process starts slow because there is so much configuration to do before you can start writing the code. 

Here's a list of key advantages:
* Modularization. Reduction of dependencies between the parts of the application.
* Organization. Get everything organized in different folders.
* Reusability. Easy to reuse.
* Extendibility. Makes the application more extensible without sacrificing ease of maintenance.

##Inspiration

Codeigniter-Bundle is inspired by this authors:
* [wiredesignz/codeigniter-modular-extensions-hmvc](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc)
* [jenssegers/codeigniter-hmvc-modules](https://github.com/jenssegers/codeigniter-hmvc-modules)

Codeigniter-Bundle intends to use all the available Codeigniter code to create a simple but functional modular system.

##Features

* Autoload packages (libraries, driver, helper files, custom config files, language files and models)
* Extend your controllers, models, helpers and libraries.
* Create/Run migrations per bundle using [CLI Craftsman](https://github.com/davidsosavaldes/Craftsman).
* Create templates and assets (like css, js, images, etc) per bundle using [Attire](https://github.com/davidsosavaldes/Attire).

To use Bundle functionality, controllers must extend the `Bundle_Controller`.

    <?php
    class Foo extends Bundle_Controller 
    {
      protected $autoload = array(
        'helper'    => array('url', 'form'),
        'libraries' => array('email'),
      );
     
      function __construct()
      {
          parent::__construct();
      }
    }

All controllers can have an `$autoload` attribute, which holds an array of items loaded in the constructor. This can be used together with `module/config/autoload.php`, however using the $autoload attribute only works for that specific controller.

Each bundle may contain a `config/routes.php` file where routing and a default controller can be defined for that module using:

    <?php
    $route['<bundle_name>'] = '<controller_name>';

##Observations

Controllers may be loaded from: 

* `application/controllers` sub-directories.
* `bundle/controllers` sub-directories.

Models and libraries can also be loaded from sub-directories in their respective application directories.

Resources may be cross loaded between modules. Example:

    <?php
    class Foo extends Bundle_Controller 
    {
      function index()
      {
          add_bundle_package('<fighters_bundle>');
          $this->load->model('fighter_model'); # inside <fighters_bundle>/models/
      }
    }
    

##Installation

* Create a clean CI project (Check [Codeigniter-installer](https://github.com/davidsosavaldes/Codeigniter-Installer)).
* Install with composer `dsv/codeigniter-bundle`
* Set $config[‘base_url’] correctly for your installation
* Rename your index.php file like `index.php.old`
* Copy the `third_party/CI-Bundle/index-dist.php` into into FCPATH (where your application and system folders resides) and rename it like `index.php`
* Copy Bundle Extensions core files into `application/core`
* Copy Bundle Config file into `application/config`
* Copy Bundle Helper file into `application/helpers`
* Access the URL /index.php/welcome

Everything looks good? Good...let's continue

##Create a new Bundle

* Create a bundle directory structure `application/bundles/welcomeBundle/controllers`
* (TESTING) Move controller `application/controllers/welcome.php` to `application/bundles/welcomeBundle/controllers/welcome.php`
* Access the URL /index.php/welcome => shows Welcome to CodeIgniter
* Create directory application/modules/welcome/views
* Move view application/views/welcome_message.php to application/modules/welcome/views/welcome_message.php
* Access the URL /index.php/welcome => shows Welcome to CodeIgniter

You should now have a running the Bundle installation.
