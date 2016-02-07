Codeigniter Bundle
-----------------

Codeigniter Bundle implements Hierarchical Model View Controller(HMVC) pattern into Codeigniter Framework. HMVC is an evolution of the MVC pattern using groups of independent components, typically model, controller and view, arranged in an application modules sub-directory that can be dropped into other CodeIgniter applications.

##Why should I use HMVC Pattern?


Every Codeigniter proyect development process starts slow because there is so much configuration to do before you can start writing the code. Here's a list of key advantages to implementing the HMVC pattern in your development cycle:
* [M]odularization. Reduction of dependencies between the disparate parts of the application.
* [O]rganization. Having a folder for each of the relevant triads makes for a lighter work load.
* [R]eusability. By nature of the design it is easy to reuse nearly every piece of code.
* [E]xtendibility. Makes the application more extensible without sacrificing ease of maintenance.

These advantages will allow you to get MORE out of your application with less headaches.

##Inspiration

Codeigniter Bundle is inspired by this authors:
* [wiredesignz/codeigniter-modular-extensions-hmvc](https://bitbucket.org/wiredesignz/codeigniter-modular-extensions-hmvc)
* [jenssegers/codeigniter-hmvc-modules](https://github.com/jenssegers/codeigniter-hmvc-modules)

These works are a valuable effort to implement a HMVC environment in Codeigniter, however, Codeigniter Bundle intends not to reinvent the wheel and use all the available Codeigniter code to create a simple but functional modular system.

##Features

Since the introduction of CodeIgniter 2.0 there has been support for packages built right inside CodeIgniter called Third-party Application Packages. This extension adds to this packages the extra functionality they need for full HMVC support.
Here's a list of features:

* Autoload packages (libraries, driver, helper files, custom config files, language files and models)
* Extend framework controllers, config files, libraries or helpers.
* Create/Run migrations per module using [CLI Craftsman](https://github.com/davidsosavaldes/Craftsman).
* Create templates and assets (like css, js, images, etc) per module using [Attire](https://github.com/davidsosavaldes/Attire).
