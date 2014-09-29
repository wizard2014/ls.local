<?php

namespace Application\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("email-form")
 */
class Email
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Тема письма: "})
     * @Annotation\Attributes({"class":"form-control subject","placeholder":"Тема письма","required":"true"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     */
    public $subject;

    /**
     * @Annotation\Type("Zend\Form\Element\Textarea")
     * @Annotation\Options({"label":"Текст письма: "})
     * @Annotation\Attributes({"class":"form-control text","autocomplete":"off","placeholder":"Пожалуйста, укажите свои контактные данные","required":"true"})
     * @Annotation\Filter({"name":"StringTrim"})
     * @Annotation\Filter({"name":"StripTags"})
     */
    public $text;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     * @Annotation\Attributes({"class":"btn btn-primary"})
     */
    public $submit;
}