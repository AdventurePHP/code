<?php

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
import('modules::genericormapper::data', 'GenericDomainObject'); 
 
/**
 * @package modules::genericormapper::data
 * @class TreeObject
 *
 * @author Nicolas Pecher
 * @version
 * Version 0.1, 23.04.2012
 */
class TreeObject extends GenericDomainObject {

    /**
     * @var GenericDomainObject The parent object of this navigation node
     */
    protected $parentObject = null;
    
    /**
     * @var GenericDomainObject The children of this navigation node
     */
    protected $childObjects = array();
    
    /**
     * @public
     * 
     * Sets the parent object
     *
     * @param TreeObject $parentObject The parent object
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.03.2012
     */
    public function setParentObject(TreeObject $parentObject) {
        $this->parentObject = $parentObject;     
    }
    
    /**
     * @public
     *
     * Returns the parent object
     *
     * @return TreeObject The parent object
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.03.2012
     */
    public function getParentObject() {
        return $this->parentObject;
    }
    
    /**
     * @public
     *
     * Adds a child object
     *
     * @param TreeObject $childObject An child object
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.03.2012
     */
    public function addChildObject(TreeObject $childObject) {
        $this->childObjects[] = $childObject;
    } 
    
    /**
     * @public
     *
     * Adds an array of child objects
     *
     * @param array $childObjects An array of child objects
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.03.2012
     */
    public function addChildObjects(array $childObjects) {
        foreach ($childObjects as $childObject) {
            $this->addChildObject($childObject);
        }
    }
    
    /**
     * @public
     *
     * Returns all child objects
     *
     * @return array A list of the child objects
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.03.2012
     */
    public function getChildObjects() {
        return $this->childObjects;
    } 
    
    /**
     * @public
     *
     * Returns the number of child objects
     *
     * @return int The number of child objects
     *
     * @author Nicolas Pecher
     * @version
     * Version 0.1, 23.04.2012
     */
    public function loadChildObjectCount() {
        return count($this->childObjects);
    }  
}
