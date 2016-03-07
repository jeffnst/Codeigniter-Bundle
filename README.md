Codeigniter Bundle (BETA)
-------------------------

**Codeigniter Bundle** implements a simple *Modular Pattern* into Codeigniter Framework using groups of independent components, typically model, controller and view, arranged in application modules (sub-directory) that can be dropped into other Codeigniter applications.

Since the introduction of Codeigniter v2.0 there has been support for packages built right inside Codeigniter called *Third-party Application Packages*, this extension adds to this packages the extra functionality they needed for full modular support.

## Why should I use Bundles?

Every Codeigniter proyect development process starts slow because there is so much configuration to do before you can start writing the actual code. 

Here's a list of key advantages:

* Modularization: reduction of dependencies between the parts of the application.
* Organization: get everything organized in different folders.
* Reusability: easy to reuse.
* Extendibility: makes the application more extensible without sacrificing ease of maintenance.

## Inspiration

**Codeigniter Bundle** is inspired by this authors:

* [wiredesignz/Codeigniter-modular-extensions-hmvc](https://bitbucket.org/wiredesignz/Codeigniter-modular-extensions-hmvc)
* [jenssegers/Codeigniter-hmvc-modules](https://github.com/jenssegers/Codeigniter-hmvc-modules)

**Codeigniter Bundle** aims to use all available code from Codeigniter to create a simple but functional modular system.

## Features

* Autoload packages (libraries, driver, helper files, custom config files, language files and models).
* Extend your controllers, models, helpers and libraries.
* Create/Run migrations per bundle using [CLI Craftsman](https://github.com/davidsosavaldes/Craftsman).
* Create templates and assets (like css, js, images, etc) per bundle using [Attire](https://github.com/davidsosavaldes/Attire).

## How to use

To use **Bundle** functionality, controllers must extend the `Bundle_Controller`.

    <?php
    class Foo extends Bundle_Controller {}

<!-- All controllers can have an `$autoload` attribute, which holds an array of items loaded in the constructor. This can be used together with `module/config/autoload.php`, however using the $autoload attribute only works for that specific controller. -->

Each bundle may contain a `config/routes.php` file where routes and a default controller can be defined for that module using:

    <?php
    $route['<bundle_name>'] = '<controller_name>';

## Observations

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
          $this->load->bundle('<fighters_bundle>');
          $this->load->model('fighters_model'); # inside <fighters_bundle>/models/
      }
    }
    

## Installation

* Create a clean CI project (Check [Codeigniter-installer](https://github.com/davidsosavaldes/Codeigniter-Installer)).
* Install with composer `dsv/codeigniter-bundle`
* Set $config[‘base_url’] correctly for your installation
* Copy Bundle Extensions core files into `application/core`
* Copy Bundle Config file into `application/config`
* Access the URL `/index.php/welcome`
* Everything looks good? Good...let's continue

## Create a new Bundle

* Create a bundle directory structure `application/bundles/WelcomeBundle/controllers`
* Move controller `application/controllers/Welcome.php` to `application/bundles/WelcomeBundle/controllers/Welcome.php`
* Access the URL `/index.php/welcome` => shows Welcome to Codeigniter
* Create directory `application/modules/welcome/views`
* Move view `application/views/welcome_message.php` to `application/bundles/WelcomeBundle/views/welcome_message.php`
* Access the URL `/index.php/welcome` => shows Welcome to Codeigniter

You should now have a running **Bundle installation**.

## Contributions

**Codeigniter Bundle** project welcomes and depends on contributions from all developers in the Codeigniter community.

Contributions can be made in a number of ways, a few examples are:

* Code patches via pull requests
* Documentation improvements
* Bug reports and patch reviews
* Reporting an Issue

Please include as much detail as you can. Let us know your platform and Codeigniter version. If the problem is visual (for example a design issue) please add a screenshot and if you get an error please include the the full error and traceback.

### Submitting Pull Requests

Once you are happy with your changes or you are ready for some feedback, push it to your fork and send a pull request. For a change to be accepted it will most likely need to have tests and documentation if it is a new feature.
