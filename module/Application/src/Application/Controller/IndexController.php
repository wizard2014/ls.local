<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Form\Annotation\AnnotationBuilder;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;
use Zend\Http\Client;
use Zend\Mail;

use Application\Model\Check;
use Application\Model\Email;

class IndexController extends AbstractActionController
{
    const MAIN_CONTENT = 'Application\Entity\MainContent';

    /**
     * @var null|object
     */
    protected $em        = null;
    protected $doc       = null;
    protected $video     = null;
    protected $imgFolder = './public/img/aside/';
    private $cache; //memory
    private $page = array(
        'home'          => 1,
        'about-us'      => 2,
        'paperwork'     => 3,
        'about-romania' => 4,
        'attention'     => 5,
        'contacts'      => 6,
        'reviews'       => 7
    );

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        $form = $this->getForm(new Check());

        $content = $this->getContent($this->page['home']);

        $checkedData = null;
        $checkFlag   = false;

        $request = $this->getRequest();
        if ($request->isPost()) {
            $firstName  = null;
            $lastName   = null;
            $number     = null;

            $postData = $request->getPost();

            if (isset($postData->name)) {
                $explode = explode(' ', $postData->name);

                $firstName  = $explode[0];
                $lastName   = $explode[1];
            }

            if (isset($postData->number)) {
                $number = (int)$postData->number;
            }

            $checkFlag = true;

            $checkedData = $this->checkOrder($firstName, $lastName, $number);

            return new JsonModel(array(
                'result' => $checkedData
            ));
        }

        $viewModel = new ViewModel(array(
            'flashMessages' => $this->flashMessenger()->getMessages(),
            'form'          => $form,
//            'checkedData'   => $checkedData,
//            'checkflag'     => $checkFlag,
            'content'       => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function aboutUsAction()
    {
        $content = $this->getContent($this->page['about-us']);

        $viewModel = new ViewModel(array(
            'content' => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function paperworkAction()
    {
        $content = $this->getContent($this->page['paperwork']);

        $viewModel = new ViewModel(array(
            'content' => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function aboutRomaniaAction()
    {
        $content = $this->getContent($this->page['about-romania']);

        $viewModel = new ViewModel(array(
            'content' => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function attentionAction()
    {
        $content = $this->getContent($this->page['attention']);

        $viewModel = new ViewModel(array(
            'content' => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
    public function contactsAction()
    {
        $form = $this->getForm(new Email());

        $content = $this->getContent($this->page['contacts']);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $postData = $request->getPost();
            $form->setData($postData);

            if ($form->isValid()) {
                $mail = new Mail\Message();

                $mail->setBody($postData->text)
                     ->setFrom('legalservice@mail.com', 'From Site')
                     ->addTo('Dmitrij.Brazhnyk@yandex.by', 'Dmitrij Brazhnyk')
                     ->setSubject($postData->subject);

                try {
                    $transport = new Mail\Transport\Sendmail();
                    $transport->send($mail);

                    $this->flashMessenger()->addMessage('<div class="email"><h5 class="success center-block">
                                                    Ваше письмо отправлено, мы ответим вам в ближайшее время!
                                                    <span class="glyphicon glyphicon-remove remove"></span></h5></div>');

                    return $this->prg('/', true);
                } catch(\Exception $e) {
                    $this->flashMessenger()->addMessage('<div class="email"><h5 class="danger email center-block">
                                                    При отправки сообщения, произошла ошибка.
                                                    Свяжитесь с нами позвонив по телефону.
                                                    <span class="glyphicon glyphicon-remove remove"></span></h5></div>');

                    return $this->prg('/', true);
                }

            }
        }

        $viewModel = new ViewModel(array(
            'form'    => $form,
            'content' => html_entity_decode($content->getText())
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @return ViewModel
     */
//    public function reviewsAction()
//    {
//        $content = $this->getContent($this->page['reviews']);
//
//        $viewModel = new ViewModel(array(
//            'content' => html_entity_decode($content->getText())
//        ));
//        $viewModel->addChild($this->getAside(), 'aside');
//
//        return $viewModel;
//    }

    /**
     * @return ViewModel
     */
    public function asideAction()
    {
        $imgs = array();

        $count = 0;
        foreach (glob($this->imgFolder . '*') as $img) {
            if (strpos($img, '.min') === false) {
                $imgs[$count]['origin'] = substr($img, 8);
            } else {
                $imgs[$count]['min']    = substr($img, 8);

                $count++;
            }
        }

        return new ViewModel(array(
            'docs' => $this->getDoc(),
            'imgs' => $imgs
        ));
    }

    /**
     * @return ViewModel
     */
    public function orderAction()
    {
        $result = array();

        $docs = $this->getDoc();

        // href | title
        foreach ($docs as $doc) {
            if ($this->cache->getItem('date') !== $doc->getDate()->format('d.m.Y')) {
                $result[] = array(
                    'date'  => $doc->getDate()->format('d.m.Y'),
                    'link'  => array($doc->getLink() . '|' .$doc->getTitle()),
                );
            } else {
                array_push($result[count($result) - 1]['link'],  $doc->getLink() . '|' .$doc->getTitle());
            }


            $this->cache->setItem('date', $doc->getDate()->format('d.m.Y'));
        }

        $viewModel = new ViewModel(array(
            'fullDocsList' => $result
        ));
        $viewModel->addChild($this->getAside(), 'aside');

        return $viewModel;
    }

    /**
     * @param mixed $doc
     */
    public function setDoc($doc)
    {
        $this->doc = $doc;
    }

    /**
     * @return mixed
     */
    public function getDoc()
    {
        return $this->doc;
    }

    /**
     * @param mixed $video
     */
    public function setVideo($video)
    {
        $this->video = $video;
    }

    /**
     * @return mixed
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * @param mixed $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param $firstName
     * @param $lastName
     * @param $number
     * @return array
     */
    protected function checkOrder($firstName, $lastName, $number)
    {
        $param1 = !empty($firstName) ? $firstName . '/' : null;
        $param2 = !empty($lastName)  ? $lastName  . '/' : null;
        $param3 = !empty($number)    ? $number          : null;

        // Получить контент с сайта
        $client = new Client();
        $client->setUri('http://www.dosare.eu/search/' . $param1 . $param2 . $param3);
        $client->setOptions(array(
            'maxredirects' => 0
        ));
        $response = $client->send();

        // Разобрать узлы
        $html      = $response->getBody();
        $document  = new Document($html);
        $nodeList  = Query::execute('.searchContainer .table tr td, .searchContainer .table tr td a', $document, Query::TYPE_CSS);

        $resArray = array();

        $count = 0;
        foreach ($nodeList as $node) {
            $href      = trim($node->getAttribute('href'));
            $nodeValue = trim(str_replace('&nbsp;', '', htmlentities($node->nodeValue))); // убрать &nbsp;

            if (empty($nodeValue)) { continue; }

            if (!empty($href)) {
                $resArray[count($resArray) - 1][] = $href;

                $count++;
            } else {
                $resArray[$count][] = str_replace('Ordin', '', $nodeValue);
            }
        }

        return $resArray;
    }

    /**
     * @param $page
     * @return mixed
     */
    protected function getContent($page)
    {
        $content = $this->getEntityManager()
            ->getRepository(self::MAIN_CONTENT)
            ->findOneBy(array('id' => $page));

        return $content;
    }

    /**
     * @return mixed
     */
    protected function getAside()
    {
        $aside = $this->forward()->dispatch('Application\Controller\Index',
            array('action' => 'aside'));

        return $aside;
    }

    /**
     * @param $entity
     * @return void|\Zend\Form\ElementInterface|\Zend\Form\Form
     */
    protected function getForm($entity)
    {
        $builder = new AnnotationBuilder();
        $form    = $builder->createForm($entity);

        return $form;
    }


    /**
     * @return object
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
        }

        return $this->em;
    }
}