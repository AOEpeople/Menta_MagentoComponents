<?php
/**
 * Components for images
 * @author Thomas Layh <thomas.layh@aoemedia.de>
 */
class Components_Image extends Menta_Component_AbstractTest {

	/**
	 * @var WebDriver_Element
	 */
	protected $image;

	/**
	 * @param WebDriver_Element $element
	 * @param int $width
	 * @param int $height
	 */
	public function checkImageSize($width, $height) {

		if (is_null($this->image)) {
			throw new Exception('No image set');
		}

		$size = $this->image->size();
		$this->getTest()->assertEquals($width, $size['width'], 'Image width is not correct');
		$this->getTest()->assertEquals($height, $size['height'], 'Image height is not correct');
		return $this;
	}

	/**
	 * @param \WebDriver_Element $image
	 */
	public function setImage(WebDriver_Element $image) {
		$this->image = $image;
		return $this;
	}


}