<?php

/*
 * This file is part of the CCDNComponent BBCodeBundle
 *
 * (c) CCDN (c) CodeConsortium <http://www.codeconsortium.com/>
 *
 * Available on github <http://www.github.com/codeconsortium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CCDNComponent\BBCodeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 *
 * @category CCDNComponent
 * @package  BBCodeBundle
 *
 * @author   Reece Fowell <reece@codeconsortium.com>
 * @license  http://opensource.org/licenses/MIT MIT
 * @version  Release: 2.0
 * @link     https://github.com/codeconsortium/CCDNComponentBBCodeBundle
 *
 */
class BBEditorFormType extends AbstractType
{
    /**
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'attr' => array(
                    'acl_group' => 'default',
                ),
            )
        );
    }

    /**
     *
     * @return null|string|\Symfony\Component\Form\FormTypeInterface
     */
    public function getParent()
    {
        return 'textarea';
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return 'bb_editor';
    }
}
