<?php

namespace Tests\Unit;

use App\Data\ListItem\ListItemAttributesData;
use App\Enums\ProductUnit;
use App\Enums\TodoPriority;
use App\Models\ListItem;
use Carbon\Carbon;
use Tests\TestCase;

class ListTest extends TestCase
{
    public function test_shopping_data_cast(): void
    {
        $item = new ListItem()->fill(['data' => ['count' => 10.5, 'unit' => ProductUnit::Thing]]);
        $this->assertTrue($item->data instanceof ListItemAttributesData);
        $this->assertTrue($item->data->unit instanceof ProductUnit);
        $this->assertTrue($item->data->unit === ProductUnit::Thing);
        $this->assertTrue($item->data->count === 10.5);
    }

    public function test_todo_data_cast(): void
    {
        $item = new ListItem()->fill(['data' => ['deadline' => Carbon::now(), 'priority' => TodoPriority::Medium]]);
        $this->assertTrue($item->data instanceof ListItemAttributesData);
        $this->assertTrue($item->data->priority instanceof TodoPriority);
        $this->assertTrue($item->data->priority === TodoPriority::Medium);
        $this->assertTrue($item->data->deadline instanceof Carbon);
        $this->assertTrue($item->data->deadline->format('Y-m-d') === Carbon::today()->format('Y-m-d'));
    }
}
