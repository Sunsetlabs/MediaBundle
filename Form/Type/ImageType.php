<?php

namespace Sunsetlabs\MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('file');
        $builder->addEventListener(FormEvents::POST_SUBMIT, array($this, 'postBind'));
    }

    public function postBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $parent = $form->getParent();

        if ($data and !$data->getId() and $parent) {
            $class = $this->getBindDataClass($parent);
            $data->setObjClass($class);
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sunsetlabs\MediaBundle\Entity\Image',
        ));
    }

    public function getName()
    {
        return 'image_type';
    }

    protected function getBindDataClass($form)
    {
        $class = get_class($form->getData());
        $class = explode("\\", $class);
        $class = end($class);

        if ($class == 'PersistentCollection' or $class == 'ArrayCollection') {
            return $this->getBindDataClass($form->getParent());
        }

        return $class;
    }
}