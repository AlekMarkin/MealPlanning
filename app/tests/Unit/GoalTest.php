<?php

namespace Tests\Unit;

use App\Models\Goal;
use PHPUnit\Framework\TestCase;

//unit tests for Goal model
class GoalTest extends TestCase
{
    //test that metrics returns all nutrition metrics
    public function test_metrics_returns_all_metrics(): void
    {
        $metrics = Goal::metrics();

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('calories', $metrics);
        $this->assertArrayHasKey('protein', $metrics);
        $this->assertArrayHasKey('carbs', $metrics);
        $this->assertArrayHasKey('fat', $metrics);
        $this->assertArrayHasKey('fiber', $metrics);
        $this->assertArrayHasKey('sugar', $metrics);
        $this->assertArrayHasKey('sodium', $metrics);
        $this->assertArrayHasKey('carbon_footprint', $metrics);
    }

    //test that each metric has label and unit
    public function test_metrics_have_label_and_unit(): void
    {
        $metrics = Goal::metrics();

        foreach ($metrics as $key => $metric) {
            $this->assertArrayHasKey('label', $metric, "Metric {$key} missing label");
            $this->assertArrayHasKey('unit', $metric, "Metric {$key} missing unit");
        }
    }

    //test that calories metric is correct
    public function test_calories_metric_is_correct(): void
    {
        $metrics = Goal::metrics();

        $this->assertEquals('Calories', $metrics['calories']['label']);
        $this->assertEquals('kcal', $metrics['calories']['unit']);
    }

    //test that metrics count is correct
    public function test_metrics_has_eight_items(): void
    {
        $metrics = Goal::metrics();

        $this->assertCount(8, $metrics);
    }

    //test that comparators returns both options
    public function test_comparators_returns_both_options(): void
    {
        $comparators = Goal::comparators();

        $this->assertIsArray($comparators);
        $this->assertArrayHasKey('at_most', $comparators);
        $this->assertArrayHasKey('at_least', $comparators);
        $this->assertCount(2, $comparators);
    }

    //test that comparators have label and symbol
    public function test_comparators_have_label_and_symbol(): void
    {
        $comparators = Goal::comparators();

        foreach ($comparators as $key => $comparator) {
            $this->assertArrayHasKey('label', $comparator, "Comparator {$key} missing label");
            $this->assertArrayHasKey('symbol', $comparator, "Comparator {$key} missing symbol");
        }
    }

    //test at_most comparator symbol is correct
    public function test_at_most_comparator_symbol(): void
    {
        $comparators = Goal::comparators();

        $this->assertEquals('≤', $comparators['at_most']['symbol']);
    }

    //test at_least comparator symbol is correct
    public function test_at_least_comparator_symbol(): void
    {
        $comparators = Goal::comparators();

        $this->assertEquals('≥', $comparators['at_least']['symbol']);
    }
}