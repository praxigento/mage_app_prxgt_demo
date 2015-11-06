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
class Praxigento_Quickorder_Model_Mysql4_Suggestion extends Mage_Core_Model_Mysql4_Abstract
{

    protected $_query = null;
    protected $_filters = null;

    public function setQuery($value)
    {
        $this->_query = $value;
        return $this;
    }

    public function _construct()
    {
        $this->_init('prxgt_qof_model/suggestions', 'entity_id');
        /** get all available attributes and compose filter to be used in products lookup */
        $allAttrs       = array_keys(Mage::getModel('prxgt_qof_model/source_attributes')->toOptionArray());
        $searchAttrs    = Praxigento_Quickorder_Config::cfgSearchAttributes();
        $this->_filters = array_intersect($searchAttrs, $allAttrs);
    }

    /**
     * Collect products according to configuration parameters and query from the QOF.
     *
     * @return Mage_Catalog_Model_Resource_Product_Collection
     */
    public function getProductCollection()
    {
        /** @var $collection Mage_Catalog_Model_Resource_Product_Collection */
        $collection = Mage::getModel('catalog/product')->getResourceCollection()
            ->addAttributeToSelect('*')
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents();

        /** Setup filter by product visibility */
        if (!Praxigento_Quickorder_Config::cfgInvisibleIncluded()) {
            /** @var $prodVisible Mage_Catalog_Model_Product_Visibility */
            $prodVisible = Mage::getSingleton('catalog/product_visibility');
            $collection->setVisibility($prodVisible->getVisibleInSiteIds());
        }
        /** Setup filter by product type */
        /** complex prods need a Ajax panel to configire options pn-the-fly */
//        if (!Praxigento_Quickorder_Config::cfgComplexProdsIncluded()) {
        /** exclude configurable, grouped, bundled products and any custom types */
        $collection->addAttributeToFilter('type_id', array('in', array('simple', 'virtual', 'downloadable')));
//        }

        /** filter products by customer's query */
        $conditions = array();
        foreach ($this->_filters as $filter) {
            $conditions[] = array(
                'attribute' => $filter,
                'like'      => "%{$this->_query}%"
            );
        }
        $collection->addAttributeToFilter($conditions);

        /** sort result set */
        $attr  = Praxigento_Quickorder_Config::cfgSortAttr();
        $order = Praxigento_Quickorder_Config::cfgSortOrder();
        $collection->addAttributeToSort($attr, $order);

        /** remove out of stock items in case of Magento is configured to show items but QOF - to hide */
        if (Mage::helper('cataloginventory')->isShowOutOfStock() && !Praxigento_Quickorder_Config::cfgOutOfStockEnabled()) {
            /** @var $item Mage_Catalog_Model_Product */
            foreach ($collection as $key => $item) {
                $inventory = $item->getStockItem();
                if (!$inventory->getData('is_in_stock')) {
                    $collection->removeItemByKey($key);
                }
            }
        }

        return $collection;
    }

    public function getIdsBySkus($skus)
    {
        $skus[] = '';
        $select = new Zend_Db_Select($this->_getReadAdapter());
        $select->from('catalog_product_entity', array('entity_id', 'sku'));
        $select->where("sku in (?)", $skus);

        $data = $this->_getReadAdapter()->fetchAll($select);
        return $data;
    }

    /**
     * Validate SKUs from Quick Order Form before adding to cart.
     * @param $skus
     * @return array
     */
    public function filterSkus($skus)
    {
        $skus[] = '';
        $select = new Zend_Db_Select($this->_getReadAdapter());
        $select->from('catalog_product_entity', 'sku');
        $select->where("sku in (?)", $skus);
        //
        $data = $this->_getReadAdapter()->fetchCol($select);
        return $data;
    }
}
