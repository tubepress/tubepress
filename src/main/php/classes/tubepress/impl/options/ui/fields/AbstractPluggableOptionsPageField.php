<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Base class for HTML fields.
 */
abstract class tubepress_impl_options_ui_fields_AbstractPluggableOptionsPageField implements tubepress_spi_options_ui_Field
{
    const TEMPLATE_VAR_NAME  = 'tubepress_impl_options_ui_fields_AbstractField__name';

    /**
     * Gets the title of this field, usually consumed by humans.
     *
     * @return string The title of this field. May be empty or null.
     */
    public final function getTitle()
    {
        return $this->_getMessage($this->getRawTitle());
    }

    /**
     * Gets the description of this field, usually consumed by humans.
     *
     * @return string The description of this field. May be empty or null.
     */
    public final function getDescription()
    {
        $originalDescription = $this->_getMessage($this->getRawDescription());

        return $this->getModifiedDescription($originalDescription);
    }

    /**
     * Override point.
     *
     * Allows subclasses to further modify the description for this field.
     *
     * @param $originalDescription string The original description as calculated by AbstractField.php.
     *
     * @return string The (possibly) modified description for this field.
     */
    protected function getModifiedDescription($originalDescription)
    {
        //override point
        return $originalDescription;
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected abstract function getRawTitle();

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected abstract function getRawDescription();

    protected final function allProviders()
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $videoProviders             = $serviceCollectionsRegistry->getAllServicesOfType(tubepress_spi_provider_PluggableVideoProviderService::_);

        $toReturn = array();

        foreach ($videoProviders as $videoProvider) {

            $toReturn[] = $videoProvider->getName();
        }

        return $toReturn;
    }

    private function _getMessage($raw)
    {
        if ($raw == '') {

            return '';
        }

        $messageService = tubepress_impl_patterns_ioc_KernelServiceLocator::getMessageService();

        return $messageService->_($raw);
    }
}