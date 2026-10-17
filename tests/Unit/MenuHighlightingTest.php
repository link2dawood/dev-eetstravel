<?php
namespace Tests\Unit;

use App\Helper\DashboardHelper;
use PHPUnit\Framework\TestCase;

class MenuHighlightingTest extends TestCase
{
    public function test_resource_menu_stays_active_across_its_routes(): void
    {
        foreach (['clients','hotel','driver','users','roles','flights','accounting'] as $module) {
            foreach (['index','create','show','edit','update'] as $action) {
                $this->assertSame('active', DashboardHelper::isMenuActive($module.'.index', $module.'.'.$action));
            }
            $this->assertSame('', DashboardHelper::isMenuActive($module.'.index', $module.'_other.edit'));
        }
        $this->assertTrue(DashboardHelper::isMenuTreeViewActive(['hotel.index','event.index'], 'hotel.edit'));
        $this->assertFalse(DashboardHelper::isMenuTreeViewActive(['hotel.index','event.index'], 'clients.edit'));
        $this->assertSame('', DashboardHelper::isMenuActive('supplier_search', 'supplier_search_extra'));
        $this->assertSame('active', DashboardHelper::isMenuActive('supplier_search', 'supplier_search'));
        $this->assertSame('', DashboardHelper::isMenuActive('clients.index', null));
    }
}
