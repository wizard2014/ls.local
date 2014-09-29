<?php

namespace Application\Model;

use Zend\Form\Annotation;

/**
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Name("check")
 * @Annotation\Attributes({"class":"form-inline"})
 */
class Check
{
    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Название: "})
     * @Annotation\Attributes({"class":"form-control name","autocomplete":"off","placeholder":"Имя Фамилия (латынью)"})
     * @Annotation\Filter({"name":"StringTrim"})
     */
    public $name;

    /**
     * @Annotation\Type("Zend\Form\Element\Text")
     * @Annotation\Options({"label":"Номер: "})
     * @Annotation\Attributes({"class":"form-control number","autocomplete":"off","placeholder":"Номер дела"})
     * @Annotation\Filter({"name":"StringTrim"})
     */
    public $number;

    /**
     * @Annotation\Type("Zend\Form\Element\Submit")
     * @Annotation\Attributes({"value":"Submit"})
     * @Annotation\Attributes({"class":"btn btn-primary"})
     */
    public $submit;
}