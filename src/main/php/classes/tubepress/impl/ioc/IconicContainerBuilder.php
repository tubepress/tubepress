<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_impl_ioc_IconicContainerBuilder extends ehough_iconic_ContainerBuilder
{
    /**
     * Replaces service references by the real service instance and evaluates expressions.
     *
     * @param mixed $value A value
     *
     * @return mixed The same value with all service references replaced by
     *               the real service instances and all expressions evaluated
     */
    public function resolveServices($value)
    {
        if (is_array($value)) {
            foreach ($value as &$v) {
                $v = $this->resolveServices($v);
            }
        } elseif ($value instanceof tubepress_api_ioc_Reference) {
            $value = $this->get((string) $value, self::NULL_ON_INVALID_REFERENCE);
        } elseif ($value instanceof ehough_iconic_Definition) {
            $value = $this->createService($value, null);
        }

        return $value;
    }
}