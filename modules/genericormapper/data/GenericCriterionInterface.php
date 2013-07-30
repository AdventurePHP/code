<?php
    namespace APF\modules\genericormapper\data;

    /**
     * <!--
     * This file is part of the adventure php framework (APF) published under
     * http://adventure-php-framework.org.
     *
     * The APF is free software: you can redistribute it and/or modify
     * it under the terms of the GNU Lesser General Public License as published
     * by the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * The APF is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
     * GNU Lesser General Public License for more details.
     *
     * You should have received a copy of the GNU Lesser General Public License
     * along with the APF. If not, see http://www.gnu.org/licenses/lgpl-3.0.txt.
     * -->
     */

    /**
     * @package APF\modules\genericormapper\data
     * @class GenericCriterionInterface
     *
     * Defines the Interface which must be implemented by every criterion object.
     *
     * @author Tobias Lückel|Megger
     * @version
     * Version 0.1, 15.05.2013<br />
     */
    

    interface GenericCriterionInterface {
        /**
         * @public
         *
         * Method to set the current link between properties.
         *
         * @param string $operator The operator to use (e.g. AND).
         * @return GenericCriterionObject Returns itself.
         *
         * @author Lutz Mahlstedt
         * @version
         * Version 0.1, 11.03.2011<br />
         */
        public function setLogicalOperator($operator);

        /**
         * @public
         *
         * Method to add a relation indicator.
         * <p/>
         * Generates a 'source' and 'target' unique id for the relation name if not set.
         *
         * @param string $relationName name of the relation between the object in the second argument and the object to load
         * @param GenericORMapperDataObject $sourceObject related object
         *
         * @return GenericCriterionObject Returns itself.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 17.06.2008<br />
         * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
         * Version 0.3, 27.04.2011 (Generate a uniqid for the relation name.)<br />
         */
        public function addRelationIndicator($relationName, GenericORMapperDataObject $sourceObject);

        /**
         * @public
         *
         * Returns the relation definitions for the current query.
         *
         * @return string[] The relation definitions.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 23.04.2010<br />
         */
        public function getRelations();

        /**
         * @public
         *
         * Returns a uniqid for the given relation name. If no uniqid is set, it will
         * be generated.
         *
         * @param string $relationName The given relation name
         * @param boolean $returningSource Indicates, if returning source uniqid or target uniqid. Default is true.
         * @return string The uniqid
         *
         * @author Tobias Lückel
         * @version
         * Version 0.1, 27.04.2011<br />
         */
        public function getUniqueRelationId($relationName, $returningSource = true);

        /**
         * @public
         *
         * Method to add a limit clause to the criterion object. If the second param is not present,
         * the first param indicates the maximum amount of objects in a list.
         *
         * @param int $startOrCount start pointer
         * @param int $count optional count parameter
         *
         * @return GenericCriterionObject Returns itself.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 17.06.2008<br />
         * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
         */
        public function addCountIndicator($startOrCount, $count = null);

        /**
         * @public
         *
         * Returns the defined limitations for the current query.
         *
         * @return string[] The limit definition.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 23.04.2010<br />
         */
        public function getLimitDefinition();

        /**
         * @public
         *
         * Method to add a property to the where list.
         *
         * @param string $attributeName name of the attribute.
         * @param string $attributeValue value of the attribute.
         * @param string $comparisonOperator The comparison operator (e.g. =, !=).
         *
         * @return GenericCriterionObject Returns itself.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 17.06.2008<br />
         * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
         */
        public function addPropertyIndicator($attributeName, $attributeValue, $comparisonOperator = '=');

        /**
         * @public
         *
         * Returns the attribute restrictions defined.
         *
         * @return string[] Attribute restrictions for the current query.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 28.05.2010<br />
         */
        public function getPropertyDefinition();

        /**
         * @public
         *
         * Method to add a order indicator.
         *
         * @param string $attributeName name of the attribute
         * @param string $orderDirection direction of ordering
         *
         * @return GenericCriterionObject Returns itself.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 17.06.2008<br />
         * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
         */
        public function addOrderIndicator($attributeName, $orderDirection = 'ASC');

        /**
         * @public
         *
         * Returns the order indicators for the current query.
         *
         * @return string[] The order indicators.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 23.04.2010<br />
         */
        public function getOrderIndicators();

        /**
         * @public
         *
         * Method to add a property, that should be loaded into the result object or object list.
         *
         * @param string $propertyName Name of the desired property
         *
         * @return GenericCriterionObject Returns itself.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 21.06.2008<br />
         * Version 0.2, 18.07.2010 (Added "Fluent Interface" support.)<br />
         */
        public function addLoadedProperty($propertyName);

        /**
         * @public
         *
         * Return the list of properties to load with the current query.
         *
         * @return string[] The properties to load.
         *
         * @author Christian Achatz
         * @version
         * Version 0.1, 23.04.2010<br />
         */
        public function getLoadedProperties();
    }