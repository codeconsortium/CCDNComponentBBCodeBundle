Installing CCDNComponent BBCodeBundle
=====================================

## Dependencies:

1. [CCDNComponent CommonBundle](https://github.com/codeconsortium/CCDNComponentCommonBundle).

## Installation:

Installation takes only 3 steps:

1. Download and install dependencies via Composer.
2. Register bundles with 'AppKernel.php'.
3. Register BB Code Editor Form Theme.

### Step 1: Download and install dependencies via Composer.

Append the following to end of your applications composer.json file (found in the root of your Symfony2 installation):

``` js
// composer.json
{
    // ...
    "require": {
        // ...
        "codeconsortium/ccdn-component-bb-code": "dev-master",
        "codeconsortium/ccdn-component-bb-code-bundle": "dev-master"
    }
}
```

NOTE: Please replace ``dev-master`` in the snippet above with the latest stable branch, for example ``2.0.*``.

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

``` bash
$ php composer.phar update
```

### Step 2: Register bundles with 'AppKernel.php'.

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        new CCDNComponent\BBCodeBundle\CCDNComponentBBCodeBundle(),
        // ...
    );
}
```

### Step 3: Register BB Code Editor Form Theme.

Add this to your app/config/config.yml:

``` yml
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"
    form:
        resources:
            - 'CCDNComponentBBCodeBundle:Form:fields.html.twig'
```

### Translations

If you wish to use default texts provided in this bundle, you have to make sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

## Next Steps.

Installation should now be complete!

If you need further help/support, have suggestions or want to contribute please join the community at [Code Consortium](http://www.codeconsortium.com)

- [Return back to the docs index](index.md).
- [Configuration Reference](configuration_reference.md).
- [Adding New Tags](adding_new_tags.md).
- [Adding New ACL Groups](adding_new_acl_groups.md).
