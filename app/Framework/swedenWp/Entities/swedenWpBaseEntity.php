<?php

// Base Entity. All entities extends this one
class swedenWpBaseEntity {
    /**
     * Magic getter for all properties in extended classes with lazy loading and caching.
     * @param string $key Name of property
     * @return mixed
     */
    public function __get($key)
    {
        // lazy loading
        $method = 'get' . ucfirst($key);
        if (property_exists(get_class($this), $key)) {
            if (is_null($this->$key) and method_exists($this, $method)) {

                $this->$key = $this->$method(); // lazy getter

                return $this->$key; // return the property;

            } elseif (is_int($this->$key) and method_exists($this, $method)) {

                $this->$key = $this->$method(); // int to object

                return $this->$key; // return the property;

            } elseif (!is_null($this->$key)) {
                return $this->$key; // property initialized in constructor or already cached result
            } else {
                if(defined('DEVELOPMENT') && DEVELOPMENT)
                    throw new swedenWpEntityException("There is no method like [{$method}] in class [" . get_class($this) . "]. Maybe you did a typo."); // developer of theme must be precise
                else{
                    return null;
                }
            }
        } else {
            if(defined('DEVELOPMENT') && DEVELOPMENT)
                throw new swedenWpEntityException("There is no property like [{$key}] in class [" . get_class($this) . "]. Maybe you did a typo."); // developer of theme must be precise
            else{
                return null;
            }
        }
    }
}

class swedenWpEntityException extends Exception {
    // exception....can't think anything right now.

}