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
    
1) Create the directory src/CCDNComponent in your Symfony directory.
  
2) Add the BBCodeBundle src/CCDNComponent directory.  

3) In your AppKernel.php add the following bundles to the registerBundles method array:  

	new CCDNComponent\BBCodeBundle\CCDNComponentBBCodeBundle(),    
	  
4) Symlink assets to your public web directory by running this in the command line:

	php app/console assets:install --symlink web/

Then your done, if you need further help/support, have suggestions or want to contribute please join the community at [www.codeconsortium.com](http://www.codeconsortium.com)
