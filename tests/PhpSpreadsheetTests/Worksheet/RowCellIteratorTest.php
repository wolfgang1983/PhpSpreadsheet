<?php

namespace PhpOffice\PhpSpreadsheetTests\Worksheet;

use PhpOffice\PhpSpreadsheet\Cell;
use PhpOffice\PhpSpreadsheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\RowCellIterator;
use PHPUnit_Framework_TestCase;

class RowCellIteratorTest extends PHPUnit_Framework_TestCase
{
    public $mockWorksheet;
    public $mockRowCell;

    public function setUp()
    {
        $this->mockCell = $this->getMockBuilder(Cell::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet = $this->getMockBuilder(Worksheet::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockWorksheet->expects($this->any())
            ->method('getHighestColumn')
            ->will($this->returnValue('E'));
        $this->mockWorksheet->expects($this->any())
            ->method('getCellByColumnAndRow')
            ->will($this->returnValue($this->mockCell));
    }

    public function testIteratorFullRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet);
        $RowCellIndexResult = 'A';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorStartEndRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'B';
        self::assertEquals($RowCellIndexResult, $iterator->key());

        foreach ($iterator as $key => $RowCell) {
            self::assertEquals($RowCellIndexResult++, $key);
            self::assertInstanceOf(Cell::class, $RowCell);
        }
    }

    public function testIteratorSeekAndPrev()
    {
        $ranges = range('A', 'E');
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $RowCellIndexResult = 'D';
        $iterator->seek('D');
        self::assertEquals($RowCellIndexResult, $iterator->key());

        for ($i = 1; $i < array_search($RowCellIndexResult, $ranges); ++$i) {
            $iterator->prev();
            $expectedResult = $ranges[array_search($RowCellIndexResult, $ranges) - $i];
            self::assertEquals($expectedResult, $iterator->key());
        }
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testSeekOutOfRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->seek(1);
    }

    /**
     * @expectedException \PhpOffice\PhpSpreadsheet\Exception
     */
    public function testPrevOutOfRange()
    {
        $iterator = new RowCellIterator($this->mockWorksheet, 2, 'B', 'D');
        $iterator->prev();
    }
}
