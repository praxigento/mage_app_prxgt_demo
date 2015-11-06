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
/**
 * Product attributes names to sort suggestion results before display.
 */
class Praxigento_Quickorder_Model_Source_SortBy
{
    /** @var array|null cache for sortable attributes */
    protected $_sortByAttrs = null;

    public function toOptionArray()
    {
        if (!is_null($this->_sortByAttrs)) {
            return $this->_sortByAttrs;
        }

        $entityType = Mage::getSingleton('eav/config')->getEntityType('catalog_product');

        $productAttributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToSelect(array('frontend_label', 'attribute_code'))
            ->setEntityTypeFilter($entityType->getEntityTypeId())
            ->addSearchableAttributeFilter()
            ->addFieldToFilter('attribute_code', array('in' => array('sku', 'price', 'name')));

        $attributes = $productAttributeCollection->getConnection()->fetchAll($productAttributeCollection->getSelect());

        /** compose and sort result set */
        foreach ($attributes as $attribute) {
            $this->_sortByAttrs[$attribute['attribute_code']] = array(
                'label' => $attribute['frontend_label'],
                'value' => $attribute['attribute_code']
            );
        }
        asort($this->_sortByAttrs);
        return $this->_sortByAttrs;
    }

}

