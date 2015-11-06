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
 * Order to sort suggestion results before display.
 */
class Praxigento_Quickorder_Model_Source_SortOrder
{
    const ASC  = 'ASC';
    const DESC = 'DESC';

    /** @var array|null cache for the data */
    protected $_order = null;

    public function toOptionArray()
    {
        if (!is_null($this->_order)) {
            return $this->_order;
        }

        $this->_order[] = array(
            'label' => Praxigento_Quickorder_Config::helper()->__('Ascending ↑'),
            'value' => self::ASC
        );

        $this->_order[] = array(
            'label' => Praxigento_Quickorder_Config::helper()->__('Descending ↓'),
            'value' => self::DESC
        );

        /** sorted by key: ASC / DESC, cause of default value is ASC (see SysConf w/o data) */
        ksort($this->_order);
        return $this->_order;
    }

}

