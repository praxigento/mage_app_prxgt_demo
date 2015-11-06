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
 * User: Alex Gusev <flancer64@gmail.com>
 */
class Praxigento_Quickorder_Config
{
    /******************************************************************************************
     *                           POST/GET request parameters
     *****************************************************************************************/
    const REQ_PARAM_QUERY         = 'prxgtQofQuery';
    const REQ_PARAM_ATTRS_ALLOWED = 'prxgtQofAttrsAllowed';
    const REQ_PARAM_ATTRS_SKU     = 'prxgtQofSku';
    const REQ_PARAM_ATTRS_QTY     = 'prxgtQofQty';

    /******************************************************************************************
     *                            Routing (/[ctrl]/[action])
     *****************************************************************************************/
    const ROUTE_SUGGESTION   = 'prxgt_qof_front/quickorder/suggestion';
    const ROUTE_ORDER_SUBMIT = 'prxgt_qof_front/quickorder/orderSubmit';
    const ROUTE_SKU_VALIDATE = 'prxgt_qof_front/quickorder/validateSku';

    /******************************************************************************************
     *                                   UI components
     *****************************************************************************************/
    /** used in qof_frontend_layout.xml */
    const UI_BLOCK_SUGGESTION = 'prxgt_qof_suggestion';
    const UI_TMPL_SUGGESTION  = 'prxgt/qof/suggestion.phtml';


    /**
     * 'true' - anonymous access is enabled, 'false' - otherwise.
     * @return bool
     */
    public static function cfgAnonymousEnabled()
    {
        return filter_var(Mage::getStoreConfig('prxgt_qof/general/anonymous_enabled'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Return ids of the customer groups disallowed to use QOF widget.
     * @param null $store
     * @return array
     */
    public static function cfgCustomerGroupsAllowed($store = null)
    {
        $entries = Mage::getStoreConfig('prxgt_qof/general/groups_allowed', $store);
        $result  = explode(',', $entries);
        if ((count($result) == 1) && ($result[0] == '')) {
            /** setup default value in case of there is no data in config */
            $result = array();
        }
        return $result;
    }

    /**
     * Return codes for the attributes to be used in products suggestion.
     * @param null $store
     * @return array
     */
    public static function cfgSearchAttributes($store = null)
    {
        $entries = Mage::getStoreConfig('prxgt_qof/general/search_attributes', $store);
        $result  = explode(',', $entries);
        if ((count($result) == 1) && ($result[0] == '')) {
            /** setup default value in case of there is no data in config */
            $result = array('sku');
        }
        return $result;
    }

    /**
     * 'true' - include individualy invivsible products to suggestion.
     * @return bool
     */
    public static function cfgInvisibleIncluded()
    {
        return filter_var(Mage::getStoreConfig('prxgt_qof/general/invisible_included'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * 'true' - include complex products to suggestion (configurable & bundled).
     * @return bool
     */
    public static function cfgComplexProdsIncluded()
    {
        return filter_var(Mage::getStoreConfig('prxgt_qof/general/complex_included'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * 'true' - anonymous access is enabled, 'false' - otherwise.
     * @return bool
     */
    public static function cfgOutOfStockEnabled()
    {
        return filter_var(Mage::getStoreConfig('prxgt_qof/general/out_of_stock_enabled'), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Return name of the attribute to sort suggestion result to display. Default: 'name'.
     * @param null $store
     * @return string
     */
    public static function cfgSortAttr($store = null)
    {
        $val = Mage::getStoreConfig('prxgt_qof/general/sort_attr', $store);
        return ($val) ? $val : 'name';
    }

    /**
     * Return sort order for suggestion result to display. Default: 'ASC'.
     * @param null $store
     * @return mixed|string
     */
    public static function cfgSortOrder($store = null)
    {
        $val = Mage::getStoreConfig('prxgt_qof/general/sort_order', $store);
        return ($val) ? $val : Praxigento_Quickorder_Model_Source_SortOrder::ASC;
    }


    /**
     * @return Praxigento_Quickorder_Model_Mysql4_Suggestion
     */
    public static function getResourceSuggestion()
    {
        return Mage::getResourceModel('prxgt_qof_model/suggestion');
    }

    /**
     * Returns default helper for the module.
     * @return Praxigento_Quickorder_Helper_Data
     */
    public static function helper()
    {
        return Mage::helper('prxgt_qof_helper');
    }
}
