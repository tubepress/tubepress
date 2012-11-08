<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of <project> (https://github.com/ehough/pulsar)
 *
 * <project> is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * <project> is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with <project>.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class FakeCompilerPass implements ehough_iconic_api_compiler_ICompilerPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ehough_iconic_impl_ContainerBuilder $container
     *
     * @return void
     */
    public final function process(ehough_iconic_impl_ContainerBuilder $container)
    {
        //do nothing
    }
}