Adding New Tags.
================

To add new Tags, you will need to add a new class to your bundle like the following:

``` php
<?php
namespace Acme\DemoBundle\Component\BBCode;

class TagIntegrator
{
	protected $tags = array();
	
	public function __construct($tags)
	{
		$this->tags = $tags;
	}
	
    /**
     *
     * @access public
     */
    public function build()
    {
		return $this->tags;
	}
}
```

The array should have the format of something like:

``` YAML
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Image',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Vimeo',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Asset\Youtube',

'\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\Code',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\CodeGroup',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Block\Quote',

'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Bold',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading1',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading2',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Heading3',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Italic',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Link',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListItem',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListOrdered',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\ListUnordered',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Strike',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\SubScript',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\SuperScript',
'\CCDNComponent\BBCode\Node\Lexeme\Tag\Format\Underline',
```

Pass the array into the constructor of the class.

You will need to make the class a service and tag it like so:

``` YAML
parameters:

    acme_demo.component.bb_code.tag_integrator.class: Acme\DemoBundle\Component\BBCode\TagIntegrator

services:

    acme_demo.component.bb_code.tag_integrator:
        class: %acme_demo.component.bb_code.tag_integrator.class%
        arguments:
            - %acme_demo.bb_parser.tags%
        tags:
            - { name: ccdn_component_bb_code.tag }
```

- [Return back to the docs index](index.md).
- [Adding New ACL Groups](adding_new_acl_groups.md).
