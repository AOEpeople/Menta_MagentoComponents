<?php

/**
 * Roles
 *
 * @author Fabrizio Branca
 * @since 2014-03-24
 */
class MagentoComponents_Pages_Admin_Roles extends MagentoComponents_Pages_Admin {

    /**
     * Get roles url
     *
     * @return string
     */
    public function getRolesUrl() {
        return $this->getAdminUrl() . '/permissions_role/';
    }

    /**
     * Use filter to search for a give role
     *
     * @param $role
     */
    public function searchRoleName($role) {
        $this->getHelperCommon()->type('id=roleGrid_filter_role_name', $role . \WebDriver\Key::ENTER);
    }

    /**
     * Assert role present
     *
     * @param string $role
     * @return void
     */
    public function assertRolesPresent($role)
    {
        $this->getHelperCommon()->open($this->getRolesUrl());

        $this->searchRoleName($role);
        sleep(1); // TODO: that's not nice!
        $grid = Menta_ComponentManager::get('MagentoComponents_Pages_Admin_Grid'); /* @var $grid MagentoComponents_Pages_Admin_Grid */
        $grid->assertGridContainsValueInColumn('roleGrid', $role, 'field_1');

    }

}
