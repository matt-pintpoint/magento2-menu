<?php

namespace Snowdog\Menu\Model\ImportExport;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\ValidatorException;
use Snowdog\Menu\Api\Data\MenuInterface;
use Snowdog\Menu\Model\ImportExport\Processor\ExtendedFields;

class ImportProcessor
{
    /**
     * @var ImportSource
     */
    private $importSource;

    /**
     * @var Processor\Import\Menu
     */
    private $menuProcessor;

    /**
     * @var Processor\Import\Node
     */
    private $nodeProcessor;

    public function __construct(
        ImportSource $importSource,
        Processor\Import\Menu $menuProcessor,
        Processor\Import\Node $nodeProcessor
    ) {
        $this->importSource = $importSource;
        $this->menuProcessor = $menuProcessor;
        $this->nodeProcessor = $nodeProcessor;
    }

    /**
     * @return string
     */
    public function importFile()
    {
        $data = $this->uploadFileAndGetData();
        $menu = $this->createMenu($data);

        if (isset($data[ExtendedFields::NODES])) {
            $this->nodeProcessor->createNodes($data[ExtendedFields::NODES], $menu->getId());
        }

        return $menu->getIdentifier();
    }

    /**
     * @return MenuInterface
     */
    private function createMenu(array $data)
    {
        $stores = $data[ExtendedFields::STORES];

        foreach (ExtendedFields::FIELDS as $extendedField) {
            unset($data[$extendedField]);
        }

        return $this->menuProcessor->createMenu($data, $stores);
    }

    /**
     * @throws ValidatorException
     * @return array
     */
    private function uploadFileAndGetData()
    {
        try {
            $data = $this->importSource->uploadFileAndGetData();
        } catch (LocalizedException $exception) {
            throw new ValidatorException(__($exception->getMessage()));
        }

        $this->menuProcessor->validateImportData($data);

        if (isset($data[ExtendedFields::NODES])) {
            $this->nodeProcessor->validateImportData($data[ExtendedFields::NODES]);
        }

        return $data;
    }
}