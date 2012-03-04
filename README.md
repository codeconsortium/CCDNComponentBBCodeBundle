CCDNComponent BBCodeBundle README.
==================================
 

Notes:  
------
  
This bundle is for the symfony framework and thusly requires Symfony 2.0.x and PHP 5.3.6
  
This project uses Doctrine 2.0.x and so does not require any specific database.
  

This file is part of the CCDNComponent BBCodeBundle

(c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/> 

Available on github <http://www.github.com/codeconsortium/>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.


Dependencies:
-------------

[CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle)
  
Installation:
-------------

1) Download and install the dependencies.
   
   You can set deps to include:

```sh
[CCDNComponentCommonBundle]
    git=http://github.com/codeconsortium/CommonBundle.git
    target=/bundles/CCDNComponent/CommonBundle

[CCDNComponentBBCodeBundle]
    git=http://github.com/codeconsortium/BBCodeBundle.git
    target=/bundles/CCDNComponent/BBCodeBundle
```
add to your autoload:

```php
    'CCDNComponent'    => __DIR__.'/../vendor/bundles',
```
and then run `bin/vendors install` script.

2) In your AppKernel.php add the following bundles to the registerBundles method array:  

```php
	new CCDNComponent\CommonBundle\CCDNComponentCommonBundle(),
	new CCDNComponent\BBCodeBundle\CCDNComponentBBCodeBundle(),
```

3) Symlink assets to your public web directory by running this in the command line:

```sh
	php app/console assets:install --symlink web/
```
	
Then your done, if you need further help/support, have suggestions or want to contribute please join the community at [www.codeconsortium.com](http://www.codeconsortium.com)
