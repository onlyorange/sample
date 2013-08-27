<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shoprevolution_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
	protected function _prepareForm()
	{

		$model = Mage::registry('shopslideshow_shoprevolution');

		$form = new Varien_Data_Form();
		$this->setForm($form);
		$fieldset = $form->addFieldset('shopslideshow_form', array('legend' => Mage::helper('shopslideshow')->__('Revolution Slide information')));

		$fieldset->addField('store_id', 'multiselect', array(
			'name' => 'stores[]',
			'label' => Mage::helper('shopslideshow')->__('Store View'),
			'title' => Mage::helper('shopslideshow')->__('Store View'),
			'required' => true,
			'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		));

		$fieldset->addField('transition', 'select', array(
			'label' => Mage::helper('shopslideshow')->__('Transition'),
			'name' => 'transition',
			'values' => array(
				array(
					'value' => 'boxslide',
					'label' => Mage::helper('shopslideshow')->__('boxslide'),
				),
				array(
					'value' => 'boxfade',
					'label' => Mage::helper('shopslideshow')->__('boxfade'),
				),
				array(
					'value' => 'slotzoom-horizontal',
					'label' => Mage::helper('shopslideshow')->__('slotzoom-horizontal'),
				),
				array(
					'value' => 'slotslide-horizontal',
					'label' => Mage::helper('shopslideshow')->__('slotslide-horizontal'),
				),
				array(
					'value' => 'slotfade-horizontal',
					'label' => Mage::helper('shopslideshow')->__('slotfade-horizontal'),
				),
				array(
					'value' => 'slotzoom-vertical',
					'label' => Mage::helper('shopslideshow')->__('slotzoom-vertical'),
				),
				array(
					'value' => 'slotslide-vertical',
					'label' => Mage::helper('shopslideshow')->__('slotslide-vertical'),
				),
				array(
					'value' => 'slotfade-vertical',
					'label' => Mage::helper('shopslideshow')->__('slotfade-vertical'),
				),
				array(
					'value' => 'curtain-1',
					'label' => Mage::helper('shopslideshow')->__('curtain-1'),
				),
				array(
					'value' => 'curtain-2',
					'label' => Mage::helper('shopslideshow')->__('curtain-2'),
				),
				array(
					'value' => 'curtain-3',
					'label' => Mage::helper('shopslideshow')->__('curtain-3'),
				),
				array(
					'value' => 'slideleft',
					'label' => Mage::helper('shopslideshow')->__('slideleft'),
				),
				array(
					'value' => 'slideright',
					'label' => Mage::helper('shopslideshow')->__('slideright'),
				),
				array(
					'value' => 'slideup',
					'label' => Mage::helper('shopslideshow')->__('slideup'),
				),
				array(
					'value' => 'slidedown',
					'label' => Mage::helper('shopslideshow')->__('slidedown'),
				),
				array(
					'value' => 'fade',
					'label' => Mage::helper('shopslideshow')->__('fade'),
				),
				array(
					'value' => 'random',
					'label' => Mage::helper('shopslideshow')->__('random'),
				),
				array(
					'value' => 'slidehorizontal',
					'label' => Mage::helper('shopslideshow')->__('slidehorizontal'),
				),
				array(
					'value' => 'slidevertical',
					'label' => Mage::helper('shopslideshow')->__('slidevertical'),
				),
				array(
					'value' => 'papercut',
					'label' => Mage::helper('shopslideshow')->__('papercut'),
				),
				array(
					'value' => 'flyin',
					'label' => Mage::helper('shopslideshow')->__('flyin'),
				),
				array(
					'value' => 'turnoff',
					'label' => Mage::helper('shopslideshow')->__('turnoff'),
				),
				array(
					'value' => 'cube',
					'label' => Mage::helper('shopslideshow')->__('cube'),
				),
				array(
					'value' => '3dcurtain-vertical',
					'label' => Mage::helper('shopslideshow')->__('3dcurtain-vertical'),
				),
				array(
					'value' => '3dcurtain-horizontal',
					'label' => Mage::helper('shopslideshow')->__('3dcurtain-horizontal'),
				),
			),
			'note' => 'The appearance transition of this slide',
		));

		$fieldset->addField('masterspeed', 'text', array(
			'label' => Mage::helper('shopslideshow')->__('Masterspeed'),
			'required' => false,
			'name' => 'masterspeed',
			'note' => 'Set the Speed of the Slide Transition. Default 300, min:100 max:2000.'
		));
		$fieldset->addField('slotamount', 'text', array(
			'label' => Mage::helper('shopslideshow')->__('Slotamount'),
			'required' => false,
			'name' => 'slotamount',
			'note' => 'The number of slots or boxes the slide is divided into. If you use boxfade, over 7 slots can be juggy.'
		));
		$fieldset->addField('link', 'text', array(
			'label' => Mage::helper('shopslideshow')->__('Slide Link'),
			'required' => false,
			'name' => 'link',
		));

		$data = array();
		$out = '';
		if (Mage::getSingleton('adminhtml/session')->getshoprevolutionData()) {
			$data = Mage::getSingleton('adminhtml/session')->getshoprevolutionData();
		} elseif (Mage::registry('shoprevolution_data')) {
			$data = Mage::registry('shoprevolution_data')->getData();
		}

		if (!empty($data['image'])) {
			$url = Mage::getBaseUrl('media') . $data['image'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('image', 'file', array(
			'label' => Mage::helper('shopslideshow')->__('Image'),
			'required' => false,
			'name' => 'image',
			'note' => $out,
		));

		$out = '';
		if (!empty($data['thumb'])) {
			$url = Mage::getBaseUrl('media') . $data['thumb'];
			$out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
			$out .= "<img src=" . $url . " width='150px' />";
			$out .= '</a></center>';
		}

		$fieldset->addField('thumb', 'file', array(
			'label' => Mage::helper('shopslideshow')->__('Slide thumb'),
			'required' => false,
			'name' => 'thumb',
			'note' => 'An Alternative Source for thumbs. If not defined a copy of the background image will be used in resized form. ' . $out,
		));

		$fieldset->addField('text', 'textarea', array(
			'label'     => Mage::helper('shopslideshow')->__('Slide Content'),
			'required'  => false,
			'name'      => 'text',
		));

		$fieldset->addField('status', 'select', array(
			'label' => Mage::helper('shopslideshow')->__('Status'),
			'name' => 'status',
			'values' => array(
				array(
					'value' => 1,
					'label' => Mage::helper('shopslideshow')->__('Enabled'),
				),
				array(
					'value' => 2,
					'label' => Mage::helper('shopslideshow')->__('Disabled'),
				),
			),
		));

		$fieldset->addField('sort_order', 'text', array(
			'label' => Mage::helper('shopslideshow')->__('Sort Order'),
			'required' => false,
			'name' => 'sort_order',
		));

		if (Mage::getSingleton('adminhtml/session')->getshoprevolutionData()) {
			$form->setValues(Mage::getSingleton('adminhtml/session')->getshoprevolutionData());
			Mage::getSingleton('adminhtml/session')->getshoprevolutionData(null);
		} elseif (Mage::registry('shoprevolution_data')) {
			$form->setValues(Mage::registry('shoprevolution_data')->getData());
		}
		return parent::_prepareForm();
	}
}