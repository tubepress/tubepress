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

/**
 * A detailed list of the core TubePress events.
 *
 * Each event name can be referred to either by its raw name (e.g. `tubepress.core.cssjs.stylesheets`)
 * or as a constant reference (e.g. `tubepress_app_api_const_event_EventNames::CSS_JS_STYLESHEETS`). The latter
 * simply removes undocumented strings from your code and can help to prevent typos.
 *
 * @package TubePress\Const\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_options_api_Constants
{
    /**
     * This event is fired when TubePress looks up the label for a specific option.
     *
     * @subject `string` The untranslated (i.e. in English) option label.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTION_GET_LABEL = 'tubepress.core.options.getLabel';

    /**
     * This event is fired when TubePress looks up the description for a specific option.
     *
     * @subject `string` The untranslated (i.e. in English) option description.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTION_GET_DESCRIPTION = 'tubepress.core.options.getDescription';

    /**
     * This event is fired when TubePress looks up the default value for a specific option. Typically
     * this only happens the first time TubePress is used on a system, but may also fire
     * after an upgrade.
     *
     * @subject `mixed` The default value of an option. May be null.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTION_GET_DEFAULT_VALUE = 'tubepress.core.options.getDefaultValue';

    /**
     * This event is fired when TubePress looks the acceptable values for an option. This
     * only applies to options that take on discrete values.
     *
     * @subject `array`|`null` Initially null, the acceptable values for this option. This *may* be an associative array
     *                         where the keys are values and the values are untranslated labels. You can use
     *                         {@link tubepress_platform_impl_util_LangUtils::isAssociativeArray()} to check the type of array.
     *
     * @argument <var>optionName</var> (`string`): The name of the option.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTION_GET_ACCEPTABLE_VALUES = 'tubepress.core.options.getAcceptableValues';

    /**
     * This event is fired when any TubePress option (a name-value pair) is being set.
     *
     * @subject `string[]` The errors found for this option's value. Initially empty, listeners may add
     *                     to the array.
     *
     * @argument <var>optionName</var> (`string`): The name of the option being set.
     * @argument <var>optionValue</var> (`mixed`): The value of the option being set.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_OPTION_SET = 'tubepress.core.options.set';

    /**
     * This event is fired when a name-value pair is being read from external input.
     *
     * @subject `mixed` The incoming value.
     *
     * @argument <var>optionName</var> (`string`): The incoming name.
     *
     * @api
     * @since 4.0.0
     */
    const EVENT_NVP_READ_FROM_EXTERNAL_INPUT = 'tubepress.core.options.event.readNvpFromExternalInput';

    const IOC_PARAM_EASY_VALIDATION = 'tubepress.core.options.param.easyValidation';

    const IOC_PARAM_EASY_ACCEPTABLE_VALUES = 'tubepress.core.options.tag.easyAcceptableValues';

    const IOC_PARAM_EASY_REFERENCE = 'tubepress.core.options.tag.easyReference';

    const IOC_PARAM_EASY_TRIMMER = 'tubepress.core.options.ioc.param.easyTrimmer';
}