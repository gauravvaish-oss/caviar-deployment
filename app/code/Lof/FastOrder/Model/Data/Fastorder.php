<?php
/**
 * Copyright (c) 2019  Landofcoder
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Lof\FastOrder\Model\Data;

use Lof\FastOrder\Api\Data\FastorderInterface;

class Fastorder extends \Magento\Framework\Api\AbstractExtensibleObject implements FastorderInterface
{

    /**
     * Get fastorder_id
     * @return string|null
     */
    public function getFastorderId()
    {
        return $this->_get(self::FASTORDER_ID);
    }

    /**
     * Set fastorder_id
     * @param string $fastorderId
     * @return \Lof\FastOrder\Api\Data\FastorderInterface
     */
    public function setFastorderId($fastorderId)
    {
        return $this->setData(self::FASTORDER_ID, $fastorderId);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Lof\FastOrder\Api\Data\FastorderExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Lof\FastOrder\Api\Data\FastorderExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Lof\FastOrder\Api\Data\FastorderExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
