Codeigniter Bundle
-------------------------

**Codeigniter Bundle** implements a simple *Modular Pattern* into Codeigniter Framework using groups of independent components, typically model, controller and view, arranged in application modules (sub-directory) that can be dropped into other Codeigniter applications.

Since the introduction of Codeigniter v2.0 there has been support for packages built right inside Codeigniter called *Third-party Application Packages*, this extension adds to this packages the extra functionality they needed for full modular support.

## Why should I use Bundles?

Every Codeigniter project development process starts slow because there is so much configuration to do before you can start writing the actual code. 

Here's a list of key advantages:

* Modularization: reduction of dependencies between the parts of the application.
* Organization: get everything organized in different folders.
* Reusability: easy to reuse.
* Extendibility: makes the application more extensible without sacrificing ease of maintenance.

## Inspiration

**Codeigniter Bundle** is inspired by this author's:

* [wiredesignz/Codeigniter-modular-extensions-hmvc](https://bitbucket.org/wiredesignz/Codeigniter-modular-extensions-hmvc)
* [jenssegers/Codeigniter-hmvc-modules](https://github.com/jenssegers/Codeigniter-hmvc-modules)

This extension aims to use all available code from Codeigniter to create a simple but functional modular system.

## Features

* Autoload packages (hooks, libraries, driver, helper files, custom config files, language files and models).
* Extend your controllers, models, helpers and libraries.
* Create/Run migrations per bundle using [Craftsman](https://github.com/davidsosavaldes/Craftsman).
* Create templates and assets (css, js, fonts, img, etc) per bundle using [Attire](https://github.com/davidsosavaldes/Attire).

## Installation 

With composer:

    composer require dsv/codeigniter-bundle

After installation run the `post-install-command`:

    php vendor/bin/ci-bundle install:post
    
[![asciicast](https://asciinema.org/a/45176.png)](https://asciinema.org/a/45176)

## How to use

Bundles used in your applications must reside in `path/to/application/bundles` directory and be enabled by registering them in the `path/to/application/config/bundles.php` file.

**Example**

If you have an "admin" bundle located in `path/to/application/bundles/AdminBundle` that you want to handle requests with URIs that begin with "admin", the configuration file will look like this:

    'admin' => array(
    	'location' => 'AdminBundle',
    	'route'    => 'admin'
    ),

Note that the "location" is relative to the "bundles" directory.

Now the bundle will be recognized by Codeigniter and will be able to respond to requests beginning with "admin".

## First steps

To use **Bundle** functionality, controllers must extend the `Bundle_Controller`.

    <?php
    class Foo extends Bundle_Controller {}

<!-- All controllers can have an `$autoload` attribute, which holds an array of items loaded in the constructor. This can be used together with `module/config/autoload.php`, however using the $autoload attribute only works for that specific controller. -->

Each bundle may contain a `config/routes.php` file where routes and a default controller can be defined using:

    <?php
    $route['<bundle_name>'] = '<controller_name>';

After `CI-Bundle` installation, controllers may be loaded from: 

* `path/to/application/controllers` sub-directories.
* `path/to/application/bundle/controllers` sub-directories.

accordingly to the URI route:

* `http://codeigniter.dev/index.php/welcome` -> `application/controllers/welcome`
* `http://codeigniter.dev/index.php/<bundle_name>/welcome` -> `path/to/bundles/<bundle_name>/welcome`

Models and libraries can also be loaded from sub-directories in their respective application directories.

Resources may be cross loaded between bundles:

    <?php
    class Foo extends Bundle_Controller 
    {
      function index()
      {
          $this->load->bundle('fightersBundle');
          $this->load->model('fighters_model'); # inside path/to/bundles/fightersBundle/models/fighters_model
      }
    }

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
