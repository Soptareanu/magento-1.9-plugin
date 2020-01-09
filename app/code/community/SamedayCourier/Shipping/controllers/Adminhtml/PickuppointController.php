<?php

class SamedayCourier_Shipping_Adminhtml_PickuppointController extends Mage_Adminhtml_Controller_Action
{
    private $store;
    private $pickupPointModel;

    /**
     * SamedayCourier_Shipping_Adminhtml_PickuppointController constructor.
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param array $invokeArgs
     * @throws Mage_Core_Model_Store_Exception
     */
    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);
        $this->store = Mage::app()->getStore();
        $this->pickupPointModel = Mage::getModel('samedaycourier_shipping/pickuppoint');
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('samedaycourier_shipping/pickuppoint');
    }

    /**
     * @return object $this
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('samedayMenu');

        return $this;
    }

    /**
     * Show Pickup points
     */
    public function indexAction()
    {
        $pickupPointIndexBlock = $this->getLayout()->createBlock(
            'samedaycourier_shipping/adminhtml_pickuppoint'
        );
        $this->_initAction();
        $this->_addContent($pickupPointIndexBlock);
        $this->_title($this->__('Pickup Points'));

        $this->renderLayout();
    }

    /**
     * @throws Mage_Core_Model_Store_Exception
     * @throws \Sameday\Exceptions\SamedayAuthorizationException
     * @throws \Sameday\Exceptions\SamedaySDKException
     * @throws \Sameday\Exceptions\SamedayServerException
     */
    public function refreshAction()
    {
        $sameday = Mage::helper('samedaycourier_shipping/api')->sameday;

        $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', $this->store);

        $remotePickupPoints = [];
        $page = 1;
        do {
            $request = new Sameday\Requests\SamedayGetPickupPointsRequest();
            $request->setPage($page++);
            try {
                $pickUpPoints = $sameday->getPickupPoints($request);
            } catch (\Exception $e) {
                return $this->_redirect("adminhtml/pickuppoint/index");
            }

            foreach ($pickUpPoints->getPickupPoints() as $pickupPointObject) {
                $pickupPoint = $this->pickupPointModel->getPickupPointSameday($pickupPointObject->getId(), $testing)[0];
                if (!$pickupPoint) {
                     // Pickup point not found, add it.
                    $this->pickupPointModel->addPickupPoint($pickupPointObject, $testing);
                } else {
                    $this->pickupPointModel->updatePickupPoint($pickupPoint['id'], $pickupPointObject, $testing);
                }

                // Save as current pickup points.
                $remotePickupPoints[] = $pickupPointObject->getId();
            }
        } while ($page <= $pickUpPoints->getPages());

        // Build array of local pickup points.
        $localPickupPoints = array_map(
            function ($pickupPoint) {
                return array(
                    'id' => $pickupPoint['id'],
                    'sameday_id' => $pickupPoint['sameday_id']
                );
            },

            $this->pickupPointModel->getPickupPoints($testing)
        );

        // Delete local pickup points that aren't present in remote pickup points anymore.
        foreach ($localPickupPoints as $localPickupPoint) {
            if (!in_array($localPickupPoint['sameday_id'], $remotePickupPoints)) {
                $this->pickupPointModel->deletePickupPoint($localPickupPoint['id']);
            }
        }

        $this->_redirect("adminhtml/pickuppoint/index");
    }
}
