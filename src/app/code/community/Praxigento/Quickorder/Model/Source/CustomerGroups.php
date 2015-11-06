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
class Praxigento_Quickorder_Model_Source_CustomerGroups
{
    /**
     * Customer groups options array
     *
     * @var null|array
     */
    protected $_options;

    /**
     * Retrieve customer groups as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $data_arr = Mage::getResourceModel('customer/group_collection')
                ->loadData()->toOptionArray();
            // sort by name
            foreach ($data_arr as $entry) {
                $this->_options[$entry['label']] = $entry;
            }
            /** QOF-15 */
            $allDisallowed['label']                  = Praxigento_Quickorder_Config::helper()->__('-- NONE --');
            $allDisallowed['value']                  = '-1';
            $this->_options[$allDisallowed['label']] = $allDisallowed;
            ksort($this->_options);
        }
        return $this->_options;
    }
}
