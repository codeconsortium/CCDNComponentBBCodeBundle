Installing CCDNComponent BBCodeBundle 1.0
==========================================

## Dependencies:

1. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CommonBundle)
2. [lib-geshi](https://github.com/codeconsortium/lib-geshi)

## Installation:

Installation takes only 6 steps:

1. Download and install the dependencies.
2. Register bundles with autoload.php.
3. Register bundles with AppKernel.php.  
4. Run vendors install script.
5. Symlink assets to your public web directory.
6. Warmup the cache.

### Step 1: Download and install the dependencies.
   
Append the following to end of your deps file (found in the root of your Symfony2 installation):

``` ini
[lib-geshi]
	git=http://github.com/codeconsortium/lib-geshi.git
	target=/geshi

[CCDNComponentBBCodeBundle]
    git=http://github.com/codeconsortium/BBCodeBundle.git
    target=/bundles/CCDNComponent/BBCodeBundle

```

### Step 2: Register bundles with autoload.php.

Add the following to the registerNamespaces array in the method by appending it to the end of the array.

``` php
// app/autoload.php
$loader->registerNamespaces(array(
    'CCDNComponent'    => __DIR__.'/../vendor/bundles',
	**...**
));
```

Add the following to the registerPrefixes array in the method by appending it to the end of the array.

``` php
// app/autoload.php
$loader->registerPrefixes(array(
	'Geshi_'		   => __DIR__.'/../vendor/geshi/lib',
	**...**
));
```

### Step 3: Register bundles with AppKernel.php.  

In your AppKernel.php add the following bundles to the registerBundles method array:  

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
		new CCDNComponent\BBCodeBundle\CCDNComponentBBCodeBundle(),
		**...**
	);
}
```

### Step 4: Run vendors install script.

From your projects root Symfony directory on the command line run:

``` bash
$ php bin/vendors install
```

### Step 5: Symlink assets to your public web directory.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console assets:install --symlink web/
```

### Step 6: Warmup the cache.

From your projects root Symfony directory on the command line run:

``` bash
$ php app/console cache:warmup
```

## Next Steps.

Installation should now be complete!

If you need further help/support, have suggestions or want to contribute please join the community at [Code Consortium](http://www.codeconsortium.com)

- [Return back to the docs index](index.md).
- [Configuration Reference](configuration_reference.md).
