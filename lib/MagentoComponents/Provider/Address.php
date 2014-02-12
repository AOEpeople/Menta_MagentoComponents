<?php
/**
 * AddressProvider
 *
 * fill address/form fields with specified address
 */

class MagentoComponents_Provider_Address {

	/**
	 * @param string $type
	 * @param string $country
	 * @return array
	 * @throws Exception
	 */
	public function getAddressField($type, $country) {
		if ($type == 'billing') {
			$address = $this->getAddress($country, 'Billing');
		} elseif ($type == 'shipping') {
			$address = $this->getAddress($country, 'Shipping');
		} else {
			throw new Exception('Unknown address type');
		}
		return $address;
	}

	/**
	 * Get address
	 *
	 * @param string $country
	 * @param string $prefix
	 * @return array
	 * @throws Exception
	 */
	protected function getAddress($country, $prefix='') {
		$address = array(
			'firstname' => $prefix.'Firstname',
			'lastname' => $prefix.'Lastname',
			'street1' => $prefix.'Street 1',
			'street2' => $prefix.'Street 2',
			'phone' => '0123 456789',
			'company' => $prefix.'Company',
		);
		switch ($country) {
			case 'de': $tmp = array('city' => 'Wiesbaden', 'postcode' => '65205', 'country' => 'Germany'); break;
			case 'us': $tmp = array('city' => 'Montgomery',	'region' => 'Alabama', 'postcode' => '85621', 'country' => 'United States'); break;
			case 'us_california': $tmp = array('city' => 'San Francisco', 'region' => 'California', 'postcode' => '85621', 'country' => 'United States'); break;
			case 'it': $tmp = array('city' => 'Rome', 'postcode' => '85621', 'country' => 'Italy'); break;
			case 'es': $tmp = array('city' => 'Barcelona', 'postcode' => '85621', 'country' => 'Spain'); break;
			case 'fi': $tmp = array('city' => 'Helsinki', 'postcode' => '85621', 'country' => 'Finland'); break;
			case 'eg': $tmp = array('city' => 'Kairo', 'postcode' => '85621', 'country' => 'Egypt'); break;
			case 'uk': $tmp = array('city' => 'London', 'postcode' => '85621', 'country' => 'United Kingdom'); break;
			default: throw new Exception('No valid country set');
		}
		return array_merge($address, $tmp);
	}

}