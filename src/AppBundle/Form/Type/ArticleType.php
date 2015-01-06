<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of ArticleType
 *
 * @author T
 */
class ArticleType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('title', 'text');
        $builder->add('content', 'textarea', array("attr" => array("class" => "ckeditor")));
        $builder->add('save', 'submit');
    }
    
    public function getName() {
        return "form_article";
    }
    
}
