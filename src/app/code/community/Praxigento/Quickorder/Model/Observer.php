<?php
/**
 * Copyright (c) 2013, Praxigento
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and the following
 *      disclaimer.
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the
 *      following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
class Praxigento_Quickorder_Model_Observer extends Mage_Core_Model_Observer
{
    public function modifyBlockForm(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof Mage_Adminhtml_Block_Customer_Group_Edit_Form) {
            if (Mage::getSingleton('adminhtml/session')->getCustomerGroupData()) {
                $values = Mage::getSingleton('adminhtml/session')->getCustomerGroupData();
            } else {
                $values = Mage::registry('current_group')->getData();
            }
            $form     = $block->getForm();
            $fieldset = $form->getElement('base_fieldset');
            $fieldset->addField('can_see_quick_order_form', 'select',
                array(
                    'name'     => 'can_see_quick_order_form',
                    'label'    => Praxigento_Quickorder_Config::helper()->__('Can see Quick Order Form'),
                    'title'    => Praxigento_Quickorder_Config::helper()->__('Can see Quick Order Form'),
                    'class'    => 'required-entry',
                    'required' => true,
                    'values'   => Mage::getSingleton('adminhtml/system_config_source_yesno')->toOptionArray(),
                    'value'    => $values['can_see_quick_order_form']
                ), "access"
            );
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return null
     */
    public function customerGroupSaveBefore(Varien_Event_Observer$observer)
    {
        $group = $observer->getEvent()->getObject();
        $group->setCanSeeQuickOrderForm(Mage::app()->getRequest()->getParam('can_see_quick_order_form'));
    }
}
