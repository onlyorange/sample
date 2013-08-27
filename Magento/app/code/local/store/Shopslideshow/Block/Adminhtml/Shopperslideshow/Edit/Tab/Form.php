<?php
/**
 * @version   0.1.0
 * @author    http://devjuhong.com <zzeppon@hotmail.com>
 */

class store_shopslideshow_Block_Adminhtml_shopslideshow_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {

	  $model = Mage::registry('shopslideshow_shopslideshow');

      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('shopslideshow_form', array('legend'=>Mage::helper('shopslideshow')->__('Flexslider Slide information')));

		$fieldset->addField('store_id', 'multiselect', array(
		      'name'      => 'stores[]',
		      'label'     => Mage::helper('shopslideshow')->__('Store View'),
		      'title'     => Mage::helper('shopslideshow')->__('Store View'),
		      'required'  => true,
		      'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
		  ));

      $fieldset->addField('slide_align', 'select', array(
          'label'     => Mage::helper('shopslideshow')->__('Text Align'),
          'name'      => 'slide_align',
          'values'    => array(
              array(
                  'value'     => 'left',
                  'label'     => Mage::helper('shopslideshow')->__('Left'),
              ),
              array(
                  'value'     => 'right',
                  'label'     => Mage::helper('shopslideshow')->__('Right'),
              ),
              array(
                  'value'     => 'center',
                  'label'     => Mage::helper('shopslideshow')->__('Center'),
              ),
          ),
      ));

      $fieldset->addField('slide_title', 'text', array(
          'label'     => Mage::helper('shopslideshow')->__('Title'),
          'required'  => false,
          'name'      => 'slide_title',
      ));
      $fieldset->addField('slide_text', 'textarea', array(
          'label'     => Mage::helper('shopslideshow')->__('Text'),
          'required'  => false,
          'name'      => 'slide_text',
      ));
      $fieldset->addField('slide_button', 'text', array(
          'label'     => Mage::helper('shopslideshow')->__('Button Text'),
          'required'  => false,
          'name'      => 'slide_button',
      ));
      $fieldset->addField('slide_width', 'text', array(
          'label'     => Mage::helper('shopslideshow')->__('Content width'),
          'required'  => false,
          'name'      => 'slide_width',
      ));
	  
	  $fieldset->addField('slide_link', 'text', array(
          'label'     => Mage::helper('shopslideshow')->__('Link'),
          'required'  => false,
          'name'      => 'slide_link',
      ));


	  $data = array();
	  $out = '';
	  if ( Mage::getSingleton('adminhtml/session')->getshopslideshowData() )
		{
			$data = Mage::getSingleton('adminhtml/session')->getshopslideshowData();
		} elseif ( Mage::registry('shopslideshow_data') ) {
			$data = Mage::registry('shopslideshow_data')->getData();
		}

	  if ( !empty($data['image']) ) {
		  $url = Mage::getBaseUrl('media') . $data['image'];
          $out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
		  $out .= "<img src=" . $url . " width='150px' />";
		  $out .= '</a></center>';
	  }

      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('shopslideshow')->__('Image for PC'),
          'required'  => false,
          'name'      => 'image',
	      'note' => 'Image used for PC screens (larger than 768) '.$out,
	  ));

      $out = '';
      if ( !empty($data['small_image']) ) {
		  $url = Mage::getBaseUrl('media') . $data['small_image'];
          $out = '<br/><center><a href="' . $url . '" target="_blank" id="imageurl">';
		  $out .= "<img src=" . $url . " width='150px' />";
		  $out .= '</a></center>';
	  }

      $fieldset->addField('small_image', 'file', array(
          'label'     => Mage::helper('shopslideshow')->__('Small Image for iPhone'),
          'required'  => false,
          'name'      => 'small_image',
	      'note' => 'Small image used for small screens (less than 768) '.$out,
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('shopslideshow')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('shopslideshow')->__('Enabled'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('shopslideshow')->__('Disabled'),
              ),
          ),
      ));

      $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('shopslideshow')->__('Sort Order'),
            'required'  => false,
            'name'      => 'sort_order',
        ));

      if ( Mage::getSingleton('adminhtml/session')->getshopslideshowData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getshopslideshowData());
          Mage::getSingleton('adminhtml/session')->getshopslideshowData(null);
      } elseif ( Mage::registry('shopslideshow_data') ) {
          $form->setValues(Mage::registry('shopslideshow_data')->getData());
      }
      return parent::_prepareForm();
  }
}