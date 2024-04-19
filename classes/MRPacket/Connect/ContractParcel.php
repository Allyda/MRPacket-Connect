<?php

namespace MRPacket\Connect;

/**
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class ArrayCourierProducts extends \ArrayObject
{
	public function offsetSet($index, $newval)
	{
		if (!$newval instanceof ContractCourierProduct) {
			throw new \InvalidArgumentException('Invalid type');
		}

		parent::offsetSet($index, $newval);
	}
}

/**
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class ContractParcel
{
	/** @todo */
	public $receiver = array(
		'first_name'	=> '',
		'last_name'		=> '',
		'company'		=> '',

		'phone' 		=> '',
		'email'			=> '',

		'street'		=> '',
		'house_number'	=> '',
		'postal_cod'	=> '',
		'city'			=> '',
		'country'		=> '',
	);

	public $order = array(
		'id'		=> null,
		'reference' => null,
		'tax_value'	=> 0,
		'net_value'	=> 0
	);

	public $package = array(
		'length'	=> null,
		'width'		=> null,
		'height'	=> null,
		'weight'	=> null
	);

	public $courier_contract_products;

	public function __construct()
	{
		$this->courier_contract_products = new ArrayCourierProducts(array(), \ArrayObject::STD_PROP_LIST);
	}
}
