<?php

namespace Roghumi\Press\Crud\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Roghumi\Press\Crud\Exceptions\HierarchyLoopException;
use Roghumi\Press\Crud\Facades\DomainService;
use Roghumi\Press\Crud\Resources\Domain\Domain;
use Roghumi\Press\Crud\Tests\Mock\User\User;
use Roghumi\Press\Crud\Tests\TestCase;

class DomainServiceTest extends TestCase
{
    /**
     * @group Service
     */
    public function test_domain_hierarchy()
    {
        Domain::factory(20)->create();
        //        1
        //   /   \  \  \
        //  2     3  5  7
        // / \   / \     \
        //4  6  8  9      10
        //   \     \      \
        //    11   12     13

        DomainService::addDomainAsChild(1, 2);
        DomainService::addDomainAsChild(1, 3);
        DomainService::addDomainAsChild(1, 5);
        DomainService::addDomainAsChild(1, 7);

        $this->assertCount(4, DB::table('domain_hierarchy')->where('parent_id', 1)->get());

        DomainService::addDomainAsChild(2, 4);
        DomainService::addDomainAsChild(2, 6);

        $this->assertCount(2, DB::table('domain_hierarchy')->where('parent_id', 2)->get());
        $this->assertCount(6, DB::table('domain_hierarchy')->where('parent_id', 1)->get());

        DomainService::addDomainAsChild(6, 11);

        $this->assertCount(7, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(3, DB::table('domain_hierarchy')->where('parent_id', 2)->get());
        $this->assertCount(1, DB::table('domain_hierarchy')->where('parent_id', 6)->get());
        $this->assertCount(0, DB::table('domain_hierarchy')->where('parent_id', 11)->get());

        DomainService::addDomainAsChild(3, 8);
        DomainService::addDomainAsChild(3, 9);
        DomainService::addDomainAsChild(9, 12);

        $this->assertCount(10, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(3, DB::table('domain_hierarchy')->where('parent_id', 3)->get());
        $this->assertCount(1, DB::table('domain_hierarchy')->where('parent_id', 9)->get());
        $this->assertCount(0, DB::table('domain_hierarchy')->where('parent_id', 11)->get());

        DomainService::addDomainAsChild(7, 10);
        DomainService::addDomainAsChild(10, 13);

        $this->assertCount(2, DB::table('domain_hierarchy')->where('parent_id', 7)->get());
        $this->assertCount(12, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(0, DB::table('domain_hierarchy')->where('parent_id', 11)->get());

        $this->assertDatabaseCount('domain_hierarchy', 12 + 3 + 3 + 1 + 1 + 2 + 1);

        DomainService::removeDomainFromParent(7);

        $this->assertCount(2, DB::table('domain_hierarchy')->where('parent_id', 7)->get());
        $this->assertCount(9, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(0, DB::table('domain_hierarchy')->where('parent_id', 11)->get());

        DomainService::addDomainAsChild(2, 7);

        $this->assertCount(2, DB::table('domain_hierarchy')->where('parent_id', 7)->get());
        $this->assertCount(12, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(6, DB::table('domain_hierarchy')->where('parent_id', 2)->get());
        $this->assertCount(0, DB::table('domain_hierarchy')->where('parent_id', 11)->get());

        DomainService::addDomainAsChild(11, 7);
        $this->assertCount(2, DB::table('domain_hierarchy')->where('parent_id', 7)->get());
        $this->assertCount(12, DB::table('domain_hierarchy')->where('parent_id', 1)->get());
        $this->assertCount(6, DB::table('domain_hierarchy')->where('parent_id', 2)->get());
        $this->assertCount(3, DB::table('domain_hierarchy')->where('parent_id', 11)->get());
        $this->assertCount(4, DB::table('domain_hierarchy')->where('parent_id', 6)->get());

        $this->assertDatabaseCount('domain_hierarchy', 12 + 6 + 3 + 4 + 1 + 2 + 1 + 3);

        $this->assertCount(0, array_diff([7, 10, 11, 13], DomainService::getChildDomainIds(6)->toArray()));
        $this->assertCount(0, array_diff([8, 9, 12], DomainService::getChildDomainIds(3)->toArray()));
        $this->assertCount(0, array_diff([12], DomainService::getChildDomainIds(9)->toArray()));

        $this->assertCount(0, array_diff([3, 1], DomainService::getAncestorDomainIds(9)->toArray()));
        $this->assertCount(0, array_diff([7, 11, 6, 2, 1], DomainService::getAncestorDomainIds(10)->toArray()));

        $this->assertCount(0, array_diff([7, 10, 11, 13], Domain::find(6)->getDescendants()->pluck('id')->toArray()));
        $this->assertCount(0, array_diff([8, 9, 12], Domain::find(3)->getDescendants()->pluck('id')->toArray()));
        $this->assertCount(0, array_diff([12], Domain::find(9)->getDescendants()->pluck('id')->toArray()));

        $this->assertCount(0, array_diff([3, 1], Domain::find(9)->getAncestors()->pluck('id')->toArray()));
        $this->assertCount(0, array_diff([7, 11, 6, 2, 1], Domain::find(10)->getAncestors()->pluck('id')->toArray()));

        $this->assertEquals(DomainService::getRootDomainId(5), 1);

        DomainService::removeDomainFromParent(11);

        $this->assertEquals(DomainService::getRootDomainId(11), 11);
        $this->assertEquals(DomainService::getRootDomainId(13), 11);
        $this->assertEquals(DomainService::getRootDomainId(8), 1);
    }

    /**
     * @group Service
     */
    public function test_domain_user_connection()
    {
        Domain::factory(20)->create();
        //        1
        //   /   \  \  \
        //  2     3  5  7
        // / \   / \     \
        //4  6  8  9      10
        //   \     \      \
        //    11   12     13

        DomainService::addDomainAsChild(1, 2);
        DomainService::addDomainAsChild(1, 3);
        DomainService::addDomainAsChild(1, 5);
        DomainService::addDomainAsChild(1, 7);
        DomainService::addDomainAsChild(2, 4);
        DomainService::addDomainAsChild(2, 6);
        DomainService::addDomainAsChild(6, 11);
        DomainService::addDomainAsChild(3, 8);
        DomainService::addDomainAsChild(3, 9);
        DomainService::addDomainAsChild(9, 12);
        DomainService::addDomainAsChild(7, 10);
        DomainService::addDomainAsChild(10, 13);

        User::factory(5)->create();

        DomainService::addUserToDomain(2, 1);
        $this->assertDatabaseCount('domain_user', 1);
        DomainService::addUserToDomain(2, 1);
        DomainService::addUserToDomain(3, 1);
        DomainService::addUserToDomain(4, 1);
        DomainService::addUserToDomain(4, 2);
        DomainService::addUserToDomain(4, 3);
        $this->assertDatabaseCount('domain_user', 5);
        DomainService::removeUserFromDomain(4, 1);
        $this->assertDatabaseCount('domain_user', 4);
        $this->assertCount(2, Domain::find(1)->getUsers());
        $this->assertCount(1, Domain::find(3)->getUsers());
    }

    /**
     * @group Service
     */
    public function test_domain_hierarchy_loop_exception()
    {
        Domain::factory(5)->create();

        DomainService::addDomainAsChild(3, 4);
        DomainService::addDomainAsChild(2, 3);
        DomainService::addDomainAsChild(1, 2);

        $this->assertThrows(function () {
            DomainService::addDomainAsChild(4, 1);
        }, HierarchyLoopException::class);
    }
}
