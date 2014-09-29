<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Form;

class UploadController extends AbstractActionController
{
    protected $imageFolder = array(
        './public/img/aside/',
        './public/img/main/',
    );

    public function indexAction()
    {
        $form = new Form\ImgUploadForm('img-file-form');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($data);
            if ($form->isValid()) {
                $form->getData();

                return $this->redirect()->toRoute('edit');
            }
        }

        $this->layout('layout/image');

        return new ViewModel(array(
            'form' => $form
        ));
    }

    public function viewAction()
    {
        $resultArray = array();

        foreach ($this->imageFolder as $folder) {
            foreach (glob($folder . '*') as $filename) {
                $resultArray[] = substr($filename, 8);
            }
        }

        $this->layout('layout/image');

        return new ViewModel(array(
            'result' => $resultArray
        ));
    }
}