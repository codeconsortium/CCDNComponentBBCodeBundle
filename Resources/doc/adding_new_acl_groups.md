Adding New ACL Groups.
======================

To add new ACL Groups, you will need to add a new class to your bundle like the following:

``` php
<?php
namespace Acme\YourBundle\Component\BBCode;

class ACLIntegrator
{
	protected $acl = array();
	
	public function __construct($acl)
	{
		$this->acl = $acl;
	}
	
    /**
     *
     * @access public
     */
    public function build()
    {
		return $this->acl;
	}
}
```

The array should have the format of something like:

``` YAML
forum_post:
    groups:
        white_list: [*]
        black_list: []
    tags:
        white_list: [*]
        black_list: [VIMEO]
message_body:
    groups:
        white_list: [*]
        black_list: []
    tags:
        white_list: [*]
        black_list: [VIMEO]
profile_biography:
    groups:
        white_list: [*]
        black_list: []
    tags:
        white_list: [*]
        black_list: [VIMEO]
```

You can create an ACL group for whatever section of your site you like.

Pass the array into the constructor of the class.

You will need to make the class a service and tag it like so:

``` YAML
parameters:

    acme_demo.component.bb_code.acl_integrator.class: Acme\DemoBundle\Component\BBCode\ACLIntegrator

services:

    acme_demo.component.bb_code.acl_integrator:
        class: %acme_demo.component.bb_code.acl_integrator.class%
        arguments:
            - %acme_demo.bb_parser.acl%
        tags:
            - { name: ccdn_component_bb_code.acl }
```

- [Return back to the docs index](index.md).
- [Adding New Tags](adding_new_tags.md).
