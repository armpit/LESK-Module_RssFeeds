# RSS Feeds Module

To deploy simply clone the repository from the ```Modules``` directory of the base [LESK](https://github.com/sroutier/laravel-enterprise-starter-kit) install, as shown below:

```
$ cd app/Modules/
$ git clone https://github.com/armpit/LESK-Module_RssFeeds.git RssFeeds
```

Then make sure to optimize the master module definition, from the base directory, with:

```
$ cd ../..
$ ./artisan module:optimize
```

# Dependencies

- [SimplePie](https://github.com/simplepie/simplepie "SimplePie") - used for parsing RSS feeds.

You can install SimplePie using composer from the base directory of your [LESK](https://github.com/sroutier/laravel-enterprise-starter-kit) installation thusly:

```
$ composer require simplepie/simplepie
```

# Prerequisites


# Installing and activating
Once a new module is detected by the framework, a site administrator can go to the "Modules administration" page and first 
initialize the module, then enable it for all authorized users to have access.
