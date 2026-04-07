<?php

namespace Habbo\Gamedata\Concerns;

use RuntimeException;
use XMLWriter;

trait WritesGameDataXml
{
    private ?XMLWriter $xmlWriter = null;

    protected function withWriter(callable $callback): string
    {
        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->startDocument('1.0', 'UTF-8');
        $this->xmlWriter->setIndent(true);

        $callback();

        $this->xmlWriter->endDocument();
        $output = $this->xmlWriter->outputMemory();
        $this->xmlWriter = null;

        return $output;
    }

    protected function startElement(string $name): void
    {
        $this->xml()->startElement($name);
    }

    protected function endElement(): void
    {
        $this->xml()->endElement();
    }

    protected function writeAttribute(string $name, int|string $value): void
    {
        $this->xml()->writeAttribute($name, (string) $value);
    }

    protected function writeText(string $value): void
    {
        $this->xml()->text($value);
    }

    protected function booleanString(bool $value): string
    {
        return $value ? '1' : '0';
    }

    protected function writeNullableElement(string $name, ?string $value): void
    {
        if ($value === null) {
            $this->startElement($name);
            $this->endElement();

            return;
        }

        $this->xml()->writeElement($name, $value);
    }

    protected function writeScalarElement(string $name, int|string $value): void
    {
        $this->xml()->writeElement($name, (string) $value);
    }

    protected function writeBooleanElement(string $name, bool $value): void
    {
        $this->xml()->writeElement($name, $this->booleanString($value));
    }

    private function xml(): XMLWriter
    {
        if ($this->xmlWriter === null) {
            throw new RuntimeException('XML writer has not been initialized.');
        }

        return $this->xmlWriter;
    }
}
