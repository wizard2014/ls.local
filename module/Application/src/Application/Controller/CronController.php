<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;
use Zend\Dom\Document;
use Zend\Dom\Document\Query;
use Zend\Http\Client;
use Zend\Stdlib\DateTime;

use Application\Entity\LinkToOrder;

class CronController extends AbstractActionController
{
    const LINK_TO_ORDER_ENTITY = 'Application\Entity\LinkToOrder';
    const MAIN_CONTENT_ENTITY  = 'Application\Entity\MainContent';

    /**
     * @var null|object
     */
    protected $em;
    private $cache;

    /**
     * @return string
     * @throws \RuntimeException
     */
    public function indexAction()
    {
        $request = $this->getRequest();

        if (!$request instanceof ConsoleRequest) {
            throw new \RuntimeException('You can only use this action from a console!');
        }

        // Получить контент с сайта
        $client = new Client();
        $client->setUri('http://cetatenie.just.ro/index.php/ro/ordine/articol-11/');
        $client->setOptions(array(
            'maxredirects' => 0
        ));
        $response = $client->send();

        // parse nodes
        $html     = $response->getBody();
        $document = new Document($html);
        $nodeList = Query::execute('.item-page ul li strong, .item-page ul li a', $document, Query::TYPE_CSS);

        $tmpArr  = array();
        $tmpDate = null;

        $count = 0;
        foreach ($nodeList as $node) {
            $href      = trim($node->getAttribute('href'));
            $nodeValue = trim(str_replace('&nbsp;', '', htmlentities($node->nodeValue))); // remove &nbsp;

            if (empty($nodeValue)) { continue; }

            if (empty($href)) {
                try {
                    $tmpArr[$count]['date']  = new \DateTime($nodeValue);
                } catch (\Exception $e) {
                    if (!is_null($tmpDate)) {
                        $nodeValue = $tmpDate . $nodeValue;
                        $tmpArr[$count]['date']  = new \DateTime($nodeValue);

                        $tmpDate = null;
                    } else {
                        $tmpDate = $nodeValue;
                    }
                }

                $this->cache->setItem('date', $nodeValue);
            } else {
                $tmpArr[$count]['link']  = $href;
                $tmpArr[$count]['title'] = $nodeValue;

                $count++;
            }

            $lastItemInArray = &$tmpArr[count($tmpArr) - 1];

            // add date, if not exists
            if (!isset($lastItemInArray['date'])) {
                $lastItemInArray['date'] = new \DateTime($this->cache->getItem('date'));
            }
        }

        $this->saveData(array_reverse($tmpArr));

        $this->cache->removeItem('doc');

        return 'done';
    }

    /**
     * @param $data
     */
    protected function saveData($data)
    {
        $currentOrders = $this->getCurrentOrder();

        foreach ($data as $item) {
            if (!isset($currentOrders[$item['link']])) {
                $order = new LinkToOrder();
                $order->populate($item);

                $this->getEntityManager()->persist($order);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @return array
     */
    protected function getCurrentOrder()
    {
        $ordersArr = array();

        $currentOrders = $this->getEntityManager()
            ->getRepository(self::LINK_TO_ORDER_ENTITY)->findAll();

        foreach ($currentOrders as $order) {
            $ordersArr[$order->getLink()] = true;
        }

        return $ordersArr;
    }

    /**
     * @param $cache
     */
    public function setCache($cache)
    {
        $this->cache = $cache;
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