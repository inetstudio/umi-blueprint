<?php
use UmiCms\Classes\Components\Data\FormSaver;
use UmiCms\Service;

class DataCustom extends def_module {
    /**
     * @var data|DataCustomMacros $module
     */
    public $module;

    /**
     * @var array|string[]
     */
    private static array $extensions = [
        'DataCustomHandlers' => '/handlers.php',
        'DataGuideHelpers'   => '/extensions/guide-helpers.php',
    ];

    /**
     * @noinspection PhpMissingParentConstructorInspection
     * DataCustom constructor.
     * @param data $self
     */
    public function __construct(data $self) {
        foreach (self::$extensions as $class => $path) {
            $self->__loadLib($path, (dirname(__FILE__)));
            $self->__implement($class);
        }
    }

    /**
     * @return array
     * @throws coreException
     * @throws errorPanicException
     * @throws privateException
     */
    public function uploadDocuments(): array {
        $uploadFolder = date('d_m_Y') ."/". uniqid();
        $uploads = Service::Request()->Files()->get('files');

        // upload all inbound files
        for ($i = 0; $i < count($uploads['name']); $i++) {
            $fileData = [
                'name'     => $uploads['name'][$i] ?? null,
                'type'     => $uploads['type'][$i] ?? null,
                'tmp_name' => $uploads['tmp_name'][$i] ?? null,
                'error'    => $uploads['error'][$i] ?? null,
                'size'     => $uploads['size'][$i] ?? null
            ];
            $result = $this->uploadFile($fileData, "./files/uploads/{$uploadFolder}");

            if (isset($result['error'])) {
                return ['success' => false, 'error' => $result['error']];
            }

            $this->createDocumentPage($result['file']);
        }

        return ['success' => true];
    }

    //region Helpers

    /**
     * @param string $data
     * @return array|bool
     * @throws Exception
     */
    public static function processBase64Image(string $data = '') {
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                throw new Exception('invalid image type');
            }

            $data = base64_decode($data);

            if ($data === false) {
                throw new Exception('base64_decode failed');
            }
        } else {
            return false;
        }

        return ['image' => $data, 'type' => $type];
    }

    /**
     * Рекурсивно создает директорию
     *
     * @param string $filePath путь до создаваемой директории
     * @throws Exception если директорию не удалось создать
     */
    public function createDirectory(string $filePath) {
        umiDirectory::requireFolder($filePath, CURRENT_WORKING_DIR);

        $directory = new umiDirectory($filePath);

        if ($directory->getIsBroken()) {
            throw new Exception("Can't create directory: " . $directory->getPath());
        }
    }

    /**
     * Upload files to server
     *
     * @param array  $files
     * @param string $targetFolder
     * @return array
     * @throws Exception
     */
    public function uploadFile(array $files = [], string $targetFolder = "./files/uploads/"): array {
        $umiErrors = [
            0 => "umi-error-file-load",
            1 => "umi-error-all-params",
            2 => "umi-error-file-format",
            3 => "umi-error-file-type",
            5 => "umi-error-file-move"
        ];
        $phpErrors = [
            1 => "php-error-file-size",
            2 => "php-error-file-size",
            3 => "php-error-file-part",
            4 => "php-error-file-upload",
            6 => "php-error-file-temp",
            7 => "php-error-file-write",
            8 => "php-error-file-ext"
        ];

        $this->createDirectory($targetFolder);

        $result = [];
        if (!is_array($files) || empty($files)) {
            $result["error"] = getLabel($umiErrors[0], 'content/ext');

            return $result;
        }
        $name = $files['name'];
        $size = $files['size'];
        $tempPath = $files['tmp_name'];
        $fileError = $files['error'];

        // frontend >> backend upload errors
        if (array_key_exists($fileError, $phpErrors)) {
            switch ($fileError) {
                case 1:
                case 2:
                    $allowedSize = cmsController::getInstance()->getModule('data')->getAllowedMaxFileSize();
                    $formattedError = sprintf(getLabel($phpErrors[$fileError], 'content/ext'), $allowedSize);
                break;
                default:
                    $formattedError = getLabel($phpErrors[$fileError], 'content/ext');
            }
            $result["error"] = $formattedError;
        }

        // backend upload errors
        if ($file = umiFile::manualUpload($name, $tempPath, $size, $targetFolder)) {
            if (is_numeric($file)) {
                switch ($file) {
                    case 4:
                    case 6:
                        $error = getLabel($umiErrors[0], 'content/ext');
                        break;
                    default:
                        $error = getLabel($umiErrors[$file], 'content/ext');
                        break;
                }
                $result["error"] = $error;
            }

            if ($file instanceof umiFile) {
                $result["file"] = $file;
            }
        } else {
            $result["error"] = getLabel($umiErrors[0], 'content/ext');
        }

        return $result;
    }

    /**
     * Load custom config.ini from template folder
     * @return iConfiguration
     */
    public function loadCustomConfig(): iConfiguration {
        // loading custom config file
        $config = mainConfiguration::getInstance();
        try {
            $template = templatesCollection::getInstance()->getDefaultTemplate();
            $config->loadConfig($template->getConfigPath());
            $config->setReadOnlyConfig();
        } catch (Exception $exception) {
            // do nothing
        }

        return $config;
    }

    //endregion

    /**
     * @param umiFile $file
     * @throws coreException
     * @throws errorPanicException
     * @throws privateException
     */
    private function createDocumentPage(umiFile $file) {
        $hierarchyTypes = umiHierarchyTypesCollection::getInstance();
        $hierarchy = umiHierarchy::getInstance();

        $hierarchyType = $hierarchyTypes->getTypeByName('filemanager', 'shared_file');
        if ($hierarchyType instanceof iUmiHierarchyType) {
            $typeId = $hierarchyType->getId();
        } else {
            throw new coreException(getLabel('error-element-type-detect-failed'));
        }

        $name = $file->getFileName();
        $parentId = $this->module->analyzeRequiredPath("/uploads/");
        $elementId = $hierarchy->addElement($parentId, $typeId, $name, $name);

        // make backup for this entity
        backupModel::getInstance()->save($elementId);

        // set default permissions
        $permissions = permissionsCollection::getInstance();
        $permissions->setDefaultPermissions($elementId);

        $element = $hierarchy->getElement($elementId);
        if ($element instanceof iUmiHierarchyElement) {
            $element->setIsActive();
            $element->setIsVisible();
            $element->setValue('h1', $name);
            $element->setValue('fs_file', $file);

            $object = $element->getObject();

            /** @var FormSaver $dataModule */
            $dataModule = cmsController::getInstance()->getModule('data');
            $dataModule->saveEditedObjectWithIgnorePermissions($object->getId(), true, true);

            $object->commit();
            $element->commit();
        }
    }
}