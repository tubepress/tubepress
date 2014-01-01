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

class tubepress_impl_patterns_toposort_TopologicalSort
{
    /**
     * Author: Dan (http://www.calcatraz.com)
     * Licensing: None - use it as you see fit
     * Updates: http://www.calcatraz.com/blog/php-topological-sort-function-384
     */
    public static function sort($nodeIds, $edges)
    {
        // initialize variables
        $toReturn       = array();
        $tempSortedList = array();
        $nodeTable      = array();

        // remove duplicate nodes
        $nodeIds = array_unique($nodeIds);

        // remove duplicate edges
        $edgeHashes = array();

        foreach ($edges as $edgeStart => $edgeEnd) {

            $edgeHash = md5(serialize($edgeEnd));

            if (in_array($edgeHash, $edgeHashes)) {

                unset($edges[$edgeStart]);

            } else {

                $edgeHashes[] = $edgeHash;
            };
        }

        // Build a lookup table of each node's edges
        foreach ($nodeIds as $nodeId) {

            $nodeTable[$nodeId] = array(

                'in'  => array(),
                'out' => array()
            );

            foreach ($edges as $edgeEnd) {

                if ($nodeId === $edgeEnd[0]) {

                    $nodeTable[$nodeId]['out'][] = $edgeEnd[1];
                }

                if ($nodeId === $edgeEnd[1]) {

                    $nodeTable[$nodeId]['in'][] = $edgeEnd[0];
                }
            }
        }

        // While we have nodes left, we pick a node with no inbound edges,
        // remove it and its edges from the graph, and add it to the end
        // of the sorted list.
        foreach ($nodeTable as $nodeId=>$n) {

            if (empty($n['in'])) {

                $tempSortedList[] = $nodeId;
            }
        }

        while (!empty($tempSortedList)) {

            $toReturn[] = $nodeId = array_shift($tempSortedList);

            foreach ($nodeTable[$nodeId]['out'] as $outNodeId) {

                $nodeTable[$outNodeId]['in'] = array_diff($nodeTable[$outNodeId]['in'], array($nodeId));

                if (empty($nodeTable[$outNodeId]['in'])) {

                    $tempSortedList[] = $outNodeId;
                }
            }

            $nodeTable[$nodeId]['out'] = array();
        }

        // Check if we have any edges left unprocessed
        foreach ($nodeTable as $n) {

            if (!empty($n['in']) || !empty($n['out'])) {

                return null; // not sortable as graph is cyclic
            }
        }

        return $toReturn;
    }
}