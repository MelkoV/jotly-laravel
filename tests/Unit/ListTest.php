<?php

namespace Tests\Unit;

use App\Data\ListItem\ShoppingData;
use App\Data\ListItem\TodoData;
use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use App\Models\ShoppingListItem;
use App\Models\TodoListItem;
use Carbon\Carbon;
use Tests\TestCase;

class ListTest extends TestCase
{
    public function test_shopping_data_cast(): void
    {
        $item = new ShoppingListItem()->fill(['data' => ['count' => 10.5, 'unit' => ProductUnit::Thing]]);
        $this->assertTrue($item->data instanceof ShoppingData);
        $this->assertTrue($item->data->unit instanceof ProductUnit);
        $this->assertTrue($item->data->unit === ProductUnit::Thing);
        $this->assertTrue($item->data->count === 10.5);
    }

    public function test_todo_data_cast(): void
    {
        $item = new TodoListItem()->fill(['data' => ['deadline' => '2026-02-01', 'priority' => TodoPriority::Medium]]);
        $this->assertTrue($item->data instanceof TodoData);
        $this->assertTrue($item->data->priority instanceof TodoPriority);
        $this->assertTrue($item->data->priority === TodoPriority::Medium);
        $this->assertTrue($item->data->deadline instanceof Carbon);
        $this->assertTrue($item->data->deadline->format('Y-m-d') === '2026-02-01');
    }
}
