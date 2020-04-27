<?php

/**
 * Class SamedayCourier_Shipping_Model_Carrier_Sameday
 *
 */
class SamedayCourier_Shipping_Model_Carrier_Sameday extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'samedaycourier_shipping';

    public function checkAvailableShipCountries(Mage_Shipping_Model_Rate_Request $request)
    {
        return $request->getDestCountryId() === 'RO';
    }

    /**
     * @return array
     *
     */
    public function getAllowedMethods()
    {
        return array(
            'samedaycourier_shipping'=> $this->getConfigData('title'),
        );
    }

    /**
     * @return bool
     */
    public function isCityRequired()
    {
        return true;
    }

    /**
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return bool|false|Mage_Core_Model_Abstract|Mage_Shipping_Model_Rate_Result|null
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if ($request->getDestCountryId() !== 'RO') {
            return null;
        }

        $result = Mage::getModel('shipping/rate_result');

        $availableServices = $this->getAvailableServices();

        $useEstimatedCost = (int) $this->getConfigData('estimated_cost');

        $estimatedCostExtraFee = $this->getConfigData('estimated_cost_extra_fee');

        if (!empty($availableServices)) {
            foreach ($availableServices as $service) {
                $quote = Mage::getSingleton('checkout/session')->getQuote();

                $shippingAddress = $quote->getShippingAddress();
                $city = $shippingAddress->getCity();

                $county = $shippingAddress->getRegion();
                $address = ltrim(implode(" ", $shippingAddress->getStreet()));
                $weight = $shippingAddress->getWeight() < 1 ? 1 : $shippingAddress->getWeight();
                $subtotal = round($quote->getTotals()['subtotal']['value'], 2);

                if ($service['sameday_code'] === 'LS') {
                    continue;
                }

                if ($service['sameday_code'] === '2H' && $city !== 'Bucuresti') {
                    continue;
                }

                if ($service['sameday_code'] === 'LN' && count($quote->getAllItems()) > 1) {
                    continue;
                }

                if ($useEstimatedCost) {
                    $estimatedCost = $this->estimateCost($service['sameday_id'], $weight, $city, $county, $address, $subtotal);
                    if ($estimatedCost !== null) {

                        if (($useEstimatedCost === 1) || ($useEstimatedCost === 2 && $service['price'] < $estimatedCost)) {
                            $service['price'] = $estimatedCost;
                        }

                        if (isset($estimatedCostExtraFee) && $estimatedCostExtraFee > 0) {
                            $service['price'] += round( $service['price'] * ($estimatedCostExtraFee / 100), 2);
                        }
                    }
                }

                if ($service['price_free'] !== '' && $subtotal >= $service['price_free']) {
                    $service['price'] = 0;
                }

                if ($service['sameday_code'] === "LN") {
                    $this->syncLockers();
                }

                $result->append($this->addRate($service));
            }
        }

        return $result;
    }

    /**
     * @return void
     *
     */
    private function syncLockers()
    {
        $time = time();
        $ltSynced = $this->getConfigData('lockers_lt_synced');

        if ($time > ($ltSynced + 86401)) {
            $this->refreshLockers();
        }
    }

    /**
     * @return bool
     */
    private function refreshLockers()
    {
        $sameday = Mage::helper('samedaycourier_shipping/api')->sameday;

        $testing = Mage::getStoreConfig('carriers/samedaycourier_shipping/is_testing', $this->store);

        $remoteLockers = [];
        $request = new Sameday\Requests\SamedayGetLockersRequest();

        try {
            $lockers = $sameday->getLockers($request);
        } catch (\Exception $e) {
            return false;
        }

        $lockerModel = Mage::getModel('samedaycourier_shipping/locker');
        foreach ($lockers->getLockers() as $lockerObject) {
            $locker = $lockerModel->getLockerSameday($lockerObject->getId(), $testing)[0];
            if (!$locker) {
                // Lockers not found, add it.
                $lockerModel->addLocker($lockerObject, $testing);
            } else {
                // Lockers already imported, update it.
                $lockerModel->updateLocker($locker['id'], $lockerObject, $testing);
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

            $lockerModel->getLockers($testing)
        );

        // Delete local lockers that aren't present in remote lockers anymore.
        foreach ($localLockers as $localLocker) {
            if (!in_array($localLocker['locker_id'], $remoteLockers)) {
                $lockerModel->deleteLocker($localLocker['id']);
            }
        }

        $this->updateLastTimeSync();

        return true;
    }

    /**
     * @return void
     */
    private function updateLastTimeSync()
    {
        $time = time();
        Mage::getConfig()->saveConfig('carriers/samedaycourier_shipping/lockers_lt_synced', $time, 'default', 0);
    }

    /**
     * @param bool $testing
     *
     * @return array
     */
    private function getAvailableServices()
    {
        $isTesting = $this->getConfigData('is_testing');

        $services = Mage::getModel('samedaycourier_shipping/service')
            ->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('is_testing', $isTesting)
            ->addFieldToFilter('status', array('gt' => 0))
            ->getData();

        $availableServices = array();
        foreach ($services as $service) {
            switch ($service['status']) {
                case 1:
                    $availableServices[] = $service;
                    break;

                case 2:
                    $working_days = json_decode($service['working_days'], '');
                    $day = strtolower(date('l'));

                    $todayFrom = $working_days["working_days_{$day}_from"];
                    $todayUntil = $working_days["working_days_{$day}_until"];

                    if ($todayFrom === '1' && $todayUntil === '1') {
                        // The service is inactive on this day
                        break;
                    }

                    $date_from = mktime($todayFrom[0], $todayFrom[1], $todayFrom[2], date('m'), date('d'), date('Y'));
                    $date_until = mktime($todayUntil[0], $todayUntil[1], $todayUntil[2], date('m'), date('d'), date('Y'));

                    $time = time();

                    if ($time < $date_from || $time > $date_until) {
                        // The service is out of interval
                        break;
                    }

                    $availableServices[] = $service;
                    break;
            }
        }

        return $availableServices;
    }

    /**
     * @param $service
     *
     * @return false|Mage_Core_Model_Abstract
     */
    private function addRate($service)
    {
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod($service['sameday_id'] . "_" . $service['sameday_code']);
        $rate->setMethodTitle($service['name']);
        $rate->setPrice($service['price']);
        $rate->setMethodDescription($service['sameday_code']);
        $rate->setCost($service['price']);

        return $rate;
    }

    /**
     * @param $serviceId
     *
     * @param $weight
     *
     * @param $city
     *
     * @param $county
     *
     * @param $address
     *
     * @param $subtotal
     *
     * @return float|null
     */
    private function estimateCost($serviceId, $weight, $city, $county, $address, $subtotal)
    {
        $sameday = Mage::helper('samedaycourier_shipping/api')->sameday;

        $pickupPointId = Mage::getModel('samedaycourier_shipping/pickuppoint')->getDefaultPickupPoint();

        $estimateCostRequest = new Sameday\Requests\SamedayPostAwbEstimationRequest(
            $pickupPointId,
            null,
            new Sameday\Objects\Types\PackageType(
                0
            ),
            [new \Sameday\Objects\ParcelDimensionsObject($weight)],
            $serviceId,
            new Sameday\Objects\Types\AwbPaymentType(
                1
            ),
            new Sameday\Objects\PostAwb\Request\AwbRecipientEntityObject(
                ucwords($city) !== 'Bucuresti' ? $city : 'Sector 1',
                $county,
                $address !== '' ? $address : '123',
                null,
                null,
                null,
                null
            ),
            0,
            $subtotal,
            null,
            array()
        );

        try {
            $estimation = $sameday->postAwbEstimation($estimateCostRequest);
            return $estimation->getCost();
        } catch (\Sameday\Exceptions\SamedayBadRequestException $exception) {
            return null;
        }
    }
}
