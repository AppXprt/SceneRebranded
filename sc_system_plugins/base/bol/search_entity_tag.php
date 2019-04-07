<?php

/**
 * Data Transfer Object for `base_search_entity_tag` table.
 *
 * @package sc_system_plugins.base.bol
 * @since 1.0
 */
class BOL_SearchEntityTag extends SC_Entity
{
    /**
     * Entity tag
     * @var string
     */
    public $entityTag;

    /**
     * Search entity Id
     * @var integer
     */
    public $searchEntityId;
}