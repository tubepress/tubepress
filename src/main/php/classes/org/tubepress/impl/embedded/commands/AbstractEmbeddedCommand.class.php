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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_template_Variable',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_api_theme_ThemeHandler',
    'org_tubepress_impl_embedded_EmbeddedPlayerUtils',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Base class for embedded commands.
 */
abstract class org_tubepress_impl_embedded_commands_AbstractEmbeddedCommand implements org_tubepress_spi_patterns_cor_Command
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        global $tubepress_base_url;

        $providerName = $context->providerName;
        $videoId      = $context->videoId;
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext  = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        if (!$this->_canHandle($providerName, $videoId, $ioc, $execContext)) {
            return false;
        }

        $theme    = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $template = $theme->getTemplateInstance($this->_getTemplatePath($providerName, $videoId, $ioc, $execContext));

        $context->template                   = $template;
        $context->dataUrl                    = $this->_getEmbeddedDataUrl($providerName, $videoId, $ioc, $execContext);
        $context->embeddedImplementationName = $this->_getEmbeddedImplName();

        /* signal that we've handled execution */
        return true;
    }

    protected abstract function _getEmbeddedImplName();

    protected abstract function _canHandle($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $execContext);

    protected abstract function _getTemplatePath($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $execContext);

    protected abstract function _getEmbeddedDataUrl($providerName, $videoId, org_tubepress_api_ioc_IocService $ioc, org_tubepress_api_exec_ExecutionContext $execContext);
}
