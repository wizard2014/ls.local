<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;

class EditController extends AbstractActionController
{
    const MAIN_CONTENT = 'Application\Entity\MainContent';

    protected $em;
    private $currentUser;

    public function __construct()
    {
        $auth = new AuthenticationService();
        $this->currentUser = $auth->getIdentity();
    }

    public function indexAction()
    {
        $content = $this->getEntityManager()
            ->getRepository(self::MAIN_CONTENT)
            ->findAll();

        $this->layout('layout/admin');
        return new ViewModel(array(
            'content' => $content,
        ));
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('edit');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $user = $this->getEntityManager()
                ->getRepository('User\Entity\User')
                ->findOneBy(array('id' => $this->currentUser));

            $postData = $request->getPost();

            $qb = $this->getEntityManager()->createQueryBuilder();

            $qu = $qb->update(self::MAIN_CONTENT, 'c')
                ->set('c.idUser', '?1')
                ->set('c.title', '?2')
                ->set('c.text', '?3')
                ->where('c.id = ?4')
                ->setParameter(1, $user)
                ->setParameter(2, $postData->title)
                ->setParameter(3, htmlentities($postData->editor))
                ->setParameter(4, $postData->id)
                ->getQuery();
            $qu->execute();

            return $this->redirect()->toRoute('edit');
        }

        $content = $this->getEntityManager()
            ->getRepository(self::MAIN_CONTENT)
            ->findOneBy(array('id' => $id));

        $this->layout('layout/admin');
        return new ViewModel(array(
            'id'    => $content->getId(),
            'title' => $content->getTitle(),
            'text'  => $content->getText()
        ));
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