<?php

class SamedayCourier_Shipping_Adminhtml_ServiceController extends Mage_Adminhtml_Controller_Action
{
    private $store;
    private $servicePointModel;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->store = Mage::app()->getStore();
        $this->servicePointModel = Mage::getModel('samedaycourier_shipping/service');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('samedaycourier_shipping/service');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('samedayMenu');

        return $this;
    }

    /**
     * Show service
     */
    public function indexAction()
    {
        // instantiate the grid container
        $serviceIndexBlock = $this->getLayout()->createBlock(
            'samedaycourier_shipping/adminhtml_service'
        );
        $this->_initAction();
        $this->_addContent($serviceIndexBlock);
        $this->_title($this->__('Services'));

        $this->renderLayout();
    }

    public function editAction()
    {
        $service = Mage::getModel('samedaycourier_shipping/service');
        if ($serviceId = $this->getRequest()->getParam('id', false)) {
            $service->load($serviceId);

            if ($service->getId() < 1) {
                $this->_getSession()->addError(
                    $this->__('This service no longer exists.')
                );
                return $this->_redirect(
                    'adminhtml/service/index'
                );
            }
        }

        if ($postData = $this->getRequest()->getPost('serviceData')) {

            $working_days = array();
            foreach ($postData as $key => $val) {
                if (strpos($key, '_days_') !== 0) {
                    $working_days[$key] = $val;
                }
            }

            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($days as $day) {
                if ($working_days["working_days_{$day}_from"] === '1') {
                    $working_days["working_days_{$day}_until"] = '1';
                }
            }

            if (!empty($working_days)) {
                $postData['working_days'] = json_encode($working_days);
            }

            try {
                $service->addData($postData);
                $service->save();

                $this->_getSession()->addSuccess(
                    $this->__('The service has been saved.')
                );

                // redirect to remove $_POST data from the request
                return $this->_redirect(
                    'adminhtml/service/index',
                    array('id' => $service->getId())
                );
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }
        }

        // make the current service object available to blocks
        Mage::register('current_service', $service);

        // instantiate the form container
        $serviceEditBlock = $this->getLayout()->createBlock(
            'samedaycourier_shipping/adminhtml_service_edit'
        );

        $this->loadLayout()
            ->_addContent($serviceEditBlock)
            ->renderLayout();
    }

    public function refreshAction()
    {
        $sameday = Mage::helper('samedaycourier_shipping/api')->sameday;

        $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', $this->store);

        $remoteServices = [];
        $page = 1;
        do {
            $request = new Sameday\Requests\SamedayGetServicesRequest();
            $request->setPage($page++);
            try {
                $services = $sameday->getServices($request);
            } catch (\Exception $e) {
                return $this->_redirect("adminhtml/service/index");
            }

            foreach ($services->getServices() as $serviceObjectObject) {
                $service = $this->servicePointModel->getServiceSameday($serviceObjectObject->getId(), $testing)[0];
                if (!$service) {
                    $this->servicePointModel->addService($serviceObjectObject, $testing);
                } else {
                    $this->servicePointModel->updateService($service['id'], $serviceObjectObject, $testing);
                }

                $remoteServices[] = $serviceObjectObject->getId();
            }
        } while ($page <= $services->getPages());

        $localServices = array_map(
            function ($service) {
                return array(
                    'id' => $service['id'],
                    'sameday_id' => $service['sameday_id']
                );
            },

            $this->servicePointModel->getServices($testing)
        );

        foreach ($localServices as $localService) {
            if (!in_array($localService['sameday_id'], $remoteServices)) {
                $this->servicePointModel->deleteService($localService['id']);
            }
        }

        $this->_redirect("adminhtml/service/index");
    }

    /**
     * @throws Exception
     */
    public function deleteAction()
    {
        $service = Mage::getModel('samedaycourier_shipping/service');
        $service->load(Mage::app()->getRequest()->getParam('id'));
        $service->delete();
        $this->_redirect("adminhtml/service/index");
    }
}
