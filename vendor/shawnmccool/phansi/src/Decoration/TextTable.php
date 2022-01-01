<?php namespace PhAnsi\Decoration;

use PhAnsi\Collection;

/**
 * $array = [
 *      ['column1', 'column2'],
 *      ['column1', 'column2']
 * ];
 *
 * echo TextTable::make()
 * ->withTitle('Feature Flags')
 * ->withHeaders('Flag', 'Is Enabled?')
 * ->withRows($array)
 * ->toString();
 */
final class TextTable
{
    private Collection $headers;
    private Collection $rows;

    private int $columnPadding = 1;
    private string $title = '';

    private function __construct()
    {
        $this->headers = Collection::empty();
        $this->rows = Collection::empty();
    }

    public function withTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function withHeaders(...$headers): self
    {
        $this->headers = Collection::of($headers);
        return $this;
    }

    public function withRows(array $rows): self
    {
        $this->rows = Collection::of($rows);
        return $this;
    }

    public function toString(): string
    {
        return $this->render();
    }

    public function toMarkdown(): string
    {
        return "```\n" . $this->render() . "```";
    }

    public function getWidth(): int
    {
        return $this->getFullWidth(
            $this->calculateColumnWidths()
        );
    }

    private function render(): string
    {
        $columnWidths = $this->calculateColumnWidths();

        $title = empty($this->title) ? '' : $this->centerText($columnWidths, $this->title);

        $top = $this->makeHorizontalLine($columnWidths);
        $divider = $this->makeHorizontalLine($columnWidths);
        $bottom = $this->makeHorizontalLine($columnWidths);

        $headers = $this->makeDataRow($this->headers, $columnWidths);

        $rows = $this->rows->map(
            function ($row) use ($columnWidths) {
                return $this->makeDataRow(Collection::of($row), $columnWidths);
            }
        )->implode('');

        return $title . $top . $headers . $divider . $rows . $bottom;
    }

    private function calculateColumnWidths(): array
    {
        $columnWidths = [];

        $headers = Collection::of([$this->headers->toArray()]);
        $rows = $this->rows->map(
            function ($row) {
                return array_values($row);
            }
        );

        $merged = $headers->merge($rows);

        // foreach row
        foreach ($merged as $rowCount => $row) {
            // foreach column
            foreach ($row as $columnCount => $column) {
                // initialize the column width for this column
                if ( ! isset($columnWidths[$columnCount])) {
                    $columnWidths[$columnCount] = 0;
                }

                // 
                $strippedColumn = $this->stripAnsi($column);

                if (strlen($strippedColumn) > $columnWidths[$columnCount]) {
                    $columnWidths[$columnCount] = strlen($strippedColumn);
                }
            }
        }

        return array_values($columnWidths);
    }

    private function makeHorizontalLine(array $columnWidths)
    {
        $length = $this->getFullWidth($columnWidths);

        return str_repeat('-', $length) . "\n";
    }

    private function makeDataRow(Collection $columns, array $widths): string
    {
        $paddedColumns = [];

        foreach (array_values($columns->toArray()) as $columnNumber => $column) {
            $paddedColumns[] = $this->padColumn($column, $widths[$columnNumber]);
        }

        $delimiter = $this->cellPadding() . '|' . $this->cellPadding();

        return '|' . $this->cellPadding() . implode($delimiter, $paddedColumns) . $this->cellPadding() . "|\n";
    }

    private function padColumn(string $text, int $width)
    {
        $ansiCharacterPadding = strlen($text) - strlen($this->stripAnsi($text));
        return str_pad($text, $width + $ansiCharacterPadding);
    }

    private function stripAnsi(string $text): string
    {
        $text = preg_replace('/\x1b([\[()])[;?0-9]*[0-9A-Za-z]/', "", $text);
        $text = preg_replace('/\x1b([\[()])[;?0-9]*[0-9A-Za-z]/', "", $text);
        $text = preg_replace('/[\x03|\x1a]/', "", $text);
        return $text;
    }

    private function cellPadding()
    {
        return str_repeat(' ', $this->columnPadding);
    }

    private function centerText(array $columnWidths, string $text): string
    {
        $strippedText = $this->stripAnsi($text);

        $width = $this->getFullWidth($columnWidths);
        $halfWidth = intdiv($width, 2);
        $halfTextWidth = intdiv(strlen($strippedText), 2);

        return str_repeat(' ', $halfWidth - $halfTextWidth) . $text . str_repeat(' ', $halfWidth - $halfTextWidth) . "\n";
    }

    private function getFullWidth(array $columnWidths): int
    {
        // the sum of all columns + the count of the columns times padding + the count of columns times separator aka |
        return array_sum($columnWidths) + (count($columnWidths) * $this->columnPadding * 2) + (count($columnWidths) + 1);
    }

    public static function make()
    {
        return new static;
    }
}