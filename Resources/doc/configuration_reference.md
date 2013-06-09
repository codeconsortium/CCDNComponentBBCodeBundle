CCDNComponent BBCodeBundle Configuration Reference.
===================================================

All available configuration options are listed below with their default values.

``` yml
ccdn_component_bb_code:
    form:
        type:
            bb_editor:
                class:                CCDNComponent\BBCodeBundle\Form\Type\BBEditorFormType
    component:
        twig_extension:
            parse_bb:
                class:                CCDNComponent\BBCodeBundle\Component\TwigExtension\BBCodeExtension
        chain:
            tag:
                class:                CCDNComponent\BBCodeBundle\Component\Chain\TagChain
            acl:
                class:                CCDNComponent\BBCodeBundle\Component\Chain\ACLChain
        bootstrap:
            class:                    CCDNComponent\BBCodeBundle\Component\BBCodeEngine
        engine:
            bootstrap:
                class:                CCDNComponent\BBCode\Bootstrap
            table_container:
                class:                CCDNComponent\BBCode\Engine\Table\TableContainer
            scanner:
                class:                CCDNComponent\BBCode\Engine\Scanner
            lexer:
                class:                CCDNComponent\BBCode\Engine\Lexer
            parser:
                class:                CCDNComponent\BBCode\Engine\Parser
    editor:
        enable:                       true
    parser:
        enable:                       true
```

- [Return back to the docs index](index.md).
- [Adding New Tags](adding_new_tags.md).
- [Adding New ACL Groups](adding_new_acl_groups.md).
