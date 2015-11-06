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
class Praxigento_Quickorder_QuickorderController extends Mage_Core_Controller_Front_Action
{

    /**
     * Quick add items to order by SKU/Qty
     */
    public function orderSubmitAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();

        if (
            isset($params[Praxigento_Quickorder_Config::REQ_PARAM_ATTRS_SKU]) &&
            isset($params[Praxigento_Quickorder_Config::REQ_PARAM_ATTRS_QTY])
        ) {
            $skus = $params[Praxigento_Quickorder_Config::REQ_PARAM_ATTRS_SKU];
            $qtys = $params[Praxigento_Quickorder_Config::REQ_PARAM_ATTRS_QTY];

            foreach ($skus as $key => $value) {
                $value = trim($value);
                if ($value == '') {
                    unset($skus[$key]);
                    if (isset($qtys[$key])) {
                        unset($qtys[$key]);
                    }
                } else {
                    $skus[$key] = $value;
                }
            }

            if (empty($skus)) {
                $this->_getSession()->addNotice($this->__('No Item # were entered.'));
                $this->_goBack();
                return;
            }

            if ($products = Praxigento_Quickorder_Config::getResourceSuggestion()->getIdsBySkus($skus)) {
                $orderItems = array();
                foreach ($skus as $k => $v) {
                    $v = strtoupper($v);
                    if (isset($orderItems[$v])) {
                        $orderItems[$v] += $qtys[$k];
                    } else {
                        $orderItems[$v] = $qtys[$k];
                    }
                }

                $wrong_products = array();
                $right_products = array();
                foreach ($products as $val) {
                    try {
                        /** @var $product Mage_Catalog_Model_Product */
                        $product = Mage::getModel('catalog/product')
                            ->setStoreId(Mage::app()->getStore()->getId())
                            ->load((int)$val['entity_id']);
                        if ($product && $product->isInStock()) {
                            if (isset($orderItems[$val['sku']])) {
                                $cart->addProduct($product, $orderItems[$val['sku']]);
                            } else {
                                $cart->addProduct($product, $orderItems[$val['alias']]);
                            }

                            array_push($right_products, $val['sku']);
                        } else {
                            Mage::throwException(Mage::helper('checkout')->__("Product couldn't be added"));
                        }
                    } catch (Mage_Core_Exception $e) {
                        $wrong_products[$val['sku']] = $e->getMessage();
                    } catch (Exception $e) {
                        $wrong_products[$val['sku']] = $this->__('Internal error occurred');
                    }
                }
                foreach ($cart->getQuote()->getItemsCollection()->getItems() as $key => $item) {
                    if ($item->getHasError()) {
                        $cart->getQuote()->getItemsCollection()->removeItemByKey($key);
                    }
                }
                $this->_getSession()->setCartWasUpdated(true);
                $rightProductsCount = count($right_products);
                if ($rightProductsCount > 0) {
                    $message = $this->__('%s product(s) successfully added to your shopping cart.', $rightProductsCount);
                    $this->_getSession()->addSuccess($message);
                }
                if (count($wrong_products) > 0) {
                    foreach ($wrong_products as $sku => $error) {
                        $message = $this->__('Product #%s couldn\'t be added to your shopping cart because of error: %s', $sku, $error);
                        $this->_getSession()->addError($message);
                    }
                }
            } else {
                $message = $this->__('Exact match for item #%s not found. Please try searching for this item.', Mage::helper('core')->escapeHtml(implode(', ', $skus)));

                $this->_getSession()->addError($message);
            }
        }
        $cart->save();
        $this->_goBack();
    }

    public function validateSkuAction()
    {
        $validateData = Zend_Json::decode($this->getRequest()->getParam(Praxigento_Quickorder_Config::REQ_PARAM_ATTRS_SKU));
        $skus         = array();
        foreach ($validateData as $row) {
            $skus[$row['id']] = $row['value'];
        }
        $filteredData = Praxigento_Quickorder_Config::getResourceSuggestion()->filterSkus(array_values($skus));
        $skus         = array_diff($skus, $filteredData);
        if (!empty($skus)) {
            echo Zend_Json::encode(array_keys($skus));
        }
        exit;
    }

    /**
     * Create suggestion according to customer's query.
     */
    public function suggestionAction()
    {
        $this->loadLayout();
        // parse request params and select products to suggestion
        $formQuery  = $this->getRequest()->getParam(Praxigento_Quickorder_Config::REQ_PARAM_QUERY);
        $collection = null;
        if ($formQuery) {
            $model      = Praxigento_Quickorder_Config::getResourceSuggestion()->setQuery($formQuery);
            $collection = $model->getProductCollection();
        }
        // prepare block to display
        $block = $this->getLayout()->getBlock(Praxigento_Quickorder_Config::UI_BLOCK_SUGGESTION);
        $block->setProductCollection($collection);
        $block->setData(Praxigento_Quickorder_Config::REQ_PARAM_QUERY, $formQuery);
        $block->setTemplate(Praxigento_Quickorder_Config::UI_TMPL_SUGGESTION);
        //
        $this->renderLayout();
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Set back redirect url to response
     * @return Praxigento_Quickorder_QuickorderController
     * @throws Mage_Exception
     */
    protected function _goBack()
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl) {

            if (!$this->_isUrlInternal($returnUrl)) {
                throw new Mage_Exception('External urls redirect to "' . $returnUrl . '" denied!');
            }

            $this->_getSession()->getMessages(true);
            $this->getResponse()->setRedirect($returnUrl);
        } elseif (!Mage::getStoreConfig('checkout/cart/redirect_to_cart')
            && !$this->getRequest()->getParam('in_cart')
            && $backUrl = $this->_getRefererUrl()
        ) {
            $this->getResponse()->setRedirect($backUrl);
        } else {
            if (($this->getRequest()->getActionName() == 'add') && !$this->getRequest()->getParam('in_cart')) {
                $this->_getSession()->setContinueShoppingUrl($this->_getRefererUrl());
            }
            $this->_redirect('checkout/cart');
        }
        return $this;
    }
}
