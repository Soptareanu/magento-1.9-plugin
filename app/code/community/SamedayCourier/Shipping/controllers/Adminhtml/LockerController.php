<?php

class SamedayCourier_Shipping_Adminhtml_LockerController extends Mage_Adminhtml_Controller_Action
{
    private $lockerModel;

    public function __construct(Zend_Controller_Request_Abstract $request, Zend_Controller_Response_Abstract $response, array $invokeArgs = array())
    {
        parent::__construct($request, $response, $invokeArgs);

        $this->lockerModel = Mage::getModel('samedaycourier_shipping/locker');
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('samedaycourier_shipping/locker');
    }

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('samedayMenu');

        return $this;
    }

    /**
     * Show Lockers
     */
    public function indexAction()
    {
        $lockerIndexBlock = $this->getLayout()->createBlock(
            'samedaycourier_shipping/adminhtml_locker'
        );
        $this->_initAction();
        $this->_addContent($lockerIndexBlock);
        $this->_title($this->__('Lockers'));

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

        $remoteLockers = [];
        $request = new Sameday\Requests\SamedayGetLockersRequest();

        try {
            $lockers = $sameday->getLockers($request);
        } catch (\Exception $e) {
           return $this->_redirect("adminhtml/locker/index");
        }

        foreach ($lockers->getLockers() as $lockerObject) {
            $locker = $this->lockerModel->getLockerSameday($lockerObject->getId(), $testing)[0];
            if (!$locker) {
                // Lockers not found, add it.
                $this->lockerModel->addLocker($lockerObject, $testing);
            } else {
                // Lockers already imported, update it.
                $this->lockerModel->updateLocker($locker['id'], $lockerObject, $testing);
            }

            // Save as current lockers.
            $remoteLockers[] = $lockerObject->getId();
        }


        // Build array of local lockers.
        $localLockers = array_map(
            function ($locker) {
                return array(
                    'id' => $locker['id'],
                    'locker_id' => $locker['locker_id']
                );
            },

            $this->lockerModel->getLockers($testing)
        );

        // Delete local lockers that aren't present in remote lockers anymore.
        foreach ($localLockers as $localLocker) {
            if (!in_array($localLocker['locker_id'], $remoteLockers)) {
                $this->lockerModel->deleteLocker($localLocker['id']);
            }
        }

        $this->updateLastTimeSync();

        $this->_redirect("adminhtml/locker/index");
    }

    /**
     * @return void
     */
    private function updateLastTimeSync()
    {
        $time = time();
        Mage::getConfig()->saveConfig('carriers/samedaycourier_shipping/lockers_lt_synced', $time, 'default', 0);
    }
}
