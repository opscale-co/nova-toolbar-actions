<?php

namespace Opscale\NovaToolbarActions\Tests;

use Laravel\Nova\Actions\Action;
use Opscale\NovaToolbarActions\ShowAsButton;

class ShowAsButtonTest extends TestCase
{
    /** @test */
    public function it_can_set_show_as_button_meta()
    {
        $action = new class extends Action
        {
            use ShowAsButton;
        };

        $action->showAsButton();

        $this->assertTrue($action->meta['showAsButton']);
    }

    /** @test */
    public function it_can_disable_show_as_button()
    {
        $action = new class extends Action
        {
            use ShowAsButton;
        };

        $action->showAsButton(false);

        $this->assertFalse($action->meta['showAsButton']);
    }

    /** @test */
    public function it_can_set_show_on_index_toolbar()
    {
        $action = new class extends Action
        {
            use ShowAsButton;
        };

        $action->showOnIndexToolbar();

        $this->assertTrue($action->meta['showOnIndexToolbar']);
    }

    /** @test */
    public function it_can_set_show_on_detail_toolbar()
    {
        $action = new class extends Action
        {
            use ShowAsButton;
        };

        $action->showOnDetailToolbar();

        $this->assertTrue($action->meta['showOnDetailToolbar']);
    }

    /** @test */
    public function it_returns_the_action_instance_for_chaining()
    {
        $action = new class extends Action
        {
            use ShowAsButton;
        };

        $result = $action->showAsButton();

        $this->assertSame($action, $result);
    }
}
